<div>
    <div class="container-fluid py-4">
        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                ✅ {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                ⚠ {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
    @endif

    <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0 bg-gradient-primary text-black">
                    <div class="card-body py-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <h3 class="mb-2">
                                    📂 {{ __('messages.folders_management') }}
                                </h3>

                                <!-- Breadcrumb Navigation -->
                                @if($filterByProjectId)
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb mb-0">
                                            <li class="breadcrumb-item">
                                                <a href="#" wire:click.prevent="navigateToBreadcrumb('root')"
                                                   class="text-white text-decoration-none opacity-75 hover-opacity-100">
                                                    🏠 {{ __('messages.root') }}
                                                </a>
                                            </li>
                                            @foreach($breadcrumbs as $index => $crumb)
                                                <li class="breadcrumb-item">
                                                    <a href="#" wire:click.prevent="navigateToBreadcrumb({{ $index }})"
                                                       class="text-white text-decoration-none opacity-75 hover-opacity-100">
                                                        {{ $crumb['name'] }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ol>
                                    </nav>
                                @endif
                            </div>

                            <div class="mt-3 mt-md-0">
                                <button class="btn btn-light" wire:click="create">
                                    ➕ 🗂 {{ __('messages.new_folder') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Stats Bar -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0">
                                    🔍
                                </span>
                                    <input type="text"
                                           class="form-control border-start-0"
                                           wire:model.live.debounce.300ms="search"
                                           placeholder="{{ __('messages.search_folders') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <select class="form-select" wire:model.live="filterByProjectId">
                                    <option value="">{{ __('messages.all_projects') }}</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-around">
                            <div class="text-center">
                                <h5 class="mb-0 text-primary">{{ $stats['total'] }}</h5>
                                <small class="text-muted">{{ __('messages.total_folders') }}</small>
                            </div>
                            <div class="text-center">
                                <h5 class="mb-0 text-success">{{ $stats['root'] }}</h5>
                                <small class="text-muted">{{ __('messages.root_folders') }}</small>
                            </div>
                            <div class="text-center">
                                <h5 class="mb-0 text-info">{{ $stats['current'] }}</h5>
                                <small class="text-muted">{{ __('messages.current_level') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Toggle -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                @if($currentFolderId)
                    📁
                    {{ $breadcrumbs[count($breadcrumbs)-1]['name'] ?? __('messages.current_folder') }}
                @else
                    📂 {{ __('messages.all_folders') }}
                @endif
            </h5>
            <div
                class="btn-group btn-group-sm {{ app()->isLocale('fa') ? 'btn-group-reverse' : '' }}"
                role="group">
                <button class="btn btn-outline-secondary {{ $viewMode === 'grid' ? 'active' : '' }}"
                        wire:click="$set('viewMode', 'grid')">
                    📄 ↕
                </button>
                <button class="btn btn-outline-secondary {{ $viewMode === 'list' ? 'active' : '' }}"
                        wire:click="$set('viewMode', 'list')">
                    📄 ↔
                </button>
            </div>
        </div>

        <!-- Folders Display -->
    @if($folders->count() > 0)
        @if($viewMode === 'grid')
            <!-- Grid View -->
                <div class="row g-4">
                    @foreach($folders as $folder)
                        <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                            <div class="folder-card h-100">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-body text-center p-3">
                                        <div class="folder-icon mb-3"
                                             wire:click="enterFolder({{ $folder->id }})"
                                             style="cursor: pointer;">
                                            <div class="folder-visual">
                                                @if($folder->children_count > 0 || $folder->tasks_count > 0)
                                                    <i class="text-warning display-3"> 📁 </i>
                                                @else
                                                    <i class="text-warning display-3">📁</i>
                                                @endif
                                            </div>
                                        </div>

                                        <h6 class="folder-name mb-2"
                                            wire:click="enterFolder({{ $folder->id }})"
                                            style="cursor: pointer;">
                                            {{ $folder->name }}
                                        </h6>

                                        <div class="folder-stats mb-3">
                                        <span class="badge bg-light text-dark me-1">
                                            🗂
                                            {{ $folder->children_count }}
                                        </span>
                                            <span class="badge bg-light text-dark">
                                             ✅{{ $folder->tasks_count }}
                                        </span>
                                        </div>

                                        <div class="folder-actions">
                                            <div
                                                class="btn-group btn-group-sm {{ app()->isLocale('fa') ? 'btn-group-reverse' : '' }} w-100"
                                                role="group">
                                                <button class="btn btn-outline-primary"
                                                        wire:click="enterFolder({{ $folder->id }})"
                                                        title="{{ __('messages.open') }}">
                                                    ➡️
                                                </button>
                                                <button class="btn btn-outline-success"
                                                        wire:click="goToTasks({{ $folder->id }})"
                                                        title="{{ __('messages.view_tasks') }}">
                                                    📄
                                                </button>
                                                <button class="btn btn-outline-warning"
                                                        wire:click="edit({{ $folder->id }})"
                                                        title="{{ __('messages.edit') }}">
                                                    ✏
                                                </button>
                                                <button class="btn btn-outline-danger"
                                                        wire:click="deleteConfirmed({{ $folder->id }})"
                                                        wire:confirm="{{ __('messages.confirm_delete') }}"
                                                        title="{{ __('messages.delete') }}">
                                                    🗑️
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
        @else
            <!-- List View -->
                <div class="card shadow-sm border-0">
                    <div class="list-group list-group-flush">
                        @foreach($folders as $folder)
                            <div class="list-group-item list-group-item-action">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div wire:click="enterFolder({{ $folder->id }})" style="cursor: pointer;">
                                            @if($folder->children_count > 0 || $folder->tasks_count > 0)
                                                <i class="text-warning fs-2"
                                                   title="{{ __('messages.subfolders') }}">📁</i>
                                            @else
                                                <i class="text-warning fs-2" title="{{ __('messages.subfolders') }}">
                                                    📁 </i>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col">
                                        <h6 class="mb-1"
                                            wire:click="enterFolder({{ $folder->id }})"
                                            style="cursor: pointer;">
                                            {{ $folder->name }}
                                        </h6>
                                        <div class="d-flex gap-3">
                                            <small class="text-muted" title="{{ __('messages.subfolders') }}">
                                                🗂 {{ $folder->children_count }} {{ __('messages.subfolders') }}
                                            </small>
                                            <small class="text-muted" title="{{ __('messages.tasks') }}">
                                                ✅ {{ $folder->tasks_count }} {{ __('messages.tasks') }}
                                            </small>
                                            <small class="text-muted">
                                                🗓️ {{ $folder->created_at->format('Y/m/d') }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div
                                            class="btn-group btn-group-sm {{ app()->isLocale('fa') ? 'btn-group-reverse' : '' }}"
                                            role="group">
                                            <button class="btn btn-sm btn-outline-primary"
                                                    wire:click="enterFolder({{ $folder->id }})"
                                                    title="{{ __('messages.open') }}">
                                                ➡️
                                            </button>
                                            <button class="btn btn-sm btn-outline-success"
                                                    wire:click="goToTasks({{ $folder->id }})"
                                                    title="{{ __('messages.view_tasks') }}">
                                                📝
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning"
                                                    wire:click="edit({{ $folder->id }})"
                                                    title="{{ __('messages.edit') }}">
                                                ✏
                                            </button>
                                            <button class="btn btn-outline-danger"
                                                    wire:click="deleteConfirmed({{ $folder->id }})"
                                                    wire:confirm="{{ __('messages.confirm_delete') }}"
                                                    title="{{ __('messages.delete') }}">
                                                🗑️
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
        @endif

        <!-- Pagination -->
            <div class="mt-4">
                {{ $folders->links() }}
            </div>
    @else
        <!-- Empty State -->
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-5">
                    <div class="empty-state-icon mb-4">
                        <i class="display-1 text-muted"> 📂 </i>
                    </div>
                    <h4 class="text-muted">{{ __('messages.no_folders_found') }}</h4>
                    <p class="text-muted mb-4">{{ __('messages.create_first_folder') }}</p>
                    <button class="btn btn-primary" wire:click="create">
                        ➕ 🗂 {{ __('messages.create_folder') }}
                    </button>
                </div>
            </div>
    @endif

    <!-- Create/Edit Folder Modal -->
        <div class="modal fade" id="folderModal" tabindex="-1" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <form wire:submit.prevent="save">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title">
                                🗂 {{ $folderId ? __('messages.edit_folder') : __('messages.create_folder') }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.folder_name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       wire:model="name"
                                       placeholder="{{ __('messages.enter_folder_name') }}">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.project_name') }} <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('projectId') is-invalid @enderror"
                                        wire:model="projectId"
                                        @if($filterByProjectId) disabled @endif>
                                    <option value="">{{ __('messages.select') }}</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                                @error('projectId')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if(!$folderId || $parentId)
                                <div class="mb-3">
                                    <label class="form-label">{{ __('messages.parent_folder') }}</label>
                                    <input type="text"
                                           class="form-control"
                                           value="{{ $currentFolderId ? ($breadcrumbs[count($breadcrumbs)-1]['name'] ?? '') : __('messages.root_folder') }}"
                                           disabled>
                                    <small class="text-muted">{{ __('messages.folder_will_be_created_here') }}</small>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ __('messages.cancel') }}
                            </button>
                            <button type="submit"
                                    class="btn btn-primary">{{ $folderId ? __('messages.update') : __('messages.create') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Folder Card Styles */
        .folder-card {
            transition: all 0.3s ease;
        }

        .folder-card:hover {
            transform: translateY(-5px);
        }

        .folder-card .card {
            border-radius: 12px;
            transition: box-shadow 0.3s ease;
        }

        .folder-card:hover .card {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        }

        .folder-visual {
            position: relative;
            display: inline-block;
            transition: transform 0.2s ease;
        }

        .folder-card:hover .folder-visual {
            transform: scale(1.05);
        }

        .folder-name {
            color: #333;
            font-weight: 500;
            transition: color 0.2s ease;
            word-break: break-word;
        }

        .folder-card:hover .folder-name {
            color: #667eea;
        }

        /* Gradient Background */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        /* Breadcrumb Styles */
        .breadcrumb-item + .breadcrumb-item::before {
            color: rgba(255, 255, 255, 0.7);
        }

        .hover-opacity-100:hover {
            opacity: 1 !important;
        }

        /* Empty State */
        .empty-state-icon {
            opacity: 0.3;
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            .folder-name {
                color: #e1e1e1;
            }

            .folder-card:hover .folder-name {
                color: #8b9fff;
            }

            .bg-light {
                background-color: #2d3748 !important;
            }

            .list-group-item {
                background-color: #1a202c;
                border-color: #2d3748;
            }

            .text-muted {
                color: #a0aec0 !important;
            }
        }
    </style>

    @script
    <script>
        let modalInstance = null;

        $wire.on('open-folder-modal', () => {
            if (modalInstance) modalInstance.hide();

            modalInstance = new bootstrap.Modal(document.getElementById('folderModal'), {
                backdrop: 'static',
                keyboard: false
            });
            modalInstance.show();
        });

        $wire.on('close-folder-modal', () => {
            if (modalInstance) {
                modalInstance.hide();
                modalInstance = null;
            }
        });

        document.getElementById('folderModal')?.addEventListener('hidden.bs.modal', () => {
            modalInstance = null;
            $wire.resetForm();
        });
    </script>
    @endscript

</div>
