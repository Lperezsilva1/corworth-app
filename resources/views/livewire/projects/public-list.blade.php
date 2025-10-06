{{-- Smart TV Style: Open / Not-Approved Projects --}}
<section class="fixed inset-0 w-screen h-screen select-none overflow-hidden
                bg-gradient-to-b from-neutral-950 via-neutral-900 to-neutral-950
                p-4 md:p-6 lg:p-8 flex flex-col" wire:poll.30s>

  {{-- Header --}}
  <header class="mb-6">
    <h1 class="tv-title flex items-center gap-3">
      Open Projects
      <span class="inline-block h-[12px] w-[12px] rounded-full"
            style="background: radial-gradient(circle at 30% 30%, var(--accent2), var(--accent));
                   box-shadow: 0 0 20px rgba(34,211,238,.65);"></span>
    </h1>
    <p class="tv-sub">Pending • Working • Awaiting Approval • Deviated • Cancelled • 
      <span class="font-semibold">Approved (last 2 days)</span>
    </p>
  </header>

  @php
    $baseVisible = ['pending','working','awaiting_approval','deviated','cancelled'];
    $badge = fn($key) => match($key){
      'pending'           => 'bg-gray-400 text-black',
      'working'           => 'bg-sky-500 text-white',
      'awaiting_approval' => 'bg-yellow-400 text-black',
      'deviated'          => 'bg-orange-500 text-white',
      'cancelled'         => 'bg-rose-600 text-white',
      'approved'          => 'bg-green-500 text-white',
      default             => 'bg-slate-600 text-white',
    };
    $leftBar = fn($key) => match($key){
      'pending'           => 'from-gray-400/70 to-gray-400/0',
      'working'           => 'from-sky-400/70 to-sky-400/0',
      'awaiting_approval' => 'from-yellow-400/70 to-yellow-400/0',
      'deviated'          => 'from-orange-400/70 to-orange-400/0',
      'cancelled'         => 'from-rose-500/70 to-rose-500/0',
      'approved'          => 'from-green-500/70 to-green-500/0',
      default             => 'from-slate-400/70 to-slate-400/0',
    };
    $recentLimit = now()->subDays(2);
  @endphp

  {{-- Cabecera de columnas --}}
  <div class="hidden md:grid grid-cols-12 text-[12px] uppercase tracking-wider text-neutral-300/70 mb-2">
    <div class="col-span-1"></div>
    <div class="col-span-6">Project</div>
    <div class="col-span-3">Seller</div>
    <div class="col-span-2">Status</div>
  </div>

  {{-- Lista scrollable --}}
  <div class="h-[calc(100%-130px)] overflow-auto">
    @forelse($projects as $p)
      @php
        $key     = strtolower($p->status?->key ?? 'draft');
        $name    = $p->project_name;
        $bld     = $p->building?->name_building ?? '—';
        $seller  = $p->seller?->name_seller ?? $p->seller?->name ?? '—';
        $label   = \Illuminate\Support\Str::of($p->status?->label ?? 'Draft')->replace('_',' ')->title();
        $show = in_array($key, $baseVisible, true);
        if ($key === 'approved') $show = $p->approved_at && $p->approved_at->gte($recentLimit);
        if (! $show) continue;
      @endphp

      <div class="group relative grid grid-cols-12 items-center gap-4
                  rounded-[20px] px-6 h-[90px] mb-4
                  bg-neutral-900/55 border border-neutral-800/90
                  backdrop-blur-[6px]
                  shadow-[0_1px_0_rgba(255,255,255,0.04)_inset,0_20px_40px_rgba(0,0,0,0.35)]
                  hover:bg-neutral-800/60 hover:border-neutral-700 transition-all">

        {{-- barra lateral --}}
        <span class="pointer-events-none absolute left-0 top-0 h-full w-[7px] rounded-l-[20px]
                     bg-gradient-to-b {{ $leftBar($key) }}"></span>

        {{-- Inicial --}}
        <div class="col-span-1 flex justify-center">
          <div class="w-12 h-12 rounded-[14px]
                      bg-neutral-800/80 border border-neutral-700/70
                      text-neutral-100 font-bold text-[16px] flex items-center justify-center
                      shadow-[0_1px_0_rgba(255,255,255,0.04)_inset]">
            {{ strtoupper(mb_substr($name,0,1)) }}
          </div>
        </div>

        {{-- Project + Building --}}
        <div class="col-span-6 min-w-0">
          <div class="font-semibold text-[20px] leading-tight truncate">{{ $name }}</div>
          <div class="text-[13px] text-neutral-400 truncate">{{ $bld }}</div>
          @if($key === 'approved' && $p->approved_at)
            <div class="text-[11px] text-green-400/90">
              Approved {{ $p->approved_at->diffForHumans() }}
            </div>
          @endif
        </div>

        {{-- Seller --}}
        <div class="col-span-3 min-w-0">
          <div class="text-[16px] truncate">{{ $seller }}</div>
        </div>

        {{-- Status --}}
        <div class="col-span-2 flex justify-end">
          <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $badge($key) }}
                       shadow-[0_3px_6px_rgba(0,0,0,.25)]">
            <span class="inline-block h-1.5 w-1.5 rounded-full bg-black/30 mix-blend-multiply"></span>
            {{ $label }}
          </span>
        </div>
      </div>
    @empty
      <div class="w-full h-full flex items-center justify-center text-neutral-400">No open projects.</div>
    @endforelse
  </div>

  {{-- Footer --}}
  <div class="mt-4 text-[12px] text-neutral-400 text-right">
    Auto-refresh every 30s • {{ now()->format('M d, Y H:i') }}
  </div>
</section>
