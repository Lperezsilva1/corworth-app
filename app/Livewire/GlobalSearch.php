<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\{Project, Seller, Drafter, Building};

class GlobalSearch extends Component
{
    public string $q = '';
    public array $results = [];
    public int $selectedIndex = 0;   // 👈 índice del resultado seleccionado
    private int $limit = 5;

    public function updatedQ(): void
    {
        $q = trim($this->q);

        if ($q === '') {
            $this->results = [];
            $this->selectedIndex = 0;
            return;
        }

        $like = "%{$q}%";

        $this->results = [
            'projects' => Project::query()
                ->select('id','project_name')
                ->where('project_name','like',$like)
                ->limit($this->limit)->get()
                ->map(fn($p)=>[
                    'label' => $p->project_name,
                    'sub'   => 'Project',
                    'url'   => route('projects.show', $p),
                ])->toArray(),

            'sellers' => Seller::query()
                ->select('id','name_seller')
                ->where('name_seller','like',$like)
                ->limit($this->limit)->get()
                ->map(fn($s)=>[
                    'label' => $s->name_seller,
                    'sub'   => 'Seller',
                    'url'   => route('sellers.index', ['q' => $q, 'seller' => $s->id]),
                ])->toArray(),

            'drafters' => Drafter::query()
                ->select('id','name_drafter')
                ->where('name_drafter','like',$like)
                ->limit($this->limit)->get()
                ->map(fn($d)=>[
                    'label' => $d->name_drafter,
                    'sub'   => 'Drafter',
                    'url'   => route('drafters.index', ['q' => $q, 'drafter' => $d->id]),
                ])->toArray(),

            'models' => Building::query()
                ->select('id','name_building')
                ->where('name_building','like',$like)
                ->limit($this->limit)->get()
                ->map(fn($b)=>[
                    'label' => $b->name_building,
                    'sub'   => 'Model',
                    'url'   => route('buildings.index', ['q' => $q, 'model' => $b->id]),
                ])->toArray(),
        ];

        // Al cambiar resultados, resetea selección al primero
        $this->selectedIndex = 0;
    }

    /** Aplana resultados en el mismo orden visual (grupos) */
    protected function flatResults(): array
    {
        $flat = [];
        foreach (['projects','sellers','drafters','models'] as $g) {
            foreach ($this->results[$g] ?? [] as $row) {
                $flat[] = $row;
            }
        }
        return $flat;
    }

    /** Flechas ↑/↓ para mover la selección */
    public function moveSelection(string $direction): void
    {
        $flat = $this->flatResults();
        $count = count($flat);
        if ($count === 0) { $this->selectedIndex = 0; return; }

        if ($direction === 'down') {
            $this->selectedIndex = ($this->selectedIndex + 1) % $count;
        } else {
            $this->selectedIndex = ($this->selectedIndex - 1 + $count) % $count;
        }
    }

    /** Enter → abre el seleccionado */
    public function goFirst(): void
    {
        $flat = $this->flatResults();
        if (isset($flat[$this->selectedIndex]['url'])) {
            $this->go($flat[$this->selectedIndex]['url']);
        }
    }

    public function go(string $url) { return redirect()->to($url); }

    public function clear(): void
    {
        $this->q = '';
        $this->results = [];
        $this->selectedIndex = 0;
    }

    public function render()
    {
        return view('livewire.global-search');
    }
}
