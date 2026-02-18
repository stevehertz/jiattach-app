<?php

namespace App\Livewire\Admin\Database;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Livewire\Component;

class Index extends Component
{
    public $tables = [];
    public $selectedTable = '';
    public $tableInfo = [];
    public $tableData = [];
    public $tableColumns = [];
    public $tableStats = [];
    public $searchQuery = '';
    public $searchColumn = '';
    public $searchResults = [];
    public $backupName = '';
    public $importFile = null;
    public $showBackupModal = false;
    public $showImportModal = false;
    public $showQueryModal = false;
    public $sqlQuery = '';
    public $queryResults = [];
    public $queryError = '';
    public $optimizationResults = [];
    public $showOptimizeModal = false;
    public $migrationStatus = [];

     public function mount()
    {
        $this->loadTables();
        $this->loadMigrationStatus();
    }
    
    public function loadTables()
    {
        $tables = DB::select('SHOW TABLES');
        $this->tables = collect($tables)->map(function ($table) {
            $tableName = array_values((array)$table)[0];
            return [
                'name' => $tableName,
                'size' => $this->getTableSize($tableName),
                'rows' => $this->getTableRowCount($tableName),
                'engine' => $this->getTableEngine($tableName),
            ];
        })->sortBy('name')->values()->toArray();
        
        if (!empty($this->tables) && empty($this->selectedTable)) {
            $this->selectTable($this->tables[0]['name']);
        }
    }
    
    public function loadMigrationStatus()
    {
        $migrationsPath = database_path('migrations');
        $migrationFiles = File::files($migrationsPath);
        
        $runMigrations = DB::table('migrations')->pluck('migration')->toArray();
        
        $this->migrationStatus = [
            'total' => count($migrationFiles),
            'run' => count($runMigrations),
            'pending' => count($migrationFiles) - count($runMigrations),
            'files' => collect($migrationFiles)->map(function ($file) use ($runMigrations) {
                $filename = $file->getFilenameWithoutExtension();
                return [
                    'name' => $filename,
                    'status' => in_array($filename, $runMigrations) ? 'run' : 'pending',
                    'date' => date('Y-m-d H:i', $file->getMTime()),
                ];
            })->toArray(),
        ];
    }
    
    public function selectTable($tableName)
    {
        $this->selectedTable = $tableName;
        $this->loadTableInfo($tableName);
        $this->loadTableStructure($tableName);
        $this->loadTableData($tableName);
        $this->loadTableStats($tableName);
        $this->reset(['searchQuery', 'searchColumn', 'searchResults']);
    }
    
    public function loadTableInfo($tableName)
    {
        try {
            $info = DB::select("SHOW TABLE STATUS LIKE ?", [$tableName]);
            if (!empty($info)) {
                $this->tableInfo = (array) $info[0];
            }
        } catch (\Exception $e) {
            $this->tableInfo = ['error' => $e->getMessage()];
        }
    }
    
    public function loadTableStructure($tableName)
    {
        try {
            $columns = DB::select("DESCRIBE {$tableName}");
            $this->tableColumns = collect($columns)->map(function ($column) {
                return [
                    'field' => $column->Field,
                    'type' => $column->Type,
                    'null' => $column->Null,
                    'key' => $column->Key,
                    'default' => $column->Default,
                    'extra' => $column->Extra,
                ];
            })->toArray();
        } catch (\Exception $e) {
            $this->tableColumns = [];
        }
    }
    
    public function loadTableData($tableName, $limit = 50)
    {
        try {
            $this->tableData = DB::table($tableName)
                ->limit($limit)
                ->get()
                ->map(function ($row) {
                    return (array) $row;
                })
                ->toArray();
        } catch (\Exception $e) {
            $this->tableData = [];
        }
    }
    
    public function loadTableStats($tableName)
    {
        try {
            $this->tableStats = [
                'total_rows' => DB::table($tableName)->count(),
                'size_mb' => $this->getTableSize($tableName),
                'indexes' => $this->getTableIndexes($tableName),
                'foreign_keys' => $this->getTableForeignKeys($tableName),
            ];
        } catch (\Exception $e) {
            $this->tableStats = [];
        }
    }
    
    public function searchTable()
    {
        if (empty($this->selectedTable) || empty($this->searchQuery)) {
            return;
        }
        
        try {
            $query = DB::table($this->selectedTable);
            
            if (!empty($this->searchColumn)) {
                $query->where($this->searchColumn, 'like', "%{$this->searchQuery}%");
            } else {
                foreach ($this->tableColumns as $column) {
                    $query->orWhere($column['field'], 'like', "%{$this->searchQuery}%");
                }
            }
            
            $this->searchResults = $query->limit(100)->get()->map(function ($row) {
                return (array) $row;
            })->toArray();
            
        } catch (\Exception $e) {
            $this->searchResults = [];
            $this->dispatchBrowserEvent('notify', [
                'type' => 'error',
                'message' => 'Search error: ' . $e->getMessage()
            ]);
        }
    }
    
    public function runQuery()
    {
        $this->reset(['queryResults', 'queryError']);
        
        if (empty($this->sqlQuery)) {
            $this->dispatchBrowserEvent('notify', [
                'type' => 'warning',
                'message' => 'Please enter a SQL query'
            ]);
            return;
        }
        
        try {
            // Security check - prevent destructive operations without confirmation
            $lowerQuery = strtolower($this->sqlQuery);
            $dangerousKeywords = ['drop', 'truncate', 'delete', 'update', 'alter'];
            
            foreach ($dangerousKeywords as $keyword) {
                if (str_contains($lowerQuery, $keyword . ' ') || 
                    str_contains($lowerQuery, $keyword . "\n") ||
                    str_contains($lowerQuery, $keyword . "\t")) {
                    $this->queryError = "This query contains a potentially dangerous operation ({$keyword}). Please use with caution.";
                    return;
                }
            }
            
            // Run the query
            $results = DB::select($this->sqlQuery);
            
            if (empty($results)) {
                $this->queryResults = ['message' => 'Query executed successfully. No results returned.'];
            } else {
                $this->queryResults = collect($results)->map(function ($row) {
                    return (array) $row;
                })->toArray();
            }
            
            $this->dispatchBrowserEvent('notify', [
                'type' => 'success',
                'message' => 'Query executed successfully'
            ]);
            
        } catch (\Exception $e) {
            $this->queryError = $e->getMessage();
            $this->dispatchBrowserEvent('notify', [
                'type' => 'error',
                'message' => 'Query error: ' . $e->getMessage()
            ]);
        }
    }
    
    public function createBackup()
    {
        try {
            $backupName = $this->backupName ?: 'backup_' . date('Y-m-d_H-i-s');
            $backupPath = storage_path('app/backups/' . $backupName . '.sql');
            
            // Ensure directory exists
            if (!File::exists(storage_path('app/backups'))) {
                File::makeDirectory(storage_path('app/backups'), 0755, true);
            }
            
            // Get database credentials
            $connection = config('database.default');
            $config = config("database.connections.{$connection}");
            
            // Build mysqldump command
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s --port=%s %s > %s',
                $config['username'],
                $config['password'],
                $config['host'],
                $config['port'],
                $config['database'],
                $backupPath
            );
            
            // Execute backup
            exec($command, $output, $returnVar);
            
            if ($returnVar === 0) {
                $this->dispatchBrowserEvent('notify', [
                    'type' => 'success',
                    'message' => "Backup created successfully: {$backupName}.sql"
                ]);
                
                $this->backupName = '';
                $this->showBackupModal = false;
            } else {
                throw new \Exception('Backup command failed');
            }
            
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('notify', [
                'type' => 'error',
                'message' => 'Backup failed: ' . $e->getMessage()
            ]);
        }
    }
    
    public function runOptimization()
    {
        try {
            $this->optimizationResults = [];
            
            // Optimize selected table or all tables
            if ($this->selectedTable) {
                DB::statement("OPTIMIZE TABLE {$this->selectedTable}");
                $this->optimizationResults[] = [
                    'table' => $this->selectedTable,
                    'status' => 'optimized',
                    'message' => 'Table optimized successfully'
                ];
            } else {
                foreach ($this->tables as $table) {
                    DB::statement("OPTIMIZE TABLE {$table['name']}");
                    $this->optimizationResults[] = [
                        'table' => $table['name'],
                        'status' => 'optimized',
                        'message' => 'Table optimized successfully'
                    ];
                }
            }
            
            $this->loadTables(); // Refresh table info
            
            $this->dispatchBrowserEvent('notify', [
                'type' => 'success',
                'message' => 'Database optimization completed successfully'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('notify', [
                'type' => 'error',
                'message' => 'Optimization failed: ' . $e->getMessage()
            ]);
        }
    }
    
    public function runMigration($action = 'migrate')
    {
        try {
            switch ($action) {
                case 'migrate':
                    Artisan::call('migrate', ['--force' => true]);
                    $message = 'Migrations ran successfully';
                    break;
                    
                case 'fresh':
                    Artisan::call('migrate:fresh', ['--force' => true]);
                    $message = 'Database refreshed successfully';
                    break;
                    
                case 'rollback':
                    Artisan::call('migrate:rollback', ['--force' => true]);
                    $message = 'Migration rolled back successfully';
                    break;
                    
                case 'reset':
                    Artisan::call('migrate:reset', ['--force' => true]);
                    $message = 'Database reset successfully';
                    break;
                    
                default:
                    $message = 'Invalid migration action';
            }
            
            $this->loadMigrationStatus();
            $this->loadTables();
            
            $this->dispatchBrowserEvent('notify', [
                'type' => 'success',
                'message' => $message
            ]);
            
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('notify', [
                'type' => 'error',
                'message' => 'Migration failed: ' . $e->getMessage()
            ]);
        }
    }
    
    // Helper Methods
    private function getTableSize($tableName)
    {
        try {
            $result = DB::select("
                SELECT 
                    ROUND((data_length + index_length) / 1024 / 1024, 2) as size_mb
                FROM information_schema.TABLES
                WHERE table_schema = ?
                AND table_name = ?
            ", [config('database.connections.mysql.database'), $tableName]);
            
            return $result[0]->size_mb ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    private function getTableRowCount($tableName)
    {
        try {
            return DB::table($tableName)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    private function getTableEngine($tableName)
    {
        try {
            $result = DB::select("SHOW TABLE STATUS LIKE ?", [$tableName]);
            return $result[0]->Engine ?? 'Unknown';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }
    
    private function getTableIndexes($tableName)
    {
        try {
            $indexes = DB::select("SHOW INDEX FROM {$tableName}");
            return collect($indexes)->groupBy('Key_name')->map(function ($group) {
                return [
                    'name' => $group[0]->Key_name,
                    'type' => $group[0]->Index_type,
                    'unique' => !$group[0]->Non_unique,
                    'columns' => $group->pluck('Column_name')->toArray(),
                ];
            })->values()->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }
    
    private function getTableForeignKeys($tableName)
    {
        try {
            $foreignKeys = DB::select("
                SELECT 
                    CONSTRAINT_NAME,
                    COLUMN_NAME,
                    REFERENCED_TABLE_NAME,
                    REFERENCED_COLUMN_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = ?
                AND TABLE_NAME = ?
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [config('database.connections.mysql.database'), $tableName]);
            
            return $foreignKeys;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function render()
    {
        return view('livewire.admin.database.index');
    }
}
