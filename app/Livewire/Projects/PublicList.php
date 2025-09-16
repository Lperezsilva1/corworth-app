<?php

namespace App\Livewire\Projects;

use Livewire\Component;
use App\Models\{Project, Status};

class PublicList extends Component
{
    public string $q = '';
    public int $limit = 80; // cuantos mostrar en el monitor

    protected function nonCompletedQuery()
    {
        $excludeIds = Status::whereIn('key', ['approved', 'cancelled'])
            ->pluck('id')
            ->filter()
            ->values()
            ->all();

        return Project::query()
            // 👇 Cargar seller + building + status
            // Nota: si en tu tabla sellers el nombre es "name" (no "name_seller"),
            // cambia a ->with(['seller:id,name'])
            ->with([
                'building:id,name_building',
                'status:id,label,key',
                'seller:id,name_seller',
            ])
            ->when(!empty($excludeIds), function ($q) use ($excludeIds) {
                $q->where(function ($qq) use ($excludeIds) {
                    $qq->whereNull('general_status')
                       ->orWhereNotIn('general_status', $excludeIds);
                });
            })
            ->when($this->q !== '', function ($q) {
                $q->where(function ($qq) {
                    $qq->where('project_name', 'like', '%'.$this->q.'%')
                       ->orWhereHas('building', fn($b) => $b->where('name_building', 'like', '%'.$this->q.'%'));
                    // ⚠️ Si quieres buscar por seller, DIME cómo se llama la columna:
                    // - si es "name_seller": descomenta esta línea:
                    // ->orWhereHas('seller', fn($s) => $s->where('name_seller', 'like', '%'.$this->q.'%'));
                    // - si es "name": usa: ->orWhereHas('seller', fn($s) => $s->where('name', 'like', '%'.$this->q.'%'));
                });
            });
    }

    public function render()
    {
        $query = $this->nonCompletedQuery();

        $projects = $query
            ->orderByDesc('updated_at')   // lo más reciente arriba para monitor
            ->limit($this->limit)
            // 👇 Incluimos seller_id para que el belongsTo se resuelva bien
            ->get([
                'id',
                'project_name',
                'building_id',
                'seller_id',        // 🔴 IMPORTANTE
                'general_status',
                'created_at',
                'updated_at',
            ]);

        // KPIs (rápidos)
        $statusIds = Status::pluck('id','key')->all();
        $sid = fn($k) => $statusIds[$k] ?? null;

        $stats = [
            'total'   => $projects->count(),
            'working' => $sid('working') ? $projects->where('general_status', $sid('working'))->count() : 0,
            'pending' => $sid('pending') ? $projects->where('general_status', $sid('pending'))->count() : 0,
            'draft'   => $projects->whereNull('general_status')->count(),
        ];

        return view('livewire.projects.public-list', compact('projects','stats'))
            ->layout('layouts.public', ['title' => 'Open Projects']);
    }
}
