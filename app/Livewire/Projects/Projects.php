<?php

namespace App\Livewire\Projects;

use Livewire\Component;
use App\Models\Project;

class Projects extends Component
{
    public bool $modalOpen = false;
    public ?int $projectId = null;

    // ===== Delete confirmation =====
    public ?int $confirmingDeleteId = null;
    public ?string $confirmingDeleteName = null;
    public string $confirmDeleteText = '';
    public ?string $deleteReason = null; // opcional
    public array $deleteReasonOptions = ['duplicate','test','wrong_data','other']; // opcional

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
        $p = Project::findOrFail($projectId);

        $this->confirmingDeleteId   = $projectId;
        $this->confirmingDeleteName = $p->project_name;
        $this->confirmDeleteText    = '';
        $this->deleteReason         = null;

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function confirmDelete(): void
    {
        // Validaciones de confirmación
        $this->validate([
            'confirmDeleteText' => ['required', function ($attr, $val, $fail) {
                if (trim((string)$val) !== (string)$this->confirmingDeleteName) {
                    $fail('Escribe exactamente el nombre del proyecto para confirmar.');
                }
            }],
            'deleteReason' => ['nullable', 'string', 'max:120'], // opcional
        ]);

        $project = Project::findOrFail($this->confirmingDeleteId);

        // (Opcional) Regla de negocio: impedir borrar si está Approved
        // if ($project->status?->key === 'approved') {
        //     $this->addError('delete', 'No puedes borrar proyectos Aprobados. Cambia el estado antes.');
        //     return;
        // }

        $project->delete(); // SoftDeletes si está activo

        // limpiar estado UI
        $this->confirmingDeleteId   = null;
        $this->confirmingDeleteName = null;
        $this->confirmDeleteText    = '';
        $this->deleteReason         = null;

        $this->dispatch('pg:eventRefresh-projects-table-main');
        session()->flash('success', 'Project deleted successfully.');
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId   = null;
        $this->confirmingDeleteName = null;
        $this->confirmDeleteText    = '';
        $this->deleteReason         = null;

        $this->resetErrorBag();
        $this->resetValidation();
    }

    // Si en algún sitio aún despachan 'delete-project', puedes mantener este borrado directo:
    #[\Livewire\Attributes\On('delete-project')]
    public function deleteProject(int $projectId): void
    {
        Project::findOrFail($projectId)->delete();
        $this->dispatch('pg:eventRefresh-projects-table-main');
        session()->flash('success', 'Project deleted successfully.');
    }

    // ===== Stats (igual que lo tenías) =====
    public function getStatsProperty(): array
    {
        return [
            'total'             => \App\Models\Project::count(),
            'pending'           => \App\Models\Project::pending()->count(),
            'working'           => \App\Models\Project::working()->count(),
            'awaiting_approval' => \App\Models\Project::awaitingApproval()->count(),
            'approved'          => \App\Models\Project::approved()->count(),
            'cancelled'         => \App\Models\Project::cancelled()->count(),
        ];
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
