<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CrudGeneratorCommand extends Command
{
    protected $signature = 'crud:generate {file_name}';
    protected $description = 'Generate CRUD based on JSON structure. E.g. php artisan crud:generate file_name (store.json)';

    public function handle()
    {
        $file_name = $this->argument('file_name');

        $jsonPath = base_path("crud/ref/{$file_name}");

        if (!File::exists($jsonPath)) {
            $this->error("JSON file not found at {$jsonPath}");
            return 1;
        }

        $config = json_decode(File::get($jsonPath), true);

        $model = $config['model'] ?? null;

        if (!$model) {
            $this->error("model key is not defined in JSON file.");
            return 1;
        }

        $table = $config['table'] ?? null;

        if (!$table) {
            $this->error("table key is not defined in JSON file.");
            return 1;
        }

        $route_prefix = $config['route_prefix'] ?? null;

        if (!$route_prefix) {
            $this->error("route_prefix key is not defined in JSON file.");
            return 1;
        }

        //input confirmation
        $this->info("Generating CRUD for Model: {$model}, Table: {$table}, Route Prefix: {$route_prefix}");
        if (!$this->confirm('Do you wish to continue?')) {
            $this->info('Command cancelled.');
            return 0;
        }

        // dd($config);

        // 1. Generate Model
        $this->generateModel($config);

        // 2. Generate Migration
        $this->generateMigration($config);

        // 3. Generate Service
        $this->generateService($config);

        // 4. Generate Requests
        $this->generateRequests($config);

        // 5. Generate Controller
        $this->generateController($config);

        // 6. Generate DataTable
        $this->generateDataTable($config);

        // 7. Generate Import/Export
        if (!empty($config['excel_import'])) {
            $this->generateImport($config);
        }
        if (!empty($config['excel_export'])) {
            $this->generateExport($config);
        }

        // 8. Generate Views
        $this->generateViews($config);

        // 9. Append Routes
        $this->appendRoutes($config);

        $this->info("CRUD for {$model} Model generated successfully!");
    }

    protected function generateModel($config)
    {
        $model_name = $config['model'];

        $modelPath = app_path("Models/{$model_name}.php");
        if (File::exists($modelPath)) {
            $this->warn("Model {$model_name} already exists. Skipping.");
            return;
        }

        // Generate fillable fields
        $fillable_fields = '';
        foreach ($config['fields'] as $field => $props) {
            $fillable_fields .= "        '{$field}',\n";
        }
        $fillable_fields = rtrim($fillable_fields, "\n"); // remove trailing newline

        // Generate relationships
        $model_relations = '';
        if (!empty($config['relations'])) {
            foreach ($config['relations'] as $rel_name => $rel) {
                $relationName = is_string($rel_name) ? $rel_name : Str::camel(str_replace('_id', '', $rel['foreign'] ?? ''));
                $foreign = isset($rel['foreign']) ? ", '{$rel['foreign']}'" : '';
                $model_relations .= "    public function {$relationName}()\n";
                $model_relations .= "    {\n";
                $model_relations .= "        return \$this->{$rel['type']}({$rel['model']}::class{$foreign});\n";
                $model_relations .= "    }\n\n";
            }
            $model_relations = rtrim($model_relations, "\n"); // remove trailing newline
        }

        // Load stub
        $stub = File::get(base_path('crud/stubs/model.stub'));

        // Replace placeholders
        $stub = str_replace(
            ['{{ modelName }}', '{{ fillableFields }}', '{{ modelRelations }}'],
            [$model_name, $fillable_fields, $model_relations],
            $stub
        );

        // Save model file
        File::put($modelPath, $stub);
        $this->info("Model created: {$modelPath}");
    }

    protected function generateMigration($config)
    {
        $table = $config['table'];
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
        // if (!empty($config['relations'])) {
        //     foreach ($config['relations'] as $rel) {
        //         $onDelete = $rel['onDelete'] ?? 'cascade';
        //         $relationsCode .= "            \$table->foreignId('{$rel['foreign']}')->constrained('{$rel['references']}')->onDelete('{$onDelete}');\n";
        //     }
        // }

        // Load stub and replace placeholders
        $stub = File::get(base_path('crud/stubs/migration.stub'));
        $stub = str_replace(
            ['{{ table }}', '{{ fields }}', '{{ relations }}'],
            [$table, $fieldsCode, $relationsCode],
            $stub
        );

        // Save migration
        File::put($migrationPath, $stub);
        $this->info("Migration created: {$migrationPath}");
    }

    protected function generateService($config)
    {
        $model_name = $config['model'];
        $table_name = $config['table'];
        $servicePath = app_path("Services/{$model_name}Service.php");

        //create filer options
        $filters = '';
        foreach ($config['fields'] as $field => $props) {
            if ($props['type'] === 'text') {
                continue; // skip text fields for filtering
            }
            if ($props['type'] === 'boolean') {
                $filters .= "        if(array_key_exists('{$field}', \$data)) {\n";
                $filters .= "            \$query->where('{$field}', \$data['{$field}']);\n";
                $filters .= "        }\n\n";
            } else {
                $filters .= "        if(isset(\$data['{$field}'])) {\n";
                $filters .= "            \$query->where('{$field}', \$data['{$field}']);\n";
                $filters .= "        }\n\n";
            }
        }
        $filters = rtrim($filters, "\n"); // remove trailing newline

        $stub = File::get(base_path('crud/stubs/service.stub'));
        $stub = str_replace(
            ['{{ model }}',  '{{ table }}', '{{ filters }}'],
            [$model_name, $table_name, $filters],
            $stub
        );

        File::put($servicePath, $stub);
        $this->info("Service created: {$servicePath}");
    }

    protected function generateRequests($config)
    {
        $model_name = $config['model'];
        $route_prefix = $config['route_prefix'];
        $route_prefix_singular = Str::singular($config['route_prefix']);

        $storeDir = app_path("Http/Requests/{$model_name}");
        $storePath = "{$storeDir}/Store{$model_name}Request.php";
        $updatePath = "{$storeDir}/Update{$model_name}Request.php";
        $filterPath = "{$storeDir}/Filter{$model_name}Request.php";

        File::ensureDirectoryExists($storeDir);

        // Generate validation rules from JSON fields
        $rulesCode = '';
        foreach ($config['fields'] as $field => $props) {
            $rulesCode .= "            '{$field}' => '{$props['validation']}',\n";
        }

        // Replace placeholders in StoreRequest
        $storeStub = File::get(base_path('crud/stubs/request.store.stub'));
        $storeStub = str_replace(
            ['{{ model }}', '{{ rules }}'],
            [$model_name, rtrim($rulesCode)],
            $storeStub
        );

        // Save Create Request files
        File::put($storePath, $storeStub);

        // Generate validation rules from JSON fields
        $rulesCode = '';
        foreach ($config['fields'] as $field => $props) {
            $rules = explode('|', $props['validation'] ?? '');
            $has_unique = false;
            $rules = array_map(function ($r) use ($route_prefix_singular, &$has_unique) {
                if (str_contains($r, 'unique')) {
                    $has_unique = true;
                    return $r . ",'. \$this->{$route_prefix_singular}->id";
                }

                return $r;
            }, $rules);

            $props['validation'] = implode('|', $rules);

            if ($has_unique) {
                $rulesCode .= "            '{$field}' => '{$props['validation']},\n";
            } else {
                $rulesCode .= "            '{$field}' => '{$props['validation']}',\n";
            }
        }

        // Replace placeholders in UpdateRequest
        $updateStub = File::get(base_path('crud/stubs/request.update.stub'));
        $updateStub = str_replace(
            ['{{ model }}', '{{ rules }}'],
            [$model_name, rtrim($rulesCode)],
            $updateStub
        );

        // Save Update Request files
        File::put($updatePath, $updateStub);

        // Generate validation rules from JSON fields for Filter Request
        $rulesCode = '';
        foreach ($config['fields'] as $field => $props) {
            $rules = explode('|', $props['validation'] ?? '');

            $rules = array_filter($rules, function ($r) {
                return in_array($r, ['string', 'integer', 'boolean']);
            });

            $rules[] = 'nullable';

            $props['validation'] = implode('|', $rules);

            $rulesCode .= "            '{$field}' => '{$props['validation']}',\n";
        }

        // Replace placeholders in StoreRequest
        $filterStub = File::get(base_path('crud/stubs/request.filter.stub'));
        $filterStub = str_replace(
            ['{{ model }}', '{{ rules }}'],
            [$model_name, rtrim($rulesCode)],
            $filterStub
        );

        // Save Create Request files
        File::put($filterPath, $filterStub);

        $this->info("Requests created: {$storePath}, {$updatePath}, {$filterPath}");
    }

    protected function generateController($config)
    {
        $model_name = $config['model'];
        $route_prefix = $config['route_prefix'];

        $controllerPath = app_path("Http/Controllers/{$model_name}Controller.php");
        $stub = File::get(base_path("crud/stubs/controller.stub"));

        $createdBy = isset($config['fields']['created_by'])
            ? "\$data['created_by'] = Auth::id();"
            : "";

        $updatedBy = isset($config['fields']['updated_by'])
            ? "\$data['updated_by'] = Auth::id();"
            : "";

        $variable = Str::singular($route_prefix);

        $stub = str_replace(
            [
                '{{ model }}',
                '{{ models }}',
                '{{ variable }}',
                '{{ route_prefix }}',
                '{{ message_model }}',
                '{{ created_by }}',
                '{{ updated_by }}'
            ],
            [
                $model_name,
                Str::plural($model_name),
                $variable,
                $route_prefix,
                $this->modelSingularTitle($model_name),
                $createdBy,
                $updatedBy
            ],
            $stub
        );

        File::put($controllerPath, $stub);
        $this->info("Controller created: {$controllerPath}");
    }

    protected function generateDataTable($config)
    {
        $model_name = $config['model'];
        $route_prefix = $config['route_prefix'];
        $model_plural = Str::plural($model_name);

        $dataTablePath = app_path("DataTables/{$model_plural}DataTable.php");
        $stub = File::get(base_path('crud/stubs/datatable.stub'));

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

        $stub = str_replace(['{{ model }}', '{{ model_plural }}', '{{ route_prefix }}', '{{ tableColumns }}'], [$model_name, $model_plural, $route_prefix, $columnsCode], $stub);

        // Save the generated DataTable file
        File::put($dataTablePath, $stub);
        $this->info("DataTable created: {$dataTablePath}");
    }

    protected function generateExport(array $config)
    {
        $model_name = $config['model'];
        $model_plural = Str::plural($model_name);

        $exportPath = app_path("Exports/{$model_plural}Export.php");
        $stub = File::get(base_path('crud/stubs/export.stub'));

        $fieldsList = implode(",\n            ", array_map(fn($f) => "'{$f}'", array_keys($config['fields'])));
        $headingsList = implode(",\n            ", array_map(fn($f) => "'" . strtolower($f) . "'", array_keys($config['fields'])));

        $stub = str_replace(
            ['{{ model }}', '{{ model_plural }}', '{{ fields_list }}', '{{ headings_list }}'],
            [$model_name, $model_plural, $fieldsList, $headingsList],
            $stub
        );

        File::put($exportPath, $stub);
        $this->info("Export class created: {$exportPath}");
    }

    protected function generateImport(array $config)
    {
        $model_name = $config['model'];
        $model_plural = Str::plural($model_name);

        $importPath = app_path("Imports/{$model_plural}Import.php");
        $stub = File::get(base_path('crud/stubs/import.stub'));

        $fieldsMapping = implode(",\n            ", array_map(fn($f) => "'{$f}' => \$row['" . strtolower($f) . "']", array_keys($config['fields'])));
        $validationRules = implode(",\n            ", array_map(fn($f, $props) => "'" . strtolower($f) . "' => '{$props['validation']}'", array_keys($config['fields']), $config['fields']));

        $stub = str_replace(
            ['{{ model }}', '{{ model_plural }}', '{{ fields_mapping }}', '{{ validation_rules }}'],
            [$model_name, $model_plural, $fieldsMapping, $validationRules],
            $stub
        );

        File::put($importPath, $stub);
        $this->info("Import class created: {$importPath}");
    }

    protected function appendRoutes($config)
    {
        $routeFile = base_path('routes/web.php');
        $model_name = $config['model'];
        $route_prefix = $config['route_prefix'];
        $controller = $model_name . 'Controller';

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

    protected function generateViews($config)
    {
        $model_name = $config['model'];
        $route_prefix = $config['route_prefix'];

        $viewsDir = resource_path("views/{$route_prefix}");
        File::ensureDirectoryExists($viewsDir);

        $stubs = ['index', 'create', 'edit', 'action'];

        if (!empty($config['excel_import'])) {
            $stubs[] = 'import';
        }

        foreach ($stubs as $stubName) {
            $stubPath = base_path("crud/stubs/views/{$stubName}.stub");
            $filePath = "{$viewsDir}/{$stubName}.blade.php";

            $stubContent = File::get($stubPath);

            $stubContent = $this->replaceViewPlaceholders($stubContent, $model_name, $config, $route_prefix);

            File::put($filePath, $stubContent);
        }

        $this->info("Views created: {$viewsDir}");
    }

    /**
     * Replace placeholders in a blade stub
     */
    private function replaceViewPlaceholders(string $stubContent, string $name, array $config, string $route_prefix): string
    {
        $variable = Str::singular($route_prefix);
        return str_replace(
            [
                '{{ model }}',
                '{{ models }}',
                '{{ model_title }}',
                '{{ models_title }}',
                '{{ route_prefix }}',
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
            $buttons .= "<a href=\"{{ route('{$route_prefix}.import') }}\" class=\"btn btn-primary\"><i class=\"bi bi-upload\"></i> Import Excel</a>\n";
        }
        if (!empty($config['excel_export'])) {
            $buttons .= "<a href=\"{{ route('{$route_prefix}.export') }}\" class=\"btn btn-success\"><i class=\"bi bi-download\"></i> Export Excel</a>\n";
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
