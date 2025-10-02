@php
  $p1Key   = $project->phase1Status?->key;
  $p1Label = $project->phase1Status?->label;

  $tones = [
    'pending'           => 'bg-zinc-50 text-zinc-700 ring-zinc-200',
    'working'           => 'bg-sky-50 text-sky-700 ring-sky-200',
    'complete'          => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    'awaiting_approval' => 'bg-amber-50 text-amber-700 ring-amber-200',
    'approved'          => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    'cancelled'         => 'bg-rose-50 text-rose-700 ring-rose-200',
  ];
  $toneClass = $tones[$p1Key] ?? 'bg-zinc-50 text-zinc-700 ring-zinc-200';
@endphp

<div class="rounded-xl bg-base-100 shadow-sm border border-base-200/80 dark:border-white/10 font-[Inter] text-[15px]">
  {{-- Header --}}
  <div class="px-6 py-5 border-b border-base-200 dark:border-white/10">
    <div class="flex items-start gap-3">
      {{-- Icono al lado del título --}}
      <svg class="h-6 w-6 text-primary/80 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none"
           viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>

      <div>
        <flux:heading size="lg" class="font-semibold leading-tight">Phase 1</flux:heading>
        <flux:text size="xs" class="text-base-content/60 mt-1">
          Define responsibilities, track status, and set timelines for Phase 1.
        </flux:text>
      </div>
    </div>
  </div>

  {{-- Body --}}
  <dl class="divide-y divide-base-200 dark:divide-white/10">
    {{-- Drafter --}}
    <div class="px-15 py-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
      <dt>
        <div class="text-sm font-medium text-base-content">Drafter</div>
        <div class="text-xs text-base-content/60">Assign the drafter in charge of this phase.</div>
      </dt>
      <dd class="sm:col-span-2 pl-6">
        @if($editing)
          <flux:field>
            <flux:select id="phase1_drafter_id" wire:model.defer="phase1_drafter_id" searchable placeholder="" class="w-full">
              <flux:select.option value="">— Select drafter —</flux:select.option>
              @foreach($drafters as $d)
                <flux:select.option value="{{ $d->id }}">{{ $d->name_drafter }}</flux:select.option>
              @endforeach
            </flux:select>
            <flux:error name="phase1_drafter_id" class="text-xs text-error mt-1" />
          </flux:field>
        @else
          <span class="text-base-content/70">{{ $project->drafterPhase1?->name_drafter ?? '—' }}</span>
        @endif
      </dd>
    </div>

    {{-- Status --}}
    <div class="px-15 py-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
      <dt>
        <div class="text-sm font-medium text-base-content">Status</div>
        <div class="text-xs text-base-content/60">Track current progress of this phase.</div>
      </dt>
      <dd class="sm:col-span-2 pl-6 flex items-center gap-3 flex-wrap">
        @if($p1Label)
          <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-medium ring-1 ring-inset shadow-sm {{ $toneClass }}">
            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $p1Label }}
          </span>
        @else
          <span class="text-base-content/60">—</span>
        @endif
        @if($editing && $project->phase1Status?->key !== 'complete' && $project->phase1_drafter_id && $project->phase1_start_date)
    <flux:button size="sm" @click="$dispatch('open-phase1-complete-modal')">
        Mark Phase 1 Complete
    </flux:button>
@endif

<flux:error name="phase1_status_id" class="text-xs text-error mt-1" />
      </dd>
    </div>

    {{-- Start date --}}
    <div class="px-15 py-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
      <dt>
        <div class="text-sm font-medium text-base-content">Start</div>
        <div class="text-xs text-base-content/60">When the phase officially began.</div>
      </dt>
      <dd class="sm:col-span-2 pl-6">
        <span class="text-base-content/70">{{ optional($project->phase1_start_date)->format('Y-m-d') ?? '—' }}</span>
      </dd>
    </div>

    {{-- End date --}}
    <div class="px-15 py-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
      <dt>
        <div class="text-sm font-medium text-base-content">End</div>
        <div class="text-xs text-base-content/60">Target or actual completion date for this phase.</div>
      </dt>
      <dd class="sm:col-span-2 pl-6">
        <span class="text-base-content/70">{{ optional($project->phase1_end_date)->format('Y-m-d') ?? '—' }}</span>
      </dd>
    </div>

    {{-- Duration --}}
    <div class="px-15 py-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
      <dt>
        <div class="text-sm font-medium text-base-content">Duration</div>
        <div class="text-xs text-base-content/60">Total time elapsed between start and end dates.</div>
      </dt>
      <dd class="sm:col-span-2 pl-6 text-base-content/70">
        {{ $project->phase1_duration ? $project->phase1_duration.' days' : '—' }}
      </dd>
    </div>
  </dl>
</div>
