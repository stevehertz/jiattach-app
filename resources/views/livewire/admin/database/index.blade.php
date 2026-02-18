<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    <div class="content">
        <div class="container-fluid">
            <!-- Control Panel -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs mr-1"></i>
                        Database Tools
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <button wire:click="$set('showBackupModal', true)" class="btn btn-success btn-block">
                                <i class="fas fa-save mr-1"></i> Create Backup
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button wire:click="$set('showOptimizeModal', true)" class="btn btn-warning btn-block">
                                <i class="fas fa-magic mr-1"></i> Optimize Tables
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button wire:click="$set('showQueryModal', true)" class="btn btn-info btn-block">
                                <i class="fas fa-terminal mr-1"></i> Run SQL Query
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="dropdown">
                                <button class="btn btn-primary btn-block dropdown-toggle" type="button"
                                    data-toggle="dropdown">
                                    <i class="fas fa-database mr-1"></i> Migrations
                                </button>
                                <div class="dropdown-menu w-100">
                                    <a class="dropdown-item" href="#" wire:click="runMigration('migrate')">
                                        <i class="fas fa-play mr-2"></i> Run Migrations
                                    </a>
                                    <a class="dropdown-item" href="#" wire:click="runMigration('fresh')">
                                        <i class="fas fa-redo mr-2"></i> Fresh Database
                                    </a>
                                    <a class="dropdown-item" href="#" wire:click="runMigration('rollback')">
                                        <i class="fas fa-undo mr-2"></i> Rollback Last
                                    </a>
                                    <a class="dropdown-item" href="#" wire:click="runMigration('reset')">
                                        <i class="fas fa-trash mr-2"></i> Reset Database
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Migration Status -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="callout callout-info">
                                <h5><i class="fas fa-code-branch mr-1"></i> Migration Status</h5>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="small-box bg-info">
                                            <div class="inner">
                                                <h3>{{ $migrationStatus['total'] ?? 0 }}</h3>
                                                <p>Total Migrations</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-file-code"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="small-box bg-success">
                                            <div class="inner">
                                                <h3>{{ $migrationStatus['run'] ?? 0 }}</h3>
                                                <p>Run Migrations</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="small-box bg-warning">
                                            <div class="inner">
                                                <h3>{{ $migrationStatus['pending'] ?? 0 }}</h3>
                                                <p>Pending Migrations</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="small-box bg-secondary">
                                            <div class="inner">
                                                <h3>{{ count($tables) }}</h3>
                                                <p>Database Tables</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-table"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="row">
                <!-- Tables List -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list mr-1"></i>
                                Database Tables
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Table Name</th>
                                            <th>Rows</th>
                                            <th>Size (MB)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tables as $table)
                                            <tr wire:click="selectTable('{{ $table['name'] }}')"
                                                style="cursor: pointer;"
                                                class="{{ $selectedTable === $table['name'] ? 'table-active' : '' }}">
                                                <td>
                                                    <i class="fas fa-table mr-1"></i>
                                                    {{ $table['name'] }}
                                                    <br>
                                                    <small class="text-muted">{{ $table['engine'] }}</small>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-info">{{ number_format($table['rows']) }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-secondary">{{ $table['size'] }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">
                                <i class="fas fa-info-circle mr-1"></i>
                                Total: {{ count($tables) }} tables
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Table Information -->
                <div class="col-lg-8">
                    @if ($selectedTable)
                        <!-- Table Stats -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Table: {{ $selectedTable }}
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="info-box bg-info">
                                            <span class="info-box-icon"><i class="fas fa-database"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Rows</span>
                                                <span
                                                    class="info-box-number">{{ number_format($tableStats['total_rows'] ?? 0) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box bg-success">
                                            <span class="info-box-icon"><i class="fas fa-weight"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Size</span>
                                                <span class="info-box-number">{{ $tableStats['size_mb'] ?? 0 }}
                                                    MB</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box bg-warning">
                                            <span class="info-box-icon"><i class="fas fa-key"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Indexes</span>
                                                <span
                                                    class="info-box-number">{{ count($tableStats['indexes'] ?? []) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box bg-danger">
                                            <span class="info-box-icon"><i class="fas fa-link"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Foreign Keys</span>
                                                <span
                                                    class="info-box-number">{{ count($tableStats['foreign_keys'] ?? []) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table Structure -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-columns mr-1"></i>
                                    Table Structure
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Field</th>
                                                <th>Type</th>
                                                <th>Null</th>
                                                <th>Key</th>
                                                <th>Default</th>
                                                <th>Extra</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tableColumns as $column)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $column['field'] }}</strong>
                                                    </td>
                                                    <td>
                                                        <code>{{ $column['type'] }}</code>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge badge-{{ $column['null'] === 'YES' ? 'warning' : 'success' }}">
                                                            {{ $column['null'] }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if ($column['key'])
                                                            <span
                                                                class="badge badge-{{ $column['key'] === 'PRI' ? 'danger' : ($column['key'] === 'UNI' ? 'warning' : 'info') }}">
                                                                {{ $column['key'] }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($column['default'] !== null)
                                                            <span
                                                                class="badge badge-light">{{ $column['default'] }}</span>
                                                        @else
                                                            <span class="text-muted">NULL</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($column['extra'])
                                                            <span
                                                                class="badge badge-secondary">{{ $column['extra'] }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Table Data -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-table mr-1"></i>
                                    Table Data (First 50 rows)
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                @if (!empty($tableColumns))
                                                    @foreach (array_slice($tableColumns, 0, 5) as $column)
                                                        <th>{{ $column['field'] }}</th>
                                                    @endforeach
                                                    <th>Actions</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tableData as $row)
                                                <tr>
                                                    @foreach (array_slice($row, 0, 5) as $value)
                                                        <td>
                                                            @if (is_string($value) && strlen($value) > 50)
                                                                {{ Str::limit($value, 50) }}
                                                            @else
                                                                {{ $value }}
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                    <td>
                                                        <button class="btn btn-sm btn-info" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Search Section -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-search mr-1"></i>
                                    Search Table Data
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Search Query</label>
                                            <input type="text" wire:model.defer="searchQuery" class="form-control"
                                                placeholder="Enter search term">
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Search in Column (Optional)</label>
                                            <select wire:model.defer="searchColumn" class="form-control">
                                                <option value="">All Columns</option>
                                                @foreach ($tableColumns as $column)
                                                    <option value="{{ $column['field'] }}">{{ $column['field'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <div class="form-group w-100">
                                            <button wire:click="searchTable" class="btn btn-primary w-100">
                                                <i class="fas fa-search mr-1"></i> Search
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                @if (!empty($searchResults))
                                    <div class="mt-3">
                                        <h6>Search Results ({{ count($searchResults) }} found)</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                    <tr>
                                                        @if (!empty($searchResults[0]))
                                                            @foreach (array_keys($searchResults[0]) as $key)
                                                                <th>{{ $key }}</th>
                                                            @endforeach
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($searchResults as $result)
                                                        <tr>
                                                            @foreach ($result as $value)
                                                                <td>
                                                                    @if (is_string($value) && strlen($value) > 30)
                                                                        {{ Str::limit($value, 30) }}
                                                                    @else
                                                                        {{ $value }}
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-database fa-4x text-muted mb-3"></i>
                                <h5>No Table Selected</h5>
                                <p class="text-muted">Select a table from the list to view its details</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

     <!-- Backup Modal -->
    <div class="modal fade" id="backupModal" tabindex="-1" role="dialog" aria-labelledby="backupModalLabel"
        wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="backupModalLabel">Create Database Backup</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="backupName">Backup Name</label>
                        <input type="text" class="form-control" id="backupName" wire:model="backupName"
                            placeholder="backup_2024_01_01">
                        <small class="form-text text-muted">Leave empty for automatic naming</small>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        The backup will be saved in: <code>storage/app/backups/</code>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" wire:click="createBackup" data-dismiss="modal">
                        <i class="fas fa-save mr-1"></i> Create Backup
                    </button>
                </div>
            </div>
        </div>
    </div>

     <!-- Optimize Modal -->
    <div class="modal fade" id="optimizeModal" tabindex="-1" role="dialog" aria-labelledby="optimizeModalLabel"
        wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="optimizeModalLabel">Optimize Database Tables</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>This will optimize the selected table to reclaim unused space and defragment the data file.</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        The table will be locked during optimization. This may take some time for large tables.
                    </div>
                    @if ($selectedTable)
                        <p><strong>Selected Table:</strong> {{ $selectedTable }}</p>
                    @else
                        <p><strong>Action:</strong> Optimize ALL tables</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" wire:click="runOptimization"
                        data-dismiss="modal">
                        <i class="fas fa-magic mr-1"></i> Optimize Now
                    </button>
                </div>
            </div>
        </div>
    </div>

     <!-- Query Modal -->
    <div class="modal fade" id="queryModal" tabindex="-1" role="dialog" aria-labelledby="queryModalLabel"
        wire:ignore.self>
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="queryModalLabel">Run SQL Query</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="sqlQuery">SQL Query</label>
                        <textarea class="form-control" id="sqlQuery" wire:model="sqlQuery" rows="5"
                            placeholder="SELECT * FROM users WHERE ..."></textarea>
                        <small class="form-text text-muted">
                            Only SELECT queries are allowed for security. Other operations require confirmation.
                        </small>
                    </div>

                    @if ($queryError)
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $queryError }}
                        </div>
                    @endif

                    @if (!empty($queryResults))
                        <div class="mt-3">
                            <h6>Query Results</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            @if (isset($queryResults[0]) && is_array($queryResults[0]))
                                                @foreach (array_keys($queryResults[0]) as $key)
                                                    <th>{{ $key }}</th>
                                                @endforeach
                                            @elseif(isset($queryResults['message']))
                                                <th>Message</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($queryResults[0]) && is_array($queryResults[0]))
                                            @foreach ($queryResults as $result)
                                                <tr>
                                                    @foreach ($result as $value)
                                                        <td>
                                                            @if (is_string($value) && strlen($value) > 50)
                                                                {{ Str::limit($value, 50) }}
                                                            @else
                                                                {{ $value }}
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        @elseif(isset($queryResults['message']))
                                            <tr>
                                                <td>{{ $queryResults['message'] }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="runQuery">
                        <i class="fas fa-play mr-1"></i> Run Query
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Handle modals
            document.addEventListener('livewire:initialized', () => {
                // Backup Modal
                Livewire.on('showBackupModal', (show) => {
                    if (show) {
                        $('#backupModal').modal('show');
                    } else {
                        $('#backupModal').modal('hide');
                    }
                });

                // Optimize Modal
                Livewire.on('showOptimizeModal', (show) => {
                    if (show) {
                        $('#optimizeModal').modal('show');
                    } else {
                        $('#optimizeModal').modal('hide');
                    }
                });

                // Query Modal
                Livewire.on('showQueryModal', (show) => {
                    if (show) {
                        $('#queryModal').modal('show');
                    } else {
                        $('#queryModal').modal('hide');
                    }
                });

                // Listen for modal close
                $('#backupModal, #optimizeModal, #queryModal').on('hidden.bs.modal', function() {
                    @this.set('showBackupModal', false);
                    @this.set('showOptimizeModal', false);
                    @this.set('showQueryModal', false);
                });

                // Listen for notify events
                Livewire.on('notify', (event) => {
                    toastr[event.type](event.message);
                });
            });
        </script>
    @endpush

</div>
