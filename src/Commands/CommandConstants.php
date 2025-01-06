<?php

declare(strict_types=1);

namespace Lrc\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CommandConstants extends Command
{
    protected $signature = 'make:constants {model?} {--fillable} {--delete}';

    protected $description = 'Generate column constants for a given model based on its table schema. Use --delete to remove constants.';

    public function handle()
    {
        $modelName = $this->argument('model');
        $isFillable = $this->option('fillable');
        $isDelete = $this->option('delete');

        if ($isDelete) {
            if ($modelName === 'all') {
                $this->deleteAllModelsConstants();
            } elseif ($modelName) {
                $this->deleteConstants($modelName);
            } else {
                $this->error('Please specify a model or use "all" to delete constants for all models.');
            }
        } else {
            if ($modelName === 'all') {
                $this->processAllModels($isFillable);
            } elseif ($modelName) {
                $this->processModel($modelName, $isFillable);
            } else {
                $this->error('Please specify a model or use "all" to generate constants for all models.');
            }
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

            try {
                $this->processModel($modelName, $isFillable);
            } catch (\Exception $e) {
                $this->error("Error processing model {$modelName}: " . $e->getMessage());
                continue; // Skip to the next model
            }
        }

        $this->info('Column constants and TABLE_NAME generated for all models.');
    }

    private function processModel(string $modelName, bool $isFillable)
    {
        $modelClass = "App\\Models\\{$modelName}";

        if (!class_exists($modelClass)) {
            throw new \Exception("Model {$modelName} does not exist.");
        }

        $model = new $modelClass;
        $table = $model->getTable();

        if (!Schema::hasTable($table)) {
            throw new \Exception("Table {$table} does not exist for model {$modelName}.");
        }

        $columns = Schema::getColumnListing($table);

        if (empty($columns)) {
            throw new \Exception("No columns found for table {$table}.");
        }

        $constants = " \n\n   public const TABLE_NAME = '{$table}';\n\n";
        foreach ($columns as $column) {
            $constantName = strtoupper('COL_' . $column);
            $constants .= "    public const {$constantName} = '{$column}';\n";
        }

        $fillable = '';
        if ($isFillable) {
            $fillableColumns = array_map(function ($column) {
                return "\n self::COL_" . strtoupper($column);
            }, $columns);
            $fillable = "    public \$fillable = [" . implode(', ', $fillableColumns) . "];\n\n";
        }

        $modelPath = app_path("Models/{$modelName}.php");
        $content = file_get_contents($modelPath);

        $classPosition = strpos($content, 'class ' . $modelName);
        if ($classPosition === false) {
            throw new \Exception("Could not find the class {$modelName} in the file.");
        }

        $bracePosition = strpos($content, '{', $classPosition);
        if ($bracePosition === false) {
            throw new \Exception("Could not find the opening brace for the class {$modelName}.");
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

    private function deleteAllModelsConstants()
    {
        $modelDirectory = app_path('Models');
        $modelFiles = scandir($modelDirectory);

        $modelFiles = array_filter($modelFiles, function ($file) {
            return Str::endsWith($file, '.php') && $file !== 'Model.php';
        });

        foreach ($modelFiles as $file) {
            $modelName = pathinfo($file, PATHINFO_FILENAME);

            try {
                $this->deleteConstants($modelName);
            } catch (\Exception $e) {
                $this->error("Error deleting constants for model {$modelName}: " . $e->getMessage());
                continue; // Skip to the next model
            }
        }

        $this->info('Column constants and TABLE_NAME deleted for all models.');
    }

    private function deleteConstants(string $modelName)
    {
        $modelClass = "App\\Models\\{$modelName}";

        if (!class_exists($modelClass)) {
            throw new \Exception("Model {$modelName} does not exist.");
        }

        $modelPath = app_path("Models/{$modelName}.php");
        $content = file_get_contents($modelPath);

        // Remove TABLE_NAME constant
        $content = preg_replace("/\s*public\s+const\s+TABLE_NAME\s*=\s*'.*?';\n/", "", $content);

        // Remove COL_ constants
        $content = preg_replace("/\s*public\s+const\s+COL_.*?;\n/", "", $content);

        file_put_contents($modelPath, $content);

        $this->info("Column constants and TABLE_NAME deleted successfully for {$modelName}.");
    }
}
