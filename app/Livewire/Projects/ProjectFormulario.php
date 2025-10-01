<?php

namespace App\Livewire\Projects;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewProjectCreated;
use Livewire\Component;
use App\Models\{Project, Seller, Drafter, Building, Status};


class ProjectFormulario extends Component
{
    public ?int $projectId = null;
    public bool $showBreadcrumbs = true;

    public array $statuses = [];

    public string  $project_name = '';

    public ?int    $building_id = null;
    public ?int    $seller_id = null;

    public ?int    $phase1_drafter_id = null;
    public ?string $phase1_status = null;
    public ?string $phase1_start_date = null;
    public ?string $phase1_end_date = null;

    public ?int    $fullset_drafter_id = null;
    public ?string $fullset_status = null;
    public ?string $fullset_start_date = null;
    public ?string $fullset_end_date = null;

    public ?int    $general_status = null;
    public ?string $notes = null;

    public function mount(?int $projectId = null, bool $showBreadcrumbs = true): void
    {
        $this->projectId = $projectId;
        $this->showBreadcrumbs = $showBreadcrumbs;

        $this->statuses = Status::active()->ordered()->get(['id','label','key'])->toArray();

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
        } else {
            $this->general_status = 1;
        }
    }

    protected function rules(): array
    {
        return [
            'project_name'        => ['required', 'string', 'max:255'],
            'building_id'         => ['required', 'integer', 'exists:buildings,id'],
            'seller_id'           => ['required', 'integer', 'exists:sellers,id'],

            'phase1_drafter_id'   => ['nullable', 'integer', 'exists:drafters,id'],
            'phase1_status'       => ['nullable', 'string', 'max:64'],
            'phase1_start_date'   => ['nullable', 'date'],
            'phase1_end_date'     => ['nullable', 'date', 'after_or_equal:phase1_start_date'],

            'fullset_drafter_id'  => ['nullable', 'integer', 'exists:drafters,id'],
            'fullset_status'      => ['nullable', 'string', 'max:64'],
            'fullset_start_date'  => ['nullable', 'date'],
            'fullset_end_date'    => ['nullable', 'date', 'after_or_equal:fullset_start_date'],

            'general_status'      => ['required', 'integer', 'exists:statuses,id'],
            'notes'               => ['nullable', 'string'],
        ];
    }

    protected function messages(): array
    {
          return [
        'project_name.required' => 'The project name is required.',
        'building_id.required'  => 'You must select a building.',
        'building_id.exists'    => 'The selected building is invalid.',
        'seller_id.required'    => 'You must select a seller.',
        'seller_id.exists'      => 'The selected seller is invalid.',
        'phase1_end_date.after_or_equal'  => 'The Phase 1 end date must be the same or after the start date.',
        'fullset_end_date.after_or_equal' => 'The Full Set end date must be the same or after the start date.',
    ];
    }

    public function save(): void
    {
        $this->validate();

        $generalStatus = (int) ($this->general_status ?: 1);

        $payload = [
            'project_name'        => $this->project_name,
            'building_id'         => $this->building_id,
            'seller_id'           => $this->seller_id,

            'phase1_drafter_id'   => $this->phase1_drafter_id,
            'phase1_status'       => $this->phase1_status,
            'phase1_start_date'   => $this->phase1_start_date ?: null,
            'phase1_end_date'     => $this->phase1_end_date ?: null,

            'fullset_drafter_id'  => $this->fullset_drafter_id,
            'fullset_status'      => $this->fullset_status,
            'fullset_start_date'  => $this->fullset_start_date ?: null,
            'fullset_end_date'    => $this->fullset_end_date ?: null,

            'general_status'      => $generalStatus,
            'notes'               => $this->notes,
        ];

        if ($this->projectId) {
            Project::findOrFail($this->projectId)->update($payload);
            session()->flash('success', 'Project updated successfully.');
        } else {
            $project = Project::create($payload);
            $this->projectId = $project->id;

            session()->flash('success', 'Project created successfully.');
            $this->dispatch('notify', 'Project created successfully');

            $this->reset([
                'project_name','building_id','seller_id',
                'phase1_drafter_id','phase1_status','phase1_start_date','phase1_end_date',
                'fullset_drafter_id','fullset_status','fullset_start_date','fullset_end_date',
                'general_status','notes'
            ]);
            $this->general_status = 1;
        }

        $this->dispatch('project-saved');
        $this->dispatch('pg:eventRefresh-projects-table-main');

        
    }

    public function render()
    {
        return view('livewire.projects.project-formulario', [
            'buildings' => Building::orderBy('name_building')->get(['id','name_building']),
            'sellers'   => Seller::orderBy('name_seller')->get(['id','name_seller','email']),
            'drafters'  => Drafter::orderBy('name_drafter')->get(['id','name_drafter']),
            'statuses'  => $this->statuses,
        ]);
    }

    
}
