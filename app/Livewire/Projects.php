<?php

namespace App\Livewire;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Projects extends Component
{
    use WithPagination;

    public ?string $name = null;
    public ?string $start_date = null;
    public ?string $end_date = null;
    public ?int $project_id = null;
    public bool $isModalOpen = false;

    private array $statusCache = [];

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ];
    }

    public function render(): View
    {
        return view('livewire.projects', [
            'projects' => $this->getProjects(),
        ])->layout('components.layouts.app', [
            'isRtl' => App::isLocale('fa'),
            'title' => __('messages.manage_projects')
        ]);
    }

    public function updated($property): void
    {
        $this->validateOnly($property);
    }

    public function goToProject(int $projectId)
    {
        $this->dispatch('close-project-modal');
        return $this->redirect(route('projects.show', $projectId), navigate: true);
    }

    public function create(): void
    {
        $this->resetForm();
        $this->isModalOpen = true;
        $this->dispatch('open-project-modal');
    }

    public function store(): void
    {
        $validated = $this->validate();

        Project::create([
            ...$validated,
            'owner_id' => auth()->id(),
        ]);

        $this->notify(__('messages.project_created'));
        $this->closeModal();
    }

    public function edit(Project $project): void
    {
        $this->authorize('update', $project);

        $this->fill([
            'project_id' => $project->id,
            'name' => $project->name,
            'start_date' => $project->start_date ?->format('Y-m-d'),
            'end_date' => $project->end_date ?->format('Y-m-d'),
        ]);

        $this->isModalOpen = true;
        $this->dispatch('open-project-modal');
    }

    public function update(): void
    {
        $this->authorize('update', Project::findOrFail($this->project_id));

        $validated = $this->validate();

        Project::where('id', $this->project_id)->update($validated);

        $this->notify(__('messages.project_updated'));
        $this->closeModal();
    }

    public function destroy(Project $project): void
    {
        $this->authorize('delete', $project);

        $project->delete();
        $this->notify(__('messages.project_deleted'));
    }

    public function getProjectStatus(Project $project): array
    {
        if (isset($this->statusCache[$project->id])) {
            return $this->statusCache[$project->id];
        }

        $now = Carbon::now();
        $start = $project->start_date;
        $end = $project->end_date;

        return $this->statusCache[$project->id] = match(true){
        $now < $start => [
        'type' => 'not_started',
        'text' => __('messages.not_started'),
        'class' => 'bg-secondary',
    ],
            $now > $end => [
        'type' => 'completed',
        'text' => __('messages.completed'),
        'class' => 'bg-success',
    ],
            default => [
        'type' => 'in_progress',
        'text' => __('messages.in_progress'),
        'class' => 'bg-warning',
    ],
        };
    }

    public function getProjectProgress(Project $project): int
    {
        $now = Carbon::now();
        $start = $project->start_date;
        $end = $project->end_date;

        $totalDays = (int)$start->diffInDays($end);
        $daysPassed = (int)$start->diffInDays($now);

        if ($totalDays <= 0) {
            return $now > $end ? 100 : 0;
        }

        return (int)min(100, round(($daysPassed / $totalDays) * 100));
    }

    public function closeModal(): void
    {
        $this->resetForm();
        $this->dispatch('close-project-modal');
    }

    #[On('resetForm')]
    public function resetForm(): void
    {
        $this->reset(['name', 'start_date', 'end_date', 'project_id', 'isModalOpen']);
        $this->resetValidation();
    }

    private function getProjects(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Project::query()
            ->with('owner:id,name')
            ->where('owner_id', auth()->id())
            ->orderByDesc('start_date')
            ->paginate(12);
    }

    private function notify(string $message): void
    {
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $message,
        ]);
    }
}
