<?php

namespace App\Livewire;

use App\Models\Folder;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Tasks extends Component
{
    use WithPagination;

    // fields of form
    public ?int $taskId = null;
    public string $title = '';
    public string $description = '';
    public ?int $projectId = null;
    public ?int $folderId = null;
    public string $status = 'todo';
    public ?string $due_date = null;
    public float $estimated_hours = 0;
    public float $spent_hours = 0;
    public ?int $assigned_to = null;

    //filters
    public string $search = '';
    public string $statusFilter = '';
    public ?int $filterByProjectId = null;
    public ?int $filterByFolderId = null;
    public ?int $filterByUserId = null;

    // sort
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    protected array $sortableFields = [
        'id',
        'title',
        'project_id',
        'folder_id',
        'status',
        'assigned_to',
        'due_date',
        'estimated_hours',
        'spent_hours',
        'created_at'
    ];

    protected function rules(): array
    {
        return [
            'title' => 'required|min:3|max:255',
            'description' => 'nullable|string|max:2000',
            'projectId' => 'required|numeric|exists:projects,id',
            'folderId' => 'nullable|exists:folders,id',
            'status' => 'required|in:todo,in_progress,done,cancelled',
            'due_date' => 'nullable|date|after:now',
            'estimated_hours' => 'nullable|numeric|min:0|max:999',
            'spent_hours' => 'nullable|numeric|min:0|max:999',
            'assigned_to' => 'nullable|exists:users,id',
        ];
    }

    public function mount(?int $filterByProjectId = null, ?int $filterByFolderId = null): void
    {
        $this->filterByProjectId = $filterByProjectId ?? ($_GET['projectId'] ?? null);
        $this->filterByFolderId = $filterByFolderId ?? ($_GET['folderId'] ?? null);
        $this->projectId = $filterByProjectId;
        $this->folderId = $filterByFolderId;
        $this->assigned_to = auth()->id();
    }

    public function updated($property): void
    {
        $this->validateOnly($property);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedFilterByUserId(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if (!in_array($field, $this->sortableFields)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getSortIcon(string $field): string
    {
        if ($this->sortField !== $field) {
            return '↕️';
        }

        return $this->sortDirection === 'asc' ? '↑' : '↓';
    }

    #[Computed]
    public function projects()
    {
        return Project::where('owner_id', auth()->id())
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function folders()
    {
        $projectId = $this->projectId ?: $this->filterByProjectId;

        if (!$projectId) {
            return collect();
        }

        return Folder::where('project_id', $projectId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function users()
    {
        return User::select('id', 'name')
            ->orderBy('name')
            ->limit(50)
            ->get();
    }

    #[Computed]
    public function allUsers()
    {
        return User::select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.tasks', [
            'tasks' => $this->getTasks(),
        ])->layout('components.layouts.app', [
            'isRtl' => App::isLocale('fa'),
            'title' => __('messages.manage_tasks')
        ]);
    }

    public function create(): void
    {
        $this->resetForm();
        $this->dispatch('open-task-modal');
    }

    public function edit(int $id): void
    {
        $task = Task::findOrFail($id);

        // Authorization
        if ($task->created_by !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, __('messages.not_allowed_edit_task'));
        }

        $this->fill([
            'taskId' => $task->id,
            'title' => $task->title,
            'description' => $task->description ?? '',
            'projectId' => $task->project_id,
            'folderId' => $task->folder_id,
            'status' => $task->status,
            'due_date' => $task->due_date ?->format('Y-m-d'),
            'estimated_hours' => (float)$task->estimated_hours,
            'spent_hours' => (float)$task->spent_hours,
            'assigned_to' => $task->assigned_to,
        ]);

        $this->dispatch('open-task-modal');
    }

    public function save(): void
    {
        $validated = $this->validate();

        // Clean up data
        $data = [
            'title' => trim($validated['title']),
            'description' => trim($validated['description'] ?? ''),
            'project_id' => (int)$validated['projectId'],
            'folder_id' => $validated['folderId'] ? (int)$validated['folderId'] : null,
            'status' => $validated['status'],
            'due_date' => $validated['due_date'] ?? null,
            'estimated_hours' => (float)($validated['estimated_hours'] ?? 0),
            'spent_hours' => (float)($validated['spent_hours'] ?? 0),
            'assigned_to' => $validated['assigned_to'] ?? null,
        ];

        if ($this->taskId) {
            // === UPDATE ===
            $task = Task::findOrFail($this->taskId);

            // Authorization: فقط creator می‌تونه ویرایش کنه
            if ($task->created_by !== auth()->id()) {
                abort(403, __('messages.not_allowed_edit_task'));
            }

            $task->update($data);
            $message = __('messages.task_updated');
        } else {
            // === CREATE ===
            $data['created_by'] = auth()->id();

            Task::create($data);
            $message = __('messages.task_created');
        }

        $this->dispatch('close-task-modal');
        session()->flash('message', $message);
    }

    public function updateStatus(int $id, string $status): void
    {
        $task = Task::findOrFail($id);

        if ($task->created_by !== auth()->id()) {
            abort(403, __('messages.not_allowed_edit_task'));
        }

        $task->update(['status' => $status]);
        session()->flash('message', __('messages.task_status_updated'));
    }

    #[On('deleteConfirmed')]
    public function deleteConfirmed(int $id): void
    {
        $task = Task::findOrFail($id);

        if ($task->created_by !== auth()->id()) {
            abort(403);
        }

        $task->delete();
        session()->flash('message', __('messages.task_deleted'));
    }

    public function closeModal(): void
    {
        $this->resetForm();
        $this->dispatch('close-task-modal');
    }

    #[On('resetForm')]
    public function resetForm(): void
    {
        $this->reset([
            'taskId', 'title', 'description', 'status',
            'due_date', 'estimated_hours', 'spent_hours'
        ]);

        // حفظ فیلترها
        $this->projectId = $this->filterByProjectId;
        $this->folderId = $this->filterByFolderId;
        $this->assigned_to = auth()->id();

        $this->resetValidation();
    }

    private function getTasks()
    {
        return Task::query()
            ->with(['assignedUser', 'creator', 'folder', 'project'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->filterByProjectId, fn ($q) => $q->where('project_id', $this->filterByProjectId))
            ->when($this->filterByFolderId, fn ($q) => $q->where('folder_id', $this->filterByFolderId))
            ->when($this->filterByUserId, fn ($q) => $q->where('assigned_to', $this->filterByUserId))
            ->orderBy($this->sortField, $this->sortDirection)
            ->latest() // Fallback sort
            ->paginate(10);
    }
}
