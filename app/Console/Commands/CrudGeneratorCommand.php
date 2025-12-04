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

        $fillable = array_keys($config['fields']);
        $fillableStr = implode("',\n        '", $fillable);

        $relationsCode = '';
        if (!empty($config['relations'])) {
            foreach ($config['relations'] as $field => $rel) {
                $relName = Str::camel(str_replace('_id', '', $field));
                $relationsCode .= "\n    public function {$relName}()\n    {\n        return \$this->{$rel['type']}({$rel['model']}::class);\n    }\n";
            }
        }

        $stub = File::get(base_path('stubs/model.stub'));
        $stub = str_replace(
            ['{{ model }}', '{{ fillable }}', '{{ relations }}'],
            [$name, $fillableStr, $relationsCode],
            $stub
        );

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
            switch ($type) {
                case 'string':
                    $fieldsCode .= "\$table->string('{$field}')";
                    break;
                case 'text':
                    $fieldsCode .= "\$table->text('{$field}')";
                    break;
                case 'integer':
                    $fieldsCode .= "\$table->integer('{$field}')";
                    break;
                case 'decimal':
                    $fieldsCode .= "\$table->decimal('{$field}', 10, 2)";
                    break;
                case 'date':
                    $fieldsCode .= "\$table->date('{$field}')";
                    break;
                case 'foreign':
                    $fieldsCode .= "\$table->foreignId('{$field}')->constrained()->onDelete('cascade')";
                    break;
                default:
                    $fieldsCode .= "\$table->{$type}('{$field}')";
            }
            $fieldsCode .= ";\n            ";
        }

        $stub = File::get(base_path('stubs/migration.stub'));
        $stub = str_replace(
            ['{{ table }}', '{{ fields }}'],
            [$table, $fieldsCode],
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
        $storePath = app_path("Http/Requests/{$name}/Store{$name}Request.php");
        $updatePath = app_path("Http/Requests/{$name}/Update{$name}Request.php");

        File::ensureDirectoryExists(dirname($storePath));

        $rulesArr = [];
        foreach ($config['fields'] as $field => $props) {
            $rulesArr[] = "'{$field}' => '{$props['validation']}'";
        }
        $rules = implode(",\n            ", $rulesArr);

        $storeStub = str_replace(['{{ rules }}'], [$rules], File::get(base_path('stubs/request.store.stub')));
        $updateStub = str_replace(['{{ rules }}', '{{ model_variable }}'], [$rules, Str::camel($name)], File::get(base_path('stubs/request.update.stub')));

        File::put($storePath, $storeStub);
        File::put($updatePath, $updateStub);
        $this->info("Requests created: {$storePath}, {$updatePath}");
    }

    protected function generateController($name, $config)
    {
        $controllerPath = app_path("Http/Controllers/{$name}Controller.php");
        $stub = File::get(base_path('stubs/controller.stub'));
        $stub = str_replace(['{{ model }}', '{{ service }}', '{{ request_namespace }}'], [$name, $name.'Service', "App\\Http\\Requests\\{$name}"], $stub);

        File::put($controllerPath, $stub);
        $this->info("Controller created: {$controllerPath}");
    }

    protected function generateDataTable($name, $config)
    {
        $dataTablePath = app_path("DataTables/{$name}sDataTable.php");
        $stub = File::get(base_path('stubs/datatable.stub'));
        $stub = str_replace(['{{ model }}'], [$name], $stub);

        File::put($dataTablePath, $stub);
        $this->info("DataTable created: {$dataTablePath}");
    }

    protected function generateImport($name, $config)
    {
        $importPath = app_path("Imports/{$name}Import.php");
        $stub = File::get(base_path('stubs/import.stub'));
        $stub = str_replace(['{{ model }}'], [$name], $stub);
        File::put($importPath, $stub);
        $this->info("Import class created: {$importPath}");
    }

    protected function generateExport($name, $config)
    {
        $exportPath = app_path("Exports/{$name}sExport.php");
        $stub = File::get(base_path('stubs/export.stub'));
        $stub = str_replace(['{{ model }}'], [$name], $stub);
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
        $controller = $name.'Controller';

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
