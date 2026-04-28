<div>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-lg border-1 border-light-subtle mb-4">

                    <div class="card-header text-bg-primary">
                        <h5 class="mb-0">{{ __('messages.task_page_title') }}</h5>
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
                                <div class="col-md-4">
                                    <input type="text" wire:model.live.debounce.300ms="search"
                                           class="form-control" placeholder=" 🔍 {{__('messages.search_task')}}">
                                </div>
                                <div class="col-md-4">
                                    <select wire:model.live="statusFilter" class="form-select">
                                        <option value="">{{__('messages.all_status')}}</option>
                                        <option value="todo">{{__('messages.todo')}}</option>
                                        <option value="in_progress">{{__('messages.in_progress')}}</option>
                                        <option value="done">{{__('messages.done')}}</option>
                                        <option value="cancelled">{{__('messages.cancelled')}}</option>
                                    </select>
                                </div>
                                <div class="d-lg-flex">
                                    <div class="col-md-4 d-flex justify-content-between align-items-center">
                                        <button wire:click="create()" class="btn btn-primary btn-sm">
                                            ➕ {{ __('messages.new_task') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive m-1">
                                <table class="table align-middle mb-1 table-hover table-bordered">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{__('messages.title')}}</th>
                                        <th>{{__('messages.project_name')}}</th>
                                        <th>📁{{__('messages.folder')}}</th>
                                        <th>{{__('messages.status')}}</th>
                                        <th>{{__('messages.assigned')}}</th>
                                        <th>{{__('messages.due_date')}}</th>
                                        <th>{{__('messages.hours')}}</th>
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
                                                            <h6 class="mb-0 text-sm">{{$task->title}}</h6>
                                                            <p class="text-xs text-secondary mb-0">{{$task->description}}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{url('projects/'.$task->project_id)}}" wire:navigate>
                                                    {{$task->project->name}}
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
                                                <div class="btn-group btn-group-sm" role="group">
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
                                                    <button wire:click="edit({{$task->id}})"
                                                            class="btn btn-outline-primary btn-sm"
                                                            title="{{__('messages.edit')}}"> ✏
                                                    </button>
                                                    <button wire:click.prevent="promptDelete({{ $task->id }})"
                                                            class="btn btn-outline-danger btn-sm"
                                                            title="{{ __('messages.delete') }}"> 🗑️
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
    <div wire:ignore.self class="modal fade" id="taskModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        {{ $taskId ? __('messages.edit_task') : __('messages.new_task') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
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
                                <select wire:model="projectId"
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

                    <div class="modal-footer">
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

        $wire.on('confirm-delete-task', ({id}) => {
            if (confirm('{{ __("messages.confirm_delete") }}')) {
                $wire.deleteConfirmed(id);
            }
        });
    </script>
    @endscript
</div>

