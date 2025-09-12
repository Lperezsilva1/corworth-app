<?php

namespace App\Livewire\Activity;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProjectComment;

class Index extends Component
{
    use WithPagination;

    /** Pagination mode: 'cursor' (recommended), 'simple', or 'page' */
    public string $paginationMode = 'cursor';

    /** Filters */
    public string $search = '';
    public int $perPage = 15;
    public ?string $from = null; // Y-m-d
    public ?string $to   = null; // Y-m-d

    /** Keep state in query string */
    protected $queryString = [
        'search'  => ['except' => ''],
        'perPage' => ['except' => 15],
        'from'    => ['except' => null],
        'to'      => ['except' => null],
        // page/cursor are handled automatically by Laravel
    ];

    public function updatingSearch()  { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }
    public function updatingFrom()    { $this->resetPage(); }
    public function updatingTo()      { $this->resetPage(); }

    public function render()
    {
        $base = ProjectComment::query()
            ->with(['project:id,project_name', 'user:id,name'])
            // Optional date filters
            ->when($this->from, fn ($q) => $q->whereDate('created_at', '>=', $this->from))
            ->when($this->to,   fn ($q) => $q->whereDate('created_at', '<=', $this->to))
            // Search in title, body and project name
            ->when($this->search, function ($q) {
                $term = "%{$this->search}%";
                $q->where(function ($sub) use ($term) {
                    $sub->where('title', 'like', $term)
                        ->orWhere('body', 'like', $term)
                        ->orWhereHas('project', fn ($p) => $p->where('project_name', 'like', $term));
                });
            })
            // Stable order for cursor pagination
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        $activities = match ($this->paginationMode) {
            'page'   => $base->paginate($this->perPage)->withQueryString(),
            'simple' => $base->simplePaginate($this->perPage)->withQueryString(),
            default  => $base->cursorPaginate($this->perPage)->withQueryString(),
        };

        return view('livewire.activity.index', compact('activities'));
    }
}
