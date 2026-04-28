<?php

namespace App\Livewire;

use App\Models\Folder;
use App\Models\Project;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use Livewire\WithPagination;

class Folders extends Component
{
    use WithPagination;

    public $folderId;
    public $name = '';
    public $projectId;
    public $parentId;
    public $search = '';
    public $filterByProjectId = null;
    public $viewMode = 'grid'; // grid یا list

    // برای navigation درون پروژه
    public $currentFolderId = null;
    public $breadcrumbs = [];

    protected $listeners = ['deleteConfirmed' => 'deleteConfirmed'];

    public function isRtl()
    {
        return App::getLocale() === 'fa';
    }

    //checked
    protected $rules = [
        'name' => 'required|min:2|max:255',
        'projectId' => 'required|exists:projects,id',
        'parentId' => 'nullable|exists:folders,id',
    ];

    public function mount($projectId = null)
    {
        $this->filterByProjectId = $projectId;
        $this->projectId = $projectId;
        $this->loadBreadcrumbs();
    }

    public function loadBreadcrumbs()
    {
        $this->breadcrumbs = [];
        if ($this->currentFolderId) {
            $folder = Folder::find($this->currentFolderId);
            if ($folder) {
                $path = [];
                $current = $folder;
                while ($current) {
                    array_unshift($path, [
                        'id' => $current->id,
                        'name' => $current->name
                    ]);
                    $current = $current->parent;
                }
                $this->breadcrumbs = $path;
            }
        }
    }

    public function create()
    {
        $this->resetForm();
        $this->dispatch('open-folder-modal');
    }

    public function save()
    {
        $validated = $this->validate($this->rules);

        Folder::updateOrCreate(
            ['id' => $this->folderId],
            [
                'name' => trim($this->name),
                'project_id' => $this->projectId,
                'parent_id' => $this->parentId ?: $this->currentFolderId,
            ]
        );

        $this->dispatch('close-folder-modal');
        session()->flash('message', $this->folderId ? __('messages.folder_updated') : __('messages.folder_created'));
    }

    public function edit($id)
    {
        $folder = Folder::findOrFail($id);

        $this->folderId = $folder->id;
        $this->name = $folder->name;
        $this->projectId = $folder->project_id;
        $this->parentId = $folder->parent_id;

        $this->dispatch('open-folder-modal');
    }

    #[On('deleteConfirmed')]
    public function deleteConfirmed(int $id)
    {
        $folder = Folder::find($id);
        if (!$folder) {
            session()->flash('message', __('messages.folder_not_found'));
            return;
        }

        // چک کردن وجود زیرپوشه یا تسک
        $hasChildren = Folder::where('parent_id', $id)->exists();
        $hasTasks = \App\Models\Task::where('folder_id', $id)->exists();

        if ($hasChildren || $hasTasks) {
            session()->flash('error', __('messages.folder_not_empty'));
            return;
        }

        $folder->delete();
        session()->flash('message', __('messages.folder_deleted'));
    }

    public function resetForm()
    {
        $this->folderId = null;
        $this->name = '';
        $this->parentId = null;

        if ($this->filterByProjectId) {
            $this->projectId = $this->filterByProjectId;
        }
    }

    // Navigation
    public function enterFolder($folderId)
    {
        $this->currentFolderId = $folderId;
        $this->loadBreadcrumbs();
    }

    public function navigateToBreadcrumb($index)
    {
        if ($index === 'root') {
            $this->currentFolderId = null;
        } else {
            $this->currentFolderId = $this->breadcrumbs[$index]['id'] ?? null;
        }
        $this->loadBreadcrumbs();
    }

    public function goToTasks($folderId = null)
    {
        return redirect()->route('tasks.index', [
            'projectId' => $this->filterByProjectId,
            'folderId' => $folderId
        ]);
    }

    public function render()
    {
        $query = Folder::with(['parent', 'children'])
            ->withCount(['children', 'tasks'])
            ->when($this->filterByProjectId, fn ($q) => $q->where('project_id', $this->filterByProjectId))
            ->when($this->currentFolderId, fn ($q) => $q->where('parent_id', $this->currentFolderId))
            ->when(!$this->currentFolderId && $this->filterByProjectId, fn ($q) => $q->whereNull('parent_id'))
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy('name');

        $folders = $query->paginate($this->viewMode === 'grid' ? 12 : 15);

        $projects = Project::where('owner_id', auth()->id())->get();

        // آمار کلی
        $stats = [
            'total' => Folder::where('project_id', $this->filterByProjectId)->count(),
            'root' => Folder::where('project_id', $this->filterByProjectId)->whereNull('parent_id')->count(),
            'current' => $folders->total(),
        ];

        return view('livewire.folders', [
            'folders' => $folders,
            'projects' => $projects,
            'stats' => $stats,
        ])->layout('components.layouts.app', [
            'isRtl' => $this->isRtl(),
            'title' => __('messages.manage_folders')
        ]);
    }
}
