<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\{Project, Status};
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class Main extends Component
{
    // Toolbar
    public string $range   = '30d';         // 7d | 30d | 90d | ytd
    public string $compare = 'prev-period'; // prev-period | prev-year | none

    // Datos UI
    public array $cards = [];
    public array $pendingProjects = [];
    public array $activity = [];
    public array $chart = []; // línea (created vs approved)
    public array $pie   = []; // <- NUEVO: datos de la torta por status

    public function mount(): void
    {
        $this->loadData();
    }

    public function updatedRange(): void   { $this->loadData(); }
    public function updatedCompare(): void { $this->loadData(); }

    protected function loadData(): void
    {
        [$from, $to]       = $this->currentRange();
        [$cmpFrom, $cmpTo] = $this->compareRange($from, $to);

        // IDs por key (cache)
        $ids = Cache::remember('status:id-by-key', 300, fn () => Status::pluck('id','key')->all());
        $id  = fn (string $k) => $ids[$k] ?? null;

        $pendingId  = $id('pending');
        $workingId  = $id('working');
        $approvedId = $id('approved');
        $cancelId   = $id('cancelled');

        // Métricas base
        $total         = Project::count();
        $pendingCount  = $pendingId  ? Project::where('general_status',$pendingId)->count() : 0;
        $workingCount  = $workingId  ? Project::where('general_status',$workingId)->count() : 0;
        $approvedCount = $approvedId ? Project::where('general_status',$approvedId)->count() : 0;
        $cancelledCount= $cancelId   ? Project::where('general_status',$cancelId)->count()   : 0;
        $draftCount    = Project::whereNull('general_status')->count();

        $createdThisRange  = Project::whereBetween('created_at', [$from,$to])->count();
        $approvedThisRange = $approvedId
            ? Project::where('general_status',$approvedId)->whereBetween('updated_at',[$from,$to])->count()
            : 0;

        // Comparación (ventana temporal)
        $pendingPrev  = $pendingId  ? Project::where('general_status',$pendingId)->whereBetween('updated_at',[$cmpFrom,$cmpTo])->count() : 0;
        $workingPrev  = $workingId  ? Project::where('general_status',$workingId)->whereBetween('updated_at',[$cmpFrom,$cmpTo])->count() : 0;
        $approvedPrev = $approvedId ? Project::where('general_status',$approvedId)->whereBetween('updated_at',[$cmpFrom,$cmpTo])->count() : 0;

        // KPIs
        $this->cards = [
            ['title'=>'Total Projects','value'=>number_format($total),'delta'=>$this->deltaPct($total, $total)],
            ['title'=>'Pending','value'=>number_format($pendingCount),'delta'=>$this->deltaPct($pendingCount,$pendingPrev)],
            ['title'=>'In Progress','value'=>number_format($workingCount),'delta'=>$this->deltaPct($workingCount,$workingPrev)],
            ['title'=>'Approved','value'=>number_format($approvedCount),'delta'=>$this->deltaPct($approvedCount,$approvedPrev)],
        ];

        // ===== Pie: distribución por status (incluye NULL como Draft) =====
        $this->pie = [
            ['label' => 'Approved',  'key'=>'approved',  'value' => $approvedCount],
            ['label' => 'Working',   'key'=>'working',   'value' => $workingCount],
            ['label' => 'Pending',   'key'=>'pending',   'value' => $pendingCount],
            ['label' => 'Cancelled', 'key'=>'cancelled', 'value' => $cancelledCount],
            ['label' => 'Draft',     'key'=>'draft',     'value' => $draftCount],
        ];

        // ===== Tabla: TODOS menos Approved y Cancelled (incluye NULL) =====
        $excludeIds = Status::whereIn('key', ['approved', 'cancelled'])
            ->pluck('id')
            ->filter()
            ->values()
            ->all();

        $this->pendingProjects = Project::query()
            ->with([
                'building:id,name_building',
                'seller:id,name_seller',
                'drafterPhase1:id,name_drafter',
                'drafterFullset:id,name_drafter',
                'phase1Status:id,label,key',
                'fullsetStatus:id,label,key',
                'status:id,label,key', // general
            ])
            ->when(!empty($excludeIds), function ($q) use ($excludeIds) {
                $q->where(function ($qq) use ($excludeIds) {
                    $qq->whereNull('general_status')
                       ->orWhereNotIn('general_status', $excludeIds);
                });
            })
            ->orderByDesc('updated_at')
            ->limit(12)
            ->get([
                'id','project_name','building_id','seller_id',
                'phase1_drafter_id','fullset_drafter_id',
                'phase1_status_id','fullset_status_id','general_status',
                'updated_at'
            ])
            ->values()
            ->map(function ($p, $i) {
                return [
                    'idx'          => $i + 1,
                    'id'           => $p->id,
                    'name'         => $p->project_name,
                    'building'     => $p->building?->name_building,
                    'seller'       => $p->seller?->name_seller,

                    'p1_drafter'   => $p->drafterPhase1?->name_drafter,
                    'p1_key'       => $p->phase1Status?->key,
                    'p1_label'     => $p->phase1Status?->label,

                    'fs_drafter'   => $p->drafterFullset?->name_drafter,
                    'fs_key'       => $p->fullsetStatus?->key,
                    'fs_label'     => $p->fullsetStatus?->label,

                    'gen_key'      => $p->status?->key,
                    'gen_label'    => $p->status?->label,

                    'updated'      => optional($p->updated_at)->diffForHumans(),
                ];
            })
            ->all();

        // ===== Actividad (últimos 4) =====
        $this->activity = \App\Models\ProjectComment::query()
            ->with(['project:id,project_name','user:id,name'])
            ->orderByDesc('created_at')
            ->limit(4)
            ->get(['id','project_id','user_id','title','body','is_system','source','created_at'])
            ->map(function ($c) {
                $title = (string)($c->title ?? 'Activity');
                $body  = (string)($c->body ?? '');
                $src   = (string)($c->source ?? '');

                $txt = strtolower($title.' '.$body.' '.$src);
                if (str_contains($txt, 'approved')) {
                    $icon='✅'; $color='text-success';
                } elseif (str_contains($txt, 'awaiting') || str_contains($txt, 'approval')) {
                    $icon='⏳'; $color='text-warning';
                } elseif (str_contains($txt, 'working')) {
                    $icon='⏳'; $color='text-info';
                } elseif (str_contains($txt, 'pending')) {
                    $icon='⚪'; $color='text-base-content/60';
                } elseif (str_contains($txt, 'cancel')) {
                    $icon='❌'; $color='text-error';
                } elseif (str_contains($txt, 'auto update') || str_contains($txt, 'auto_diff')) {
                    $icon='📝'; $color='text-base-content/70';
                } else {
                    $icon='💬'; $color='text-base-content/70';
                }

                return [
                    'id'        => $c->id,
                    'when'      => optional($c->created_at)->diffForHumans(),
                    'title'     => $title,
                    'body'      => \Illuminate\Support\Str::limit($body, 140),
                    'projectId' => $c->project?->id,
                    'project'   => $c->project?->project_name,
                    'user'      => $c->user?->name ?? 'System',
                    'icon'      => $icon,
                    'color'     => $color,
                ];
            })
            ->all();

        // ===== Línea: Created vs Approved (últimas 8 semanas) =====
        $this->chart = $this->buildWeeklyChart($approvedId);
    }

    protected function buildWeeklyChart(?int $approvedId): array
{
    // Usamos lunes como inicio de semana
    $end   = now()->startOfWeek(\Carbon\CarbonInterface::MONDAY);
    $start = (clone $end)->subWeeks(7);

    // Etiquetas y semanas (8 semanas: start .. end)
    $labels  = [];
    $wStarts = [];
    for ($i = 0; $i < 8; $i++) {
        $w = (clone $start)->addWeeks($i);
        $labels[]  = $w->format('M d');
        $wStarts[] = $w->toDateString(); // YYYY-MM-DD del lunes
    }

    // === Created por semana (en PHP) ===
    $createdRows = \App\Models\Project::query()
        ->where('created_at', '>=', $start)
        ->get(['created_at']);

    $createdByWeek = [];
    foreach ($createdRows as $p) {
        $w = $p->created_at->copy()->startOfWeek(\Carbon\CarbonInterface::MONDAY)->toDateString();
        $createdByWeek[$w] = ($createdByWeek[$w] ?? 0) + 1;
    }

    // === Approved por semana (en PHP) ===
    $approvedByWeek = [];
    if ($approvedId) {
        $approvedRows = \App\Models\Project::query()
            ->where('general_status', $approvedId)
            ->where('updated_at', '>=', $start)
            ->get(['updated_at']);

        foreach ($approvedRows as $p) {
            $w = $p->updated_at->copy()->startOfWeek(\Carbon\CarbonInterface::MONDAY)->toDateString();
            $approvedByWeek[$w] = ($approvedByWeek[$w] ?? 0) + 1;
        }
    }

    // Mapear a las 8 semanas exactas (0 si no hay)
    $created  = [];
    $approved = [];
    foreach ($wStarts as $w) {
        $created[]  = (int) ($createdByWeek[$w]  ?? 0);
        $approved[] = (int) ($approvedByWeek[$w] ?? 0);
    }

    return [
        'labels'   => $labels,
        'created'  => $created,
        'approved' => $approved,
    ];
}
    /** ===== Helpers ===== */

    protected function currentRange(): array
    {
        $now = now();
        return match ($this->range) {
            '7d'  => [$now->copy()->subDays(6)->startOfDay(), $now->endOfDay()],
            '30d' => [$now->copy()->subDays(29)->startOfDay(), $now->endOfDay()],
            '90d' => [$now->copy()->subDays(89)->startOfDay(), $now->endOfDay()],
            'ytd' => [$now->copy()->startOfYear(), $now->endOfDay()],
            default => [$now->copy()->subDays(29)->startOfDay(), $now->endOfDay()],
        };
    }

    protected function compareRange(Carbon $from, Carbon $to): array
    {
        $days = $to->diffInDays($from) + 1;
        return match ($this->compare) {
            'none'        => [$from->copy()->subDays($days), $from->copy()->subDay()],
            'prev-period' => [$from->copy()->subDays($days), $to->copy()->subDays($days)],
            'prev-year'   => [$from->copy()->subYear(),     $to->copy()->subYear()],
            default       => [$from->copy()->subDays($days), $to->copy()->subDays($days)],
        };
    }

    protected function rangeLabel(Carbon $from, Carbon $to): string
    {
        return $from->format('M d, Y').' – '.$to->format('M d, Y');
    }

    protected function deltaPct(int|float $current, int|float $previous): string
    {
        if ($this->compare === 'none') return '0%';
        if ($previous == 0) return $current > 0 ? '+100%' : '0%';
        $pct = (($current - $previous) / max(1e-9, $previous)) * 100;
        $sign = $pct >= 0 ? '+' : '';
        return $sign.number_format($pct, 1).'%';
    }
}
