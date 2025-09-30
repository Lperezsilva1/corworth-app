@php
  $fsKey   = $project->fullsetStatus?->key;
  $fsLabel = $project->fullsetStatus?->label;

  $tones = [
    'pending'           => 'bg-zinc-50 text-zinc-700 ring-zinc-200',
    'working'           => 'bg-sky-50 text-sky-700 ring-sky-200',
    'complete'          => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    'awaiting_approval' => 'bg-amber-50 text-amber-700 ring-amber-200',
    'approved'          => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    'cancelled'         => 'bg-rose-50 text-rose-700 ring-rose-200',
  ];
  $toneClass = $tones[$fsKey] ?? 'bg-zinc-50 text-zinc-700 ring-zinc-200';
@endphp

<div class="rounded-xl bg-base-100 shadow-sm border border-base-200/80 dark:border-white/10 font-[Inter] text-[15px]">
  {{-- Header --}}
  <div class="px-6 py-5 border-b border-base-200 dark:border-white/10">
    <div class="flex items-start gap-3">
      {{-- Icono --}}
      <svg class="h-6 w-6 text-primary/80 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none"
           viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>

      <div>
        <flux:heading size="lg" class="font-semibold leading-tight">Full Set</flux:heading>
        <flux:text size="xs" class="text-base-content/60 mt-1">
          Handle the complete drawing package, assign tasks, and monitor progress for the Full Set stage.
        </flux:text>
      </div>
    </div>
  </div>

  {{-- Body --}}
  <dl class="divide-y divide-base-200 dark:divide-white/10">
    {{-- Drafter --}}
    <div class="pl-15 pr-6 py-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
      <dt>
        <div class="text-sm font-medium text-base-content">Drafter</div>
        <div class="text-xs text-base-content/60">Assign the drafter in charge of this phase.</div>
      </dt>
      <dd class="sm:col-span-2">
        @if($editing)
          <flux:field>
            <flux:select id="fullset_drafter_id" wire:model.defer="fullset_drafter_id" searchable placeholder="— Select drafter —" class="w-full">
              <flux:select.option value="">— Select drafter —</flux:select.option>
              @foreach($drafters as $d)
                <flux:select.option value="{{ $d->id }}">{{ $d->name_drafter }}</flux:select.option>
              @endforeach
            </flux:select>
            <flux:error name="fullset_drafter_id" class="text-xs text-error mt-1" />
          </flux:field>
        @else
          <span class="text-base-content/70">{{ $project->drafterFullset?->name_drafter ?? '—' }}</span>
        @endif
      </dd>
    </div>

    {{-- Status --}}
    <div class="pl-15 pr-6 py-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
      <dt>
        <div class="text-sm font-medium text-base-content">Status</div>
        <div class="text-xs text-base-content/60">Current progress of the Full Set package.</div>
      </dt>
      <dd class="sm:col-span-2 flex items-center gap-3 flex-wrap">
        @if($fsLabel)
          <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-medium ring-1 ring-inset shadow-sm {{ $toneClass }}">
            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $fsLabel }}
          </span>
        @else
          <span class="text-base-content/60">—</span>
        @endif

        @if($editing && $project->fullsetStatus?->key !== 'complete' && $project->fullset_drafter_id && $project->fullset_start_date)
          <flux:button size="sm"  @click="$dispatch('open-fullset-complete-modal')">
            Mark Full Set Complete
          </flux:button>
        @endif
        <flux:error name="fullset_status_id" class="text-xs text-error mt-1" />
      </dd>
    </div>

    {{-- Start date --}}
    <div class="pl-15 pr-6 py-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
      <dt>
        <div class="text-sm font-medium text-base-content">Start</div>
        <div class="text-xs text-base-content/60">Date when the Full Set preparation began.</div>
      </dt>
      <dd class="sm:col-span-2">
        <span class="text-base-content/70">{{ optional($project->fullset_start_date)->format('Y-m-d') ?? '—' }}</span>
      </dd>
    </div>

    {{-- End date --}}
    <div class="pl-15 pr-6 py-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
      <dt>
        <div class="text-sm font-medium text-base-content">End</div>
        <div class="text-xs text-base-content/60">Target or actual completion date of the Full Set.</div>
      </dt>
      <dd class="sm:col-span-2">
        <span class="text-base-content/70">{{ optional($project->fullset_end_date)->format('Y-m-d') ?? '—' }}</span>
      </dd>
    </div>

    {{-- Duration --}}
    <div class="pl-15 pr-6 py-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
      <dt>
        <div class="text-sm font-medium text-base-content">Duration</div>
        <div class="text-xs text-base-content/60">Total time elapsed from start to finish.</div>
      </dt>
      <dd class="sm:col-span-2 text-base-content/70">
        {{ $project->fullset_duration ? $project->fullset_duration.' days' : '—' }}
      </dd>
    </div>
  </dl>
</div>
