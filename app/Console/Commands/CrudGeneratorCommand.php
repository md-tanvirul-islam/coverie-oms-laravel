<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CrudGeneratorCommand extends Command
{
    protected $signature = 'crud:generate {name}';
    protected $description = 'Generate CRUD based on JSON structure';

    public function handle()
    {
        $name = Str::studly($this->argument('name')); // e.g. Order
        $variable = Str::camel($name);               // e.g. order
        $plural = Str::snake(Str::plural($name));            // e.g. orders
        // $title = Str::title($name);
        // $pluralTitle = Str::title(Str::plural($name));

        $jsonPath = base_path("crud/{$plural}.json");

        if (!File::exists($jsonPath)) {
            $this->error("JSON file not found at {$jsonPath}");
            return 1;
        }

        $config = json_decode(File::get($jsonPath), true);

        // 1. Generate Model
        $this->generateModel($name, $config);

        // 2. Generate Migration
        $this->generateMigration($name, $config);

        // 3. Generate Service
        $this->generateService($name, $config);

        // 4. Generate Requests
        $this->generateRequests($name, $config);

        // 5. Generate Controller
        $this->generateController($name, $config);

        // 6. Generate DataTable
        $this->generateDataTable($name, $config);

        // 7. Generate Import/Export
        if (!empty($config['excel_import'])) {
            $this->generateImport($name, $config);
        }
        if (!empty($config['excel_export'])) {
            $this->generateExport($name, $config);
        }

        // 8. Generate Views
        $this->generateViews($name, $config);

        // 9. Append Routes
        $this->appendRoutes($name, $config);

        $this->info("CRUD for {$name} generated successfully!");
    }

    protected function generateModel($name, $config)
    {
        $modelPath = app_path("Models/{$name}.php");
        if (File::exists($modelPath)) {
            $this->warn("Model {$name} already exists. Skipping.");
            return;
        }

        // Generate fillable fields
        $fillableCode = '';
        foreach ($config['fields'] as $field => $props) {
            $fillableCode .= "        '{$field}',\n";
        }
        $fillableCode = rtrim($fillableCode, "\n"); // remove trailing newline

        // Generate relationships
        $relationsCode = '';
        if (!empty($config['relations'])) {
            foreach ($config['relations'] as $rel_name => $rel) {
                $relationName = is_string($rel_name) ? $rel_name : Str::camel(str_replace('_id', '', $rel['foreign'] ?? ''));
                $foreign = isset($rel['foreign']) ? ", '{$rel['foreign']}'" : '';
                $relationsCode .= "    public function {$relationName}()\n";
                $relationsCode .= "    {\n";
                $relationsCode .= "        return \$this->{$rel['type']}({$rel['model']}::class{$foreign});\n";
                $relationsCode .= "    }\n\n";
            }
            $relationsCode = rtrim($relationsCode, "\n"); // remove trailing newline
        }

        // Load stub
        $stub = File::get(base_path('stubs/model.stub'));

        // Replace placeholders
        $stub = str_replace(
            ['{{ model }}', '{{ fillable }}', '{{ relations }}'],
            [$name, $fillableCode, $relationsCode],
            $stub
        );

        // Save model file
        File::put($modelPath, $stub);
        $this->info("Model created: {$modelPath}");
    }

    protected function generateMigration($name, $config)
    {
        $table = $config['table'] ?? Str::snake(Str::plural($name));
        $migrationName = 'create_' . $table . '_table';
        $timestamp = date('Y_m_d_His');
        $migrationPath = database_path("migrations/{$timestamp}_{$migrationName}.php");

        // Generate fields
        $fieldsCode = '';
        foreach ($config['fields'] as $field => $props) {
            $type = $props['type'];
            $line = "            \$table";

            switch ($type) {
                case 'string':
                    $length = $props['length'] ?? 255;
                    $line .= "->string('{$field}', {$length})";
                    break;
                case 'text':
                    $line .= "->text('{$field}')";
                    break;
                case 'integer':
                    $line .= "->integer('{$field}')";
                    break;
                case 'unsignedBigInteger':
                    $line .= "->unsignedBigInteger('{$field}')";
                    break;
                case 'decimal':
                    $precision = $props['precision'] ?? 10;
                    $scale = $props['scale'] ?? 2;
                    $line .= "->decimal('{$field}', {$precision}, {$scale})";
                    break;
                case 'boolean':
                    $line .= "->boolean('{$field}')";
                    break;
                case 'date':
                    $line .= "->date('{$field}')";
                    break;
                default:
                    $line .= "->{$type}('{$field}')";
            }

            if (!empty($props['nullable'])) {
                $line .= "->nullable()";
            }
            if (!empty($props['unique'])) {
                $line .= "->unique()";
            }
            if (isset($props['default'])) {
                $default = is_string($props['default']) ? "'{$props['default']}'" : $props['default'];
                $line .= "->default({$default})";
            }

            $fieldsCode .= $line . ";\n";
        }

        // Generate foreign key relations
        $relationsCode = '';
        if (!empty($config['relations'])) {
            foreach ($config['relations'] as $rel) {
                $onDelete = $rel['onDelete'] ?? 'cascade';
                $relationsCode .= "            \$table->foreignId('{$rel['foreign']}')->constrained('{$rel['references']}')->onDelete('{$onDelete}');\n";
            }
        }

        // Load stub and replace placeholders
        $stub = File::get(base_path('stubs/migration.stub'));
        $stub = str_replace(
            ['{{ table }}', '{{ fields }}', '{{ relations }}'],
            [$table, $fieldsCode, $relationsCode],
            $stub
        );

        // Save migration
        File::put($migrationPath, $stub);
        $this->info("Migration created: {$migrationPath}");
    }

    protected function generateService($name, $config)
    {
        $servicePath = app_path("Services/{$name}Service.php");
        $stub = File::get(base_path('stubs/service.stub'));
        $stub = str_replace(
            ['{{ model }}'],
            [$name],
            $stub
        );

        File::put($servicePath, $stub);
        $this->info("Service created: {$servicePath}");
    }

    protected function generateRequests($name, $config)
    {
        $storeDir = app_path("Http/Requests/{$name}");
        $storePath = "{$storeDir}/Store{$name}Request.php";
        $updatePath = "{$storeDir}/Update{$name}Request.php";

        File::ensureDirectoryExists($storeDir);

        // Generate validation rules from JSON fields
        $rulesCode = '';
        foreach ($config['fields'] as $field => $props) {
            $rulesCode .= "            '{$field}' => '{$props['validation']}',\n";
        }

        // Load stubs
        $storeStub = File::get(base_path('stubs/request.store.stub'));
        $updateStub = File::get(base_path('stubs/request.update.stub'));

        // Replace placeholders in StoreRequest
        $storeStub = str_replace(
            ['{{ model }}', '{{ rules }}'],
            [$name, rtrim($rulesCode)],
            $storeStub
        );

        // Replace placeholders in UpdateRequest
        $updateStub = str_replace(
            ['{{ model }}', '{{ rules }}', '{{ model_variable }}'],
            [$name, rtrim($rulesCode), Str::camel($name)],
            $updateStub
        );

        // Save files
        File::put($storePath, $storeStub);
        File::put($updatePath, $updateStub);

        $this->info("Requests created: {$storePath}, {$updatePath}");
    }

    protected function generateController($name, $config)
    {
        $controllerPath = app_path("Http/Controllers/{$name}Controller.php");
        $stub = File::get(base_path('stubs/controller.stub'));
        $route_prefix = $this->routePrefix($name);
        $stub = str_replace(
            ['{{ model }}', '{{ service }}', '{{ request_namespace }}', '{{ route_prefix }}'],
            [$name, $name . 'Service', "App\\Http\\Requests\\{$name}", $route_prefix],
            $stub
        );

        File::put($controllerPath, $stub);
        $this->info("Controller created: {$controllerPath}");
    }

    protected function generateDataTable($name, $config)
    {
        $dataTablePath = app_path("DataTables/{$name}sDataTable.php");
        $stub = File::get(base_path('stubs/datatable.stub'));

        // Replace {{ model }} and {{ models }}
        $stub = str_replace(
            ['{{ model }}', '{{ models }}'],
            [$name, Str::plural(Str::camel($name))],
            $stub
        );

        // Generate dynamic columns from JSON fields
        $columnsCode = '';
        foreach ($config['fields'] as $field => $props) {
            $columnsCode .= "Column::make('{$field}'),\n            ";
        }

        // Add computed action column at the end
        $columnsCode .= "Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),";

        // Replace the @foreach placeholder with actual column code
        $stub = preg_replace('/@foreach\(\$fields as \$field => \$config\).*?@endforeach/s', $columnsCode, $stub);

        // Save the generated DataTable file
        File::put($dataTablePath, $stub);
        $this->info("DataTable created: {$dataTablePath}");
    }

    protected function generateExport(string $name, array $config)
    {
        $exportPath = app_path("Exports/{$name}sExport.php");
        $stub = File::get(base_path('stubs/export.stub'));

        $fieldsList = implode(",\n            ", array_map(fn($f) => "'{$f}'", array_keys($config['fields'])));
        $headingsList = implode(",\n            ", array_map(fn($f) => "'" . strtolower($f) . "'", array_keys($config['fields'])));

        $stub = str_replace(
            ['{{ model }}', '{{ fields_list }}', '{{ headings_list }}'],
            [$name, $fieldsList, $headingsList],
            $stub
        );

        File::put($exportPath, $stub);
        $this->info("Export class created: {$exportPath}");
    }

    protected function generateImport(string $name, array $config)
    {
        $importPath = app_path("Imports/{$name}Import.php");
        $stub = File::get(base_path('stubs/import.stub'));

        $fieldsMapping = implode(",\n            ", array_map(fn($f) => "'{$f}' => \$row['" . strtolower($f) . "']", array_keys($config['fields'])));
        $validationRules = implode(",\n            ", array_map(fn($f, $props) => "'" . strtolower($f) . "' => '{$props['validation']}'", array_keys($config['fields']), $config['fields']));

        $stub = str_replace(
            ['{{ model }}', '{{ fields_mapping }}', '{{ validation_rules }}'],
            [$name, $fieldsMapping, $validationRules],
            $stub
        );

        File::put($importPath, $stub);
        $this->info("Import class created: {$importPath}");
    }


    protected function appendRoutes($name, $config)
    {
        $routeFile = base_path('routes/web.php');
        $route_prefix = $this->routePrefix($name);
        $controller = $name . 'Controller';

        $routes = [];
        if (!empty($config['excel_import'])) {
            $routes[] = "Route::get('{$route_prefix}/import', [{$controller}::class, 'import'])->name('{$route_prefix}.import');";
            $routes[] = "Route::post('{$route_prefix}/import', [{$controller}::class, 'importStore'])->name('{$route_prefix}.import.store');";
        }
        if (!empty($config['excel_export'])) {
            $routes[] = "Route::get('{$route_prefix}/export', [{$controller}::class, 'export'])->name('{$route_prefix}.export');";
        }
        $routes[] = "Route::resource('{$route_prefix}', {$controller}::class);";

        File::append($routeFile, "\n" . implode("\n", $routes) . "\n");
        $this->info("Routes appended to web.php");
    }

    private function modelSingularTitle(string $modelName): string
    {
        $words = preg_split('/(?=[A-Z])/', $modelName, -1, PREG_SPLIT_NO_EMPTY);
        return implode(' ', $words);
    }

    private function modelPluralTitle(string $modelName): string
    {
        $words = preg_split('/(?=[A-Z])/', $modelName, -1, PREG_SPLIT_NO_EMPTY);
        $pluralWords = array_merge(array_slice($words, 0, -1), [Str::plural(end($words))]);
        return implode(' ', $pluralWords);
    }

    private function routePrefix(string $modelName): string
    {
        return strtolower(str_replace(" ", "_", $this->modelPluralTitle($modelName)));
    }

    protected function generateViews($name, $config)
    {
        $route_prefix = $this->routePrefix($name);
        $viewsDir = resource_path("views/{$route_prefix}");
        File::ensureDirectoryExists($viewsDir);

        $stubs = ['index', 'create', 'edit', 'action', 'import'];
        foreach ($stubs as $stubName) {
            $stubPath = base_path("stubs/views/{$stubName}.stub");
            $filePath = "{$viewsDir}/{$stubName}.blade.php";

            $stubContent = File::get($stubPath);

            $stubContent = $this->replaceViewPlaceholders($stubContent, $name, $config, $route_prefix);

            File::put($filePath, $stubContent);
        }

        $this->info("Views created: {$viewsDir}");
    }

    /**
     * Replace placeholders in a blade stub
     */
    private function replaceViewPlaceholders(string $stubContent, string $name, array $config, string $route_prefix): string
    {
        $variable = Str::camel($name);
        return str_replace(
            [
                '{{ model }}',
                '{{ models }}',
                '{{ model_title }}',
                '{{ models_title }}',
                '{{route_prefix}}',
                '{{ variable }}',
                '{{ excel_buttons }}',
                '{{ fields_inputs }}',
                '{{ fields_inputs_edit }}',
                '{{ import_columns }}'
            ],
            [
                $name,
                Str::plural($variable),
                $this->modelSingularTitle($name),
                $this->modelPluralTitle($name),
                $route_prefix,
                $variable,
                $this->generateExcelButtons($route_prefix, $config),
                $this->generateFieldsInputs($config),
                $this->generateFieldsInputsEdit($config, $variable),
                $this->generateImportColumns($config)
            ],
            $stubContent
        );
    }

    /**
     * Generate Excel import/export buttons
     */
    private function generateExcelButtons(string $route_prefix, array $config): string
    {
        $buttons = '';
        if (!empty($config['excel_import'])) {
            $buttons .= "<a href=\"{{ route('{$route_prefix}.import') }}\" class=\"btn btn-primary\">Import Excel</a>\n";
        }
        if (!empty($config['excel_export'])) {
            $buttons .= "<a href=\"{{ route('{$route_prefix}.export') }}\" class=\"btn btn-success\">Export Excel</a>\n";
        }
        return $buttons;
    }

    /**
     * Generate input fields for create view
     */
    private function generateFieldsInputs(array $config): string
    {
        $html = '';
        foreach ($config['fields'] as $field => $props) {
            $html .= $this->generateInputField($field, $props, false);
        }
        return $html;
    }

    /**
     * Generate input fields for edit view
     */
    private function generateFieldsInputsEdit(array $config, string $variable): string
    {
        $html = '';
        foreach ($config['fields'] as $field => $props) {
            $html .= $this->generateInputField($field, $props, true, $variable);
        }
        return $html;
    }

    /**
     * Generate a single input field (for create or edit)
     */
    private function generateInputField(string $field, array $props, bool $isEdit = false, string $variable = ''): string
    {
        $label = Str::title(str_replace('_', ' ', $field));
        $type = $props['type'];

        $inputType = match ($type) {
            'text' => 'textarea',
            'integer', 'decimal' => 'number',
            'boolean' => 'checkbox',
            'date' => 'date',
            default => 'text'
        };

        $oldValue = $isEdit ? "{{ \${$variable}->{$field} }}" : "{{ old('{$field}') }}";

        if ($inputType === 'textarea') {
            return "<div class=\"mb-3\">
                        <label class=\"form-label\">{$label}</label>
                        <textarea name=\"{$field}\" class=\"form-control\" rows=\"3\">{$oldValue}</textarea>
                    </div>\n";
        }

        if ($inputType === 'checkbox') {
            $checked = $isEdit ? "{{ \${$variable}->{$field} ? 'checked' : '' }}" : "{{ old('{$field}') ? 'checked' : '' }}";
            return "<div class=\"form-check mb-3\">
                        <input type=\"checkbox\" name=\"{$field}\" class=\"form-check-input\" value=\"1\" {$checked}>
                        <label class=\"form-check-label\">{$label}</label>
                    </div>\n";
        }

        return "<div class=\"mb-3\">
                    <label class=\"form-label\">{$label}</label>
                    <input type=\"{$inputType}\" name=\"{$field}\" class=\"form-control\" value=\"{$oldValue}\">
                </div>\n";
    }

    /**
     * Generate import columns list for import view
     */
    private function generateImportColumns(array $config): string
    {
        $html = '';
        foreach ($config['fields'] as $field => $props) {
            $html .= "<li>{$field}</li>\n";
        }
        return $html;
    }
}
