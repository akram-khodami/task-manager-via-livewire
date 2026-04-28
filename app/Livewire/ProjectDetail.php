<?php

namespace App\Livewire;

use App\Models\Folder;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class ProjectDetail extends Component
{
    public Project $project;
    public $activeFolder = NULL;

    public function mount($projectId)
    {
        $this->project = Project::findOrFail($projectId);

        if ($this->project->owner_id !== auth()->id()) {
            abort(403);
        }
    }

    public function isRtl()
    {
        return App::getLocale() === 'fa';
    }

    public function render()
    {
        //get folders without parent
        $folders = Folder::where(['project_id' => $this->project->id])
            ->whereNull('parent_id')
            ->get();

        //get tasks without folder
        $tasks = Task::where(['project_id' => $this->project->id])
            ->whereNull('folder_id')
            ->pluck('title', 'id');

        return view('livewire.project-detail', [
            'project' => $this->project,
            'folders' => $folders,
            'tasks' => $tasks,
        ])->layout('components.layouts.app', [
            'isRtl' => $this->isRtl(),
            'title' => $this->project->name
        ]);
    }

    public function backToProjects()
    {
        $this->dispatch('close-modal');

        return $this->redirect(route('projects'), navigate: true);
    }

    public function setActiveFolder($folderId = NULL)
    {
        $this->activeFolder = $folderId;

        $this->dispatch('folder-selected', folderId: $folderId);

    }
}
