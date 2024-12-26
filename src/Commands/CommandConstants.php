<?php

declare(strict_types=1);

namespace Lrc\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CommandConstants extends Command
{
    protected $signature = 'make:constants {model?} {--fillable}';

    protected $description = 'Generate column constants for a given model based on its table schema';

    public function handle()
    {
        $modelName = $this->argument('model');
        $isFillable = $this->option('fillable');

        if ($modelName === 'all') {
            $this->processAllModels($isFillable);
        } elseif ($modelName) {
            $this->processModel($modelName, $isFillable);
        } else {
            $this->error('Please specify a model or use "all" to generate constants for all models.');
        }
    }

    private function processAllModels(bool $isFillable)
    {
        $modelDirectory = app_path('Models');
        $modelFiles = scandir($modelDirectory);

        $modelFiles = array_filter($modelFiles, function ($file) {
            return Str::endsWith($file, '.php') && $file !== 'Model.php';
        });

        foreach ($modelFiles as $file) {
            $modelName = pathinfo($file, PATHINFO_FILENAME);
            $this->processModel($modelName, $isFillable);
        }

        $this->info('Column constants and TABLE_NAME generated for all models.');
    }

    private function processModel(string $modelName, bool $isFillable)
    {
        $modelClass = "App\\Models\\{$modelName}";

        if (!class_exists($modelClass)) {
            $this->error("Model {$modelName} does not exist.");
            return;
        }

        $model = new $modelClass;
        $table = $model->getTable();
        $columns = Schema::getColumnListing($table);

        $constants = "    public const TABLE_NAME = '{$table}';\n\n";
        foreach ($columns as $column) {
            $constantName = strtoupper('COL_' . $column);
            $constants .= "    public const {$constantName} = '{$column}';\n";
        }

        $fillable = '';
        if ($isFillable) {
            $fillableColumns = array_map(function ($column) {
                return strtoupper('COL_' . $column);
            }, $columns);
            $fillable = "    public \$fillable = [" . implode(', ', $fillableColumns) . "];\n\n";
        }

        $modelPath = app_path("Models/{$modelName}.php");
        $content = file_get_contents($modelPath);

        $classPosition = strpos($content, 'class ' . $modelName);
        if ($classPosition === false) {
            $this->error("Could not find the class {$modelName} in the file.");
            return;
        }

        $bracePosition = strpos($content, '{', $classPosition);
        if ($bracePosition === false) {
            $this->error("Could not find the opening brace for the class {$modelName}.");
            return;
        }

        // Check if constants or fillable already exist
        if (strpos($content, 'public const TABLE_NAME') !== false && strpos($content, 'public const COL_') !== false) {
            $this->info("Constants already exist in {$modelName}. Skipping insertion.");
            return;
        }

        // Insert constants and fillable properties
        $content = substr_replace($content, $constants . $fillable, $bracePosition + 1, 0);
        file_put_contents($modelPath, $content);

        $this->info("Column constants and TABLE_NAME generated successfully for {$modelName}.");
    }
}
