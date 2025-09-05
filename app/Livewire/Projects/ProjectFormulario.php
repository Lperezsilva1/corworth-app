<?php

namespace App\Livewire\Projects;

use Livewire\Component;
use App\Models\{Project, Seller, Drafter, Building};

class ProjectFormulario extends Component
{
    // Contexto
    public ?int $projectId = null;
    public bool $showBreadcrumbs = true; // true en página, false en modal

    // Campos
    public string  $project_name = '';

    public ?int    $building_id = null;
    public ?int    $seller_id = null;

    // Phase 1 (un solo drafter)
    public ?int    $phase1_drafter_id = null;
    public ?string $phase1_status = null;        // "Working on Phase 1's" | "Phase 1's Complete"
    public ?string $phase1_start_date = null;    // Y-m-d
    public ?string $phase1_end_date = null;      // Y-m-d

    // Full Set (opcional)
    public ?int    $fullset_drafter_id = null;
    public ?string $fullset_status = null;       // "Working on Full Set" | "Ready w/o approval" | "Full Set Complete"
    public ?string $fullset_start_date = null;   // Y-m-d
    public ?string $fullset_end_date = null;     // Y-m-d

    // General
    public ?string $general_status = null;       // "Approved" | "Not Approved" | "Cancelled"
    public ?string $notes = null;

    public function mount(?int $projectId = null, bool $showBreadcrumbs = true): void
    {
        $this->projectId = $projectId;
        $this->showBreadcrumbs = $showBreadcrumbs;

        if ($this->projectId) {
            $p = Project::findOrFail($this->projectId);

            $this->project_name       = (string) $p->project_name;

            $this->building_id        = $p->building_id;
            $this->seller_id          = $p->seller_id;

            $this->phase1_drafter_id  = $p->phase1_drafter_id;
            $this->phase1_status      = $p->phase1_status;
            $this->phase1_start_date  = optional($p->phase1_start_date)?->format('Y-m-d');
            $this->phase1_end_date    = optional($p->phase1_end_date)?->format('Y-m-d');

            $this->fullset_drafter_id = $p->fullset_drafter_id;
            $this->fullset_status     = $p->fullset_status;
            $this->fullset_start_date = optional($p->fullset_start_date)?->format('Y-m-d');
            $this->fullset_end_date   = optional($p->fullset_end_date)?->format('Y-m-d');

            $this->general_status     = $p->general_status;
            $this->notes              = $p->notes;
        }
    }

    protected function rules(): array
    {
        return [
            'project_name'       => 'required|string|max:255',

            'building_id'        => 'nullable|exists:buildings,id',
            'seller_id'          => 'nullable|exists:sellers,id',

            'phase1_drafter_id'  => 'nullable|exists:drafters,id',
            'phase1_status'      => 'nullable|string|max:64',
            'phase1_start_date'  => 'nullable|date',
            'phase1_end_date'    => 'nullable|date|after_or_equal:phase1_start_date',

            'fullset_drafter_id' => 'nullable|exists:drafters,id',
            'fullset_status'     => 'nullable|string|max:64',
            'fullset_start_date' => 'nullable|date',
            'fullset_end_date'   => 'nullable|date|after_or_equal:fullset_start_date',

            'general_status'     => 'nullable|string|max:32',
            'notes'              => 'nullable|string',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $payload = [
            'project_name'       => $this->project_name,

            'building_id'        => $this->building_id,
            'seller_id'          => $this->seller_id,

            'phase1_drafter_id'  => $this->phase1_drafter_id,
            'phase1_status'      => $this->phase1_status,
            'phase1_start_date'  => $this->phase1_start_date ?: null,
            'phase1_end_date'    => $this->phase1_end_date ?: null,

            'fullset_drafter_id' => $this->fullset_drafter_id,
            'fullset_status'     => $this->fullset_status,
            'fullset_start_date' => $this->fullset_start_date ?: null,
            'fullset_end_date'   => $this->fullset_end_date ?: null,

            'general_status'     => $this->general_status,
            'notes'              => $this->notes,
        ];

        if ($this->projectId) {
            Project::findOrFail($this->projectId)->update($payload);
            session()->flash('success', 'Project updated successfully.');
        } else {
            $project = Project::create($payload);
            $this->projectId = $project->id; // por si necesitas el id luego
            session()->flash('success', 'Project created successfully.');

            // Si quieres limpiar campos para un nuevo registro en página (no en modal):
            // $this->reset(['project_name','building_id','seller_id','phase1_drafter_id','phase1_status','phase1_start_date','phase1_end_date','fullset_drafter_id','fullset_status','fullset_start_date','fullset_end_date','general_status','notes']);
        }

        // Notificar: cerrar modal y refrescar PowerGrid
        $this->dispatch('project-saved'); // el contenedor cierra el modal
        $this->dispatch('pg:eventRefresh-projects-table-main');
    }

    public function render()
    {
        // Vista: resources/views/livewire/projects/project-formulario.blade.php
        return view('livewire.projects.project-formulario', [
            'buildings' => Building::orderBy('name_building')->get(['id','name_building']),
            'sellers'   => Seller::orderBy('name_seller')->get(['id','name_seller','email']),
            'drafters'  => Drafter::orderBy('name_drafter')->get(['id','name_drafter']),
        ]);
    }
}
