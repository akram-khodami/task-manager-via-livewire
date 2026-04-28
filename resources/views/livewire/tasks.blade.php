<div>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-lg border-1 border-light-subtle mb-4">

                    <div class="card-header text-bg-primary d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('messages.task_page_title') }}</h5>

                        <div class="d-flex gap-2">

                            @if($this->isEmbedded)
                                <a href="{{ url('tasks?projectId='.$projectId) }}"
                                   class="btn btn-primary btn-sm"
                                   target="_blank"
                                   title="{{ __('messages.fullscreen') }}">
                                    ⛶
                                </a>
                            @else
                                @if($this->filterByProjectId && $this->backToProjectUrl)
                                    <a href="{{ $this->backToProjectUrl }}"
                                       class="btn btn-primary btn-sm"
                                       wire:navigate>
                                        ← {{ __('messages.back_to_project') }}
                                    </a>
                                @endif

                                @if($this->filterByFolderId && $this->backToFolderUrl)
                                    <a href="{{ $this->backToFolderUrl }}"
                                       class="btn btn-primary btn-sm"
                                       wire:navigate>
                                        ← {{ __('messages.back_to_folder') }}
                                    </a>
                                @endif

                            @endif
                        </div>
                    </div>

                    {{-- Filters --}}
                    <div class="card-body px-0 pb-0 m-1">

                        @if (session()->has('message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <input type="text" wire:model.live.debounce.300ms="search"
                                           class="form-control" placeholder=" 🔍 {{__('messages.search_task')}}">
                                </div>
                                <div class="col-md-2">
                                    <select wire:model.live="statusFilter" class="form-select">
                                        <option value="">{{__('messages.all_status')}}</option>
                                        <option value="todo">{{__('messages.todo')}}</option>
                                        <option value="in_progress">{{__('messages.in_progress')}}</option>
                                        <option value="done">{{__('messages.done')}}</option>
                                        <option value="cancelled">{{__('messages.cancelled')}}</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <select wire:model.live="filterByProjectId" class="form-select">
                                        <option value="">{{__('messages.all_projects')}}</option>
                                        @foreach($this->projects as $project)
                                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select wire:model.live="filterByUserId" class="form-select">
                                        <option value="">{{__('messages.all_users')}}</option>
                                        @foreach($this->allUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex gap-2 align-items-center">
                                    @if($search || $statusFilter || $filterByProjectId || $filterByUserId)
                                        <button wire:click="resetFilters"
                                                class="btn btn-outline-danger btn-sm"
                                                title="{{ __('messages.clear_filters') }}">
                                            ✖ {{ __('messages.filters') }}
                                        </button>
                                    @endif

                                    <button wire:click="create()" class="btn btn-primary btn-sm ms-auto">
                                        ➕ {{ __('messages.new_task') }}
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive m-1">
                                <table class="table align-middle mb-1 table-hover table-bordered">
                                    <thead>
                                    <tr>
                                        <th style="cursor: pointer;" wire:click="sortBy('id')">
                                            # {!! $this->getSortIcon('id') !!}
                                        </th>
                                        <th style="cursor: pointer;" wire:click="sortBy('title')">
                                            {{__('messages.title')}} {!! $this->getSortIcon('title') !!}
                                        </th>
                                        <th style="cursor: pointer;" wire:click="sortBy('project_id')">
                                            {{__('messages.project_name')}} {!! $this->getSortIcon('project_id') !!}
                                        </th>
                                        <th style="cursor: pointer;" wire:click="sortBy('folder_id')">
                                            📁{{__('messages.folder')}} {!! $this->getSortIcon('folder_id') !!}
                                        </th>
                                        <th style="cursor: pointer;" wire:click="sortBy('status')">
                                            {{__('messages.status')}} {!! $this->getSortIcon('status') !!}
                                        </th>
                                        <th style="cursor: pointer;" wire:click="sortBy('assigned_to')">
                                            {{__('messages.assigned')}} {!! $this->getSortIcon('assigned_to') !!}
                                        </th>
                                        <th style="cursor: pointer;" wire:click="sortBy('due_date')">
                                            {{__('messages.due_date')}} {!! $this->getSortIcon('due_date') !!}
                                        </th>
                                        <th style="cursor: pointer;" wire:click="sortBy('estimated_hours')">
                                            {{__('messages.hours')}} {!! $this->getSortIcon('estimated_hours') !!}
                                        </th>
                                        <th>{{__('messages.actions')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($tasks as $index => $task)
                                        <tr>
                                            <td>{{$tasks->firstItem() + $index}}</td>
                                            <td>
                                                <div class="d-flex px-2">
                                                    <div>
                                                        <div class="my-auto">
                                                            <h6 class="mb-0 text-sm">
                                                                <a href="{{ url('tasks/'.$task->id) }}" wire:navigate
                                                                   class="text-decoration-none">
                                                                    {{$task->title}}
                                                                </a>
                                                            </h6>
                                                            <p class="text-xs text-secondary mb-0">{{$task->description}}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{url('projects/'.$task->project_id)}}" wire:navigate>
                                                    {{$task?->project?->name}}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{url('folders?folderId='.$task->folder_id)}}" wire:navigate>
                                                    {{$task->folder ? $task->folder->name:''}}
                                                </a>
                                            </td>
                                            <td>
                                            <span
                                                class="badge bg-{{$task->status_color}}">{{__('messages.'.$task->status)}}</span>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">
                                                    {{ $task->assignedUser?->name ?? __('messages.unassigned') }}
                                                </p>
                                                <p class="text-xs text-secondary mb-0">
                                                    {{__('messages.by')}} {{ $task->creator->name }}
                                                </p>
                                            </td>
                                            <td>
                                            <span class="text-xs font-weight-bold badge bg-black">
                                                {{ $task->due_date?->format('M d') ?? __('messages.no_date') }}
                                            </span>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                <span class="text-xs font-weight-bold">
                                                    {{ $task->estimated_hours }}h / {{ $task->spent_hours }}h
                                                </span>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <div
                                                    class="btn-group btn-group-sm {{ app()->isLocale('fa') ? 'btn-group-reverse' : '' }}"
                                                    role="group">
                                                    <button wire:click="updateStatus({{$task->id}}, 'todo')"
                                                            class="btn btn-outline-warning btn-sm"
                                                            title="{{__('messages.todo')}}"> ▶
                                                    </button>
                                                    <button wire:click="updateStatus({{$task->id}}, 'in_progress')"
                                                            class="btn btn-outline-info btn-sm"
                                                            title="{{__('messages.in_progress')}}"> ⏰
                                                    </button>
                                                    <button wire:click="updateStatus({{$task->id}}, 'done')"
                                                            class="btn btn-outline-success btn-sm"
                                                            title="{{__('messages.done')}}"> ✅
                                                    </button>
                                                    <a href="{{ url('tasks/'.$task->id) }}"
                                                       class="btn btn-outline-secondary btn-sm"
                                                       title="{{__('messages.view')}}"> 👁️
                                                    </a>
                                                    <button wire:click="edit({{$task->id}})"
                                                            class="btn btn-outline-primary btn-sm"
                                                            title="{{__('messages.edit')}}"> ✏
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger"
                                                            wire:click="deleteConfirmed({{ $task->id }})"
                                                            wire:confirm="{{ __('messages.confirm_delete') }}"
                                                            title="{{ __('messages.delete') }}">
                                                        🗑️
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <i class="fas fa-inbox fa-3x text-secondary mb-3"></i>
                                                <p class="text-secondary">{{__('messages.no_tasks')}}</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        {{ $tasks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div wire:ignore.self class="modal fade" id="taskModal" tabindex="-1"
         dir="{{ app()->isLocale('fa') ? 'rtl' : 'ltr' }}">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title {{ app()->isLocale('fa') ? 'ms-auto' : '' }}">
                        {{ $taskId ? __('messages.edit_task') : __('messages.new_task') }}
                    </h5>
                    <button type="button"
                            class="btn-close btn-close-white {{ app()->isLocale('fa') ? 'me-0' : '' }}"
                            wire:click="closeModal"
                            aria-label="Close"></button>
                </div>

                <form wire:submit="save">
                    <div class="modal-body">
                        <div class="row">
                            {{-- Title --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    {{ __('messages.title') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       wire:model="title"
                                       class="form-control @error('title') is-invalid @enderror"
                                       placeholder="{{ __('messages.title') }}">
                                @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label">{{ __('messages.description') }}</label>
                                <textarea wire:model="description"
                                          rows="3"
                                          class="form-control @error('description') is-invalid @enderror"
                                          placeholder="{{ __('messages.description') }}"></textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Project --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    {{ __('messages.project_name') }} <span class="text-danger">*</span>
                                </label>
                                <select wire:model.live="projectId"
                                        {{ $filterByProjectId && !$taskId ? 'disabled' : '' }}
                                        class="form-select @error('projectId') is-invalid @enderror">
                                    <option value="">{{ __('messages.select') }}</option>
                                    @foreach($this->projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                                @error('projectId')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Folder --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('messages.folder_name') }}</label>
                                <select wire:model="folderId"
                                        {{ $filterByFolderId && !$taskId ? 'disabled' : '' }}
                                        class="form-select @error('folderId') is-invalid @enderror">
                                    <option value="">{{ __('messages.select') }}</option>
                                    @foreach($this->folders as $folder)
                                        <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                                    @endforeach
                                </select>
                                @error('folderId')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('messages.status') }}</label>
                                <select wire:model="status"
                                        class="form-select @error('status') is-invalid @enderror">
                                    <option value="todo">{{ __('messages.todo') }}</option>
                                    <option value="in_progress">{{ __('messages.in_progress') }}</option>
                                    <option value="done">{{ __('messages.done') }}</option>
                                    <option value="cancelled">{{ __('messages.cancelled') }}</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Assigned To --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('messages.assigned_to') }}</label>
                                <select wire:model="assigned_to" class="form-select">
                                    <option value="">{{ __('messages.unassigned') }}</option>
                                    @foreach($this->users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Due Date --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('messages.due_date') }}</label>
                                <input type="date"
                                       wire:model="due_date"
                                       class="form-control @error('due_date') is-invalid @enderror"
                                       min="{{ now()->format('Y-m-d') }}">
                                @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Estimated Hours --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('messages.estimated_hours') }}</label>
                                <input type="number"
                                       step="0.5"
                                       min="0"
                                       wire:model="estimated_hours"
                                       class="form-control @error('estimated_hours') is-invalid @enderror">
                                @error('estimated_hours')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Spent Hours --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('messages.spent_hours') }}</label>
                                <input type="number"
                                       step="0.5"
                                       min="0"
                                       wire:model="spent_hours"
                                       class="form-control @error('spent_hours') is-invalid @enderror">
                                @error('spent_hours')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer {{ app()->isLocale('fa') ? 'flex-row-reverse' : '' }}">
                        <button type="button"
                                class="btn btn-secondary"
                                wire:click="closeModal">
                            <i class="fas fa-times me-1"></i>
                            {{ __('messages.cancel') }}
                        </button>

                        <button type="submit"
                                class="btn btn-primary"
                                wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="fas fa-save me-1"></i>
                            {{ $taskId ? __('messages.update') : __('messages.create') }}
                        </span>
                            <span wire:loading>
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            {{ __('messages.saving') }}
                        </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @script
    <script>
        let modalInstance = null;

        $wire.on('open-task-modal', () => {
            if (modalInstance) modalInstance.hide();

            modalInstance = new bootstrap.Modal(document.getElementById('taskModal'), {
                backdrop: 'static',
                keyboard: false
            });
            modalInstance.show();
        });

        $wire.on('close-task-modal', () => {
            if (modalInstance) {
                modalInstance.hide();
                modalInstance = null;
            }
        });

        document.getElementById('taskModal')?.addEventListener('hidden.bs.modal', () => {
            modalInstance = null;
            $wire.resetForm();
        });
    </script>
    @endscript
</div>
