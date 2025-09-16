{{-- Minimal Pro Table con Seller (transparente) --}}
<section class="max-w-6xl mx-auto px-6 py-8 font-[Inter] select-none" wire:poll.30s>
  {{-- Header --}}
  <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
    <div>
      <h1 class="text-[28px] sm:text-[32px] font-semibold leading-tight tracking-tight">Open Projects</h1>
      <p class="text-sm text-base-content/60">Pending • Working • Awaiting Approval (public)</p>
    </div>

    {{-- buscador opcional --}}
    @isset($q)
      <div class="w-full sm:w-80">
        <input type="text" wire:model.live="q"
               placeholder="Search by project, building or seller…"
               class="input input-bordered w-full rounded-xl" />
      </div>
    @endisset
  </div>

  {{-- Tabla --}}
  <div class="rounded-2xl border border-base-300/50 bg-white/70 shadow-sm backdrop-blur-sm overflow-hidden">
    <div class="max-h-[68vh] overflow-auto">
      <table class="table w-full">
        <thead class="sticky top-0 z-10 bg-white/60 backdrop-blur-sm border-b border-base-300/50">
          <tr class="text-[11px] uppercase tracking-wide text-base-content/60">
            <th class="w-10"></th>
            <th class="px-6 py-3 text-left">Project</th>
            <th class="py-3 text-left">Building</th>
            <th class="py-3 text-left">Seller</th>
            <th class="py-3 text-left w-40">Status</th>
          </tr>
        </thead>

        <tbody class="[&>tr]:border-t [&>tr]:border-base-300/40">
          @forelse($projects as $p)
            @php
              $name   = $p->project_name;
              $bld    = $p->building?->name_building ?? '—';
              $seller = $p->seller?->name_seller ?? $p->seller?->name ?? '—';
              $key    = strtolower($p->status?->key ?? 'draft');
              $label  = $p->status?->label ?? 'Draft';

              $badge = match ($key) {
                'working'   => 'border-sky-300 text-sky-700 bg-sky-50/70',
                'pending'   => 'border-amber-300 text-amber-700 bg-amber-50/70',
                'approved'  => 'border-emerald-300 text-emerald-700 bg-emerald-50/70',
                'cancelled' => 'border-rose-300 text-rose-700 bg-rose-50/70',
                default     => 'border-slate-300 text-slate-700 bg-slate-50/70',
              };
            @endphp

            <tr class="hover:bg-white/40 transition-colors">
              {{-- avatar inicial --}}
              <td class="py-3">
                <div class="avatar placeholder">
                  <div class="w-8 h-8 rounded-md bg-base-200/70 text-base-content/70 text-xs flex items-center justify-center">
                    {{ strtoupper(mb_substr($name,0,1)) }}
                  </div>
                </div>
              </td>

              {{-- nombre + id --}}
              <td class="px-6 py-3">
                @if (Route::has('projects.show'))
                  <a href="{{ route('projects.show', $p->id) }}" class="font-medium hover:text-primary transition-colors">
                    {{ $name }}
                  </a>
                @else
                  <span class="font-medium">{{ $name }}</span>
                @endif
                <div class="text-[11px] text-base-content/60">#{{ $p->id }}</div>
              </td>

              {{-- building --}}
              <td class="text-sm">{{ $bld }}</td>

              {{-- seller --}}
              <td class="text-sm">{{ $seller }}</td>

              {{-- status --}}
              <td>
                <span class="inline-flex items-center rounded-full border {{ $badge }} px-3 py-0.5 text-[11px]">
                  {{ \Illuminate\Support\Str::of($label)->replace('_',' ')->title() }}
                </span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="py-14 text-center text-base-content/60">No open projects.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="text-xs text-base-content/60 text-right">
    Auto-refresh every 30s • {{ now()->format('M d, Y H:i') }}
  </div>
</section>
