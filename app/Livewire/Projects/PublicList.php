<?php

namespace App\Livewire\Projects;

use Livewire\Component;
use App\Models\{Project, Status};

class PublicList extends Component
{
    public string $q = '';
    public int $limit = 80; // cuantos mostrar en el monitor

    /**
     * Base query:
     *  - Muestra todos los que NO son 'approved' ni 'cancelled'
     *  - Incluye 'approved' SOLO si approved_at >= now()-2d
     *  - Excluye siempre 'cancelled'
     *  - Aplica búsqueda por nombre de proyecto y building
     */
    protected function nonCompletedQuery()
    {
        // Cachea los IDs por key
        $statusIds   = Status::pluck('id', 'key')->all();
        $approvedId  = $statusIds['approved']  ?? null;
        $cancelledId = $statusIds['cancelled'] ?? null;

        $twoDaysAgo = now()->subDays(2);

        return Project::query()
            // Cargar relaciones mínimas necesarias
            ->with([
                'building:id,name_building',
                'status:id,label,key',
                'seller:id,name_seller',
            ])
            // Lógica central:
            // ( general_status IS NULL )
            // OR ( general_status NOT IN (approved, cancelled) )
            // OR ( general_status = approved AND approved_at >= now()-2d )
            ->where(function ($q) use ($approvedId, $cancelledId, $twoDaysAgo) {

                // 1) Sin status
                $q->whereNull('general_status');

                // 2) Todos los NO approved/cancelled
                $q->orWhere(function ($qq) use ($approvedId, $cancelledId) {
                    // armamos la lista a excluir
                    $exclude = array_filter([$approvedId, $cancelledId], fn($v) => !is_null($v));
                    if (!empty($exclude)) {
                        $qq->whereNotIn('general_status', $exclude);
                    } else {
                        // si no hay ids, no excluimos nada aquí
                        $qq->whereRaw('1=1');
                    }
                });

                // 3) Approved recientes (<= 2 días)
                if (!is_null($approvedId)) {
                    $q->orWhere(function ($qq) use ($approvedId, $twoDaysAgo) {
                        $qq->where('general_status', $approvedId)
                           ->where('approved_at', '>=', $twoDaysAgo);
                    });
                }
            })
            // Búsqueda
            ->when($this->q !== '', function ($q) {
                $q->where(function ($qq) {
                    $term = '%'.$this->q.'%';

                    $qq->where('project_name', 'like', $term)
                       ->orWhereHas('building', fn($b) => $b->where('name_building', 'like', $term));

                    // Si quieres buscar por seller y la columna es "name_seller", descomenta:
                    // ->orWhereHas('seller', fn($s) => $s->where('name_seller', 'like', $term));

                    // Si la columna del seller es "name", usa:
                    // ->orWhereHas('seller', fn($s) => $s->where('name', 'like', $term));
                });
            });
    }

    public function render()
    {
        $query = $this->nonCompletedQuery();

        $projects = $query
            ->orderByDesc('updated_at')   // lo más reciente arriba para monitor
            ->limit($this->limit)
            ->get([
                'id',
                'project_name',
                'building_id',
                'seller_id',        // importante para belongsTo
                'general_status',
                'approved_at',      // 👈 necesario para la lógica de 2 días y/o mostrar en la vista
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
