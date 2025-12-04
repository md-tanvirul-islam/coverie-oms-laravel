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

        $fieldsCode = '';
        foreach ($config['fields'] as $field => $props) {
            $type = $props['type'];

            // Start building the field line
            $line = "\$table";

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

            // Add nullable, unique, and default options
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

            $fieldsCode .= $line . ";\n            ";
        }

        // Handle foreign key relations if any
        $relationsCode = '';
        if (!empty($config['relations'])) {
            foreach ($config['relations'] as $rel) {
                $onDelete = $rel['onDelete'] ?? 'cascade';
                $relationsCode .= "\$table->foreignId('{$rel['foreign']}')->constrained('{$rel['references']}')->onDelete('{$onDelete}');\n            ";
            }
        }

        $stub = File::get(base_path('stubs/migration.stub'));
        $stub = str_replace(
            ['{{ table }}', '{{ fields }}', '{{ relations }}'],
            [$table, $fieldsCode, $relationsCode],
            $stub
        );

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
        $stub = str_replace(['{{ model }}', '{{ service }}', '{{ request_namespace }}'], [$name, $name . 'Service', "App\\Http\\Requests\\{$name}"], $stub);

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


    protected function generateImport($name, $config)
    {
        $importPath = app_path("Imports/{$name}Import.php");
        $stub = File::get(base_path('stubs/import.stub'));

        // Replace {{ model }}
        $stub = str_replace('{{ model }}', $name, $stub);

        // Generate dynamic fields for model array
        $fieldsModel = '';
        foreach ($config['fields'] as $field => $props) {
            $fieldsModel .= "'{$field}' => \$row['" . strtoupper($field) . "'],\n            ";
        }
        $stub = preg_replace('/@foreach\(\$fields as \$field => \$config\).*?@endforeach/s', $fieldsModel, $stub);

        // Generate dynamic validation rules
        $fieldsRules = '';
        foreach ($config['fields'] as $field => $props) {
            $validation = $props['validation'] ?? '';
            $fieldsRules .= "'" . strtoupper($field) . "' => '{$validation}',\n            ";
        }
        $stub = preg_replace('/return\s*\[\s*.*?@endforeach\s*\];/s', "return [\n            {$fieldsRules}        ];", $stub);

        File::put($importPath, $stub);
        $this->info("Import class created: {$importPath}");
    }

    protected function generateExport($name, $config)
    {
        $exportPath = app_path("Exports/{$name}sExport.php");
        $stub = File::get(base_path('stubs/export.stub'));

        // Replace {{ model }}
        $stub = str_replace('{{ model }}', $name, $stub);

        // Generate dynamic columns for collection
        $fieldsCollection = '';
        foreach ($config['fields'] as $field => $props) {
            $fieldsCollection .= "'{$field}',\n            ";
        }
        $stub = preg_replace('/@foreach\(\$fields as \$field => \$config\).*?@endforeach/s', $fieldsCollection, $stub);

        // Generate dynamic headings
        $fieldsHeadings = '';
        foreach ($config['fields'] as $field => $props) {
            $fieldsHeadings .= "'" . strtoupper($field) . "',\n            ";
        }
        $stub = preg_replace('/headings\(\): array\s*\{\s*return\s*\[\s*.*?@endforeach\s*\];/s', "headings(): array {\n        return [\n            {$fieldsHeadings}        ];", $stub);

        File::put($exportPath, $stub);
        $this->info("Export class created: {$exportPath}");
    }

    protected function generateViews($name, $config)
    {
        $viewsDir = resource_path("views/{$name}s");
        File::ensureDirectoryExists($viewsDir);

        $stubs = ['index', 'create', 'edit', 'action', 'import'];
        foreach ($stubs as $stubName) {
            $stubPath = base_path("stubs/views/{$stubName}.stub");
            $filePath = "{$viewsDir}/{$stubName}.blade.php";

            $stubContent = File::get($stubPath);
            // Here you can replace dynamic placeholders like {{ model }}, {{ models }}, {{ fields_inputs }} etc.
            $stubContent = str_replace(
                ['{{ model }}', '{{ models }}', '{{ model_title }}', '{{ models_title }}', '{{ variable }}', '{{ excel_import }}', '{{ excel_export }}'],
                [$name, Str::plural(Str::camel($name)), Str::title($name), Str::title(Str::plural($name)), Str::camel($name), $config['excel_import'] ?? false, $config['excel_export'] ?? false],
                $stubContent
            );

            File::put($filePath, $stubContent);
        }

        $this->info("Views created: {$viewsDir}");
    }

    protected function appendRoutes($name, $config)
    {
        $routeFile = base_path('routes/web.php');
        $plural = Str::plural(Str::camel($name));
        $controller = $name . 'Controller';

        $routes = [];
        if (!empty($config['excel_import'])) {
            $routes[] = "Route::get('{$plural}/import', [{$controller}::class, 'import'])->name('{$plural}.import');";
            $routes[] = "Route::post('{$plural}/import', [{$controller}::class, 'importStore'])->name('{$plural}.import.store');";
        }
        if (!empty($config['excel_export'])) {
            $routes[] = "Route::get('{$plural}/export', [{$controller}::class, 'export'])->name('{$plural}.export');";
        }
        $routes[] = "Route::resource('{$plural}', {$controller}::class);";

        File::append($routeFile, "\n" . implode("\n", $routes) . "\n");
        $this->info("Routes appended to web.php");
    }
}
