<div>
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-lg border-1 border-light-subtle mb-4">

                    <div class="card-header text-bg-primary">
                        <h5 class="mb-0">{{ __('messages.projects_page') }}</h5>
                    </div>

                    <div class="card-body px-0 pb-0 m-1">

                        @if (session()->has('message'))
                            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                                {{ session('message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="row mb-4 px-3">
                            <div class="col-md-12">
                                <button class="btn btn-sm btn-primary" wire:click="create">
                                    ➕ {{ __('messages.new_project') }}
                                </button>
                            </div>
                        </div>

                        <div class="row g-4 py-1">
                            @forelse($projects as $project)
                                <div class="col-md-6 col-lg-4 col-xl-3 px-4">
                                    <div class="card h-100 shadow-sm border-0 project-card">

                                        <div class="card-header bg-gradient-primary text-white text-center py-4"
                                             wire:click="goToProject({{ $project->id }})"
                                             role="button"
                                             tabindex="0"
                                             style="cursor: pointer;">
                                            <h5 class="card-title mb-0 fw-bold">{{ $project->name }}</h5>
                                        </div>

                                        <div class="card-body" wire:click="goToProject({{ $project->id }})"
                                             role="button"
                                             tabindex="0"
                                             style="cursor: pointer;">

                                            @php
                                                $status = $this->getProjectStatus($project);
                                            @endphp

                                            <div class="mb-3">
                                                <span class="badge {{ $status['class'] }} px-3 py-2">
                                                    {{ $status['text'] }}
                                                </span>
                                            </div>

                                            <div class="project-dates">
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="date-icon me-3">
                                                        <i class="text-primary">🗓️</i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <small class="text-muted d-block">
                                                            {{ __('messages.start_date') }}
                                                        </small>
                                                        <strong>
                                                            {{ $project->start_date->format('Y/m/d') }}
                                                        </strong>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center">
                                                    <div class="date-icon me-3">
                                                        <i class="text-danger">🗓️</i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <small class="text-muted d-block">
                                                            {{ __('messages.end_date') }}
                                                        </small>
                                                        <strong>
                                                            {{ $project->end_date->format('Y/m/d') }}
                                                        </strong>
                                                    </div>
                                                </div>
                                            </div>

                                            @if($status['type'] === 'in_progress')
                                                @php
                                                    $progress = $this->getProjectProgress($project);
                                                @endphp
                                                <div class="mt-3">
                                                    <div class="d-flex justify-content-between small">
                                                        <span>{{ __('messages.project_progress') }}</span>
                                                        <span>{{ $progress }}%</span>
                                                    </div>
                                                    <div class="progress" style="height: 6px;">
                                                        <div class="progress-bar bg-primary"
                                                             role="progressbar"
                                                             style="width: {{ $progress }}%"
                                                             aria-valuenow="{{ $progress }}"
                                                             aria-valuemin="0"
                                                             aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div
                                                    class="btn-group btn-group-sm {{ app()->isLocale('fa') ? 'btn-group-reverse' : '' }}"
                                                    role="group">
                                                    <a class="btn btn-sm btn-outline-primary"
                                                       title="{{ __('messages.folders') }}"
                                                       href="{{ route('folders.index', $project->id) }}">
                                                        📁
                                                    </a>

                                                    <button class="btn btn-sm btn-outline-primary"
                                                            title="{{ __('messages.show') }}"
                                                            wire:click.stop="goToProject({{ $project->id }})">
                                                        👁
                                                    </button>

                                                    <button class="btn btn-sm btn-outline-warning"
                                                            wire:click="edit({{ $project->id }})">
                                                        ✏️
                                                    </button>

                                                    <button class="btn btn-sm btn-outline-danger"
                                                            wire:click="destroy({{ $project->id }})"
                                                            wire:confirm="{{ __('messages.confirm_delete') }}"
                                                            title="{{ __('messages.delete') }}">
                                                        🗑️
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="text-center py-5">
                                        <div class="empty-state">
                                            📥
                                            <h4 class="mt-3 text-muted">{{ __('messages.no_projects') }}</h4>
                                            <p class="text-muted">{{ __('messages.add_project_hint') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <div class="px-3 pb-3">
                            {{ $projects->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="projectModal" tabindex="-1"
         wire:ignore.self
         aria-labelledby="projectModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form wire:submit.prevent="{{ $project_id ? 'update' : 'store' }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="projectModalLabel">
                            {{ $project_id ? __('messages.edit_project') : __('messages.add_project') }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"
                                aria-label="{{ __('messages.close_label') }}"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('messages.project_name') }}</label>
                            <input type="text" class="form-control" wire:model="name" required>
                            @error('name')
                            <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('messages.start_date') }}</label>
                            <input type="date" class="form-control" wire:model="start_date" required>
                            @error('start_date')
                            <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('messages.end_date') }}</label>
                            <input type="date" class="form-control" wire:model="end_date" required>
                            @error('end_date')
                            <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">
                            {{ __('messages.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                {{ $project_id ? __('messages.update') : __('messages.create') }}
                            </span>
                            <span wire:loading>
                                <span class="spinner-border spinner-border-sm"></span>
                                {{ __('messages.loading') }}
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @script
    <script>
        let modalInstance = null;

        $wire.on('open-project-modal', () => {
            if (modalInstance) modalInstance.hide();

            modalInstance = new bootstrap.Modal(document.getElementById('projectModal'), {
                backdrop: 'static',
                keyboard: false
            });
            modalInstance.show();
        });

        $wire.on('close-project-modal', () => {
            if (modalInstance) {
                modalInstance.hide();
                modalInstance = null;
            }
        });

        document.getElementById('projectModal')?.addEventListener('hidden.bs.modal', () => {
            modalInstance = null;
            $wire.resetForm();
        });
    </script>
    @endscript

    <style>
        .project-card {
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        }

        .project-card .card-header {
            border-bottom: none;
        }

        .project-card .card-body,
        .project-card .card-header {
            cursor: pointer;
            user-select: none;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .date-icon {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #e9d2fa;
            border-radius: 50%;
        }

        .empty-state i {
            opacity: 0.3;
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .date-icon {
                background-color: #2d3748;
            }
        }
    </style>
</div>
