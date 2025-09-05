<?php

namespace App\Livewire\Projects;

use Livewire\Component;
use App\Models\Project;

class Projects extends Component
{
    public bool $modalOpen = false;
    public ?int $projectId = null;
    public ?int $confirmingDeleteId = null;

    #[\Livewire\Attributes\On('open-project-modal')]
    public function openProjectModal(int $projectId): void
    {
        $this->projectId = $projectId;
        $this->modalOpen = true;
    }

    #[\Livewire\Attributes\On('project-saved')]
    public function onProjectSaved(): void
    {
        // cerrar modal tras guardar desde el formulario
        $this->modalOpen = false;
        $this->projectId = null;

        // refrescar PowerGrid
       $this->dispatch('pg:eventRefresh-projects-table-main');
        session()->flash('success', 'Project saved successfully.');
    }

    #[\Livewire\Attributes\On('ask-delete-project')]
    public function askDelete(int $projectId): void
    {
        $this->confirmingDeleteId = $projectId;
    }

    public function confirmDelete(): void
    {
        Project::findOrFail($this->confirmingDeleteId)->delete();
        $this->confirmingDeleteId = null;

       $this->dispatch('pg:eventRefresh-projects-table-main');
        session()->flash('success', 'Project deleted successfully.');
    }

    #[\Livewire\Attributes\On('delete-project')]
    public function deleteProject(int $projectId): void
    {
        // borrado directo (si no usas modal de confirmación)
        Project::findOrFail($projectId)->delete();

       $this->dispatch('pg:eventRefresh-projects-table-main');
        session()->flash('success', 'Project deleted successfully.');
    }

    public function closeModal(): void
    {
        $this->modalOpen = false;
        $this->projectId = null;
    }

    public function render()
    {
        return view('livewire.projects.projects');
    }
}
