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

<div class="rounded-md border border-base-300 bg-base-100 shadow-sm overflow-hidden">
  <div class="px-4 py-3">
    <flux:subheading size="sm">Phase 1</flux:subheading>
  </div>
  <div class="border-t border-base-300"></div>

  <div class="px-4 py-5 grid grid-cols-1 md:grid-cols-4 gap-6">
    {{-- Drafter --}}
    <div class="md:col-span-2">
      @if($editing)
        <flux:field>
          <flux:label for="phase1_drafter_id">Drafter</flux:label>
          <flux:select id="phase1_drafter_id" wire:model.defer="phase1_drafter_id" searchable placeholder="Select drafter" class="w-full">
            <flux:select.option value="">— Select drafter —</flux:select.option>
            @foreach($drafters as $d)
              <flux:select.option value="{{ $d->id }}">{{ $d->name_drafter }}</flux:select.option>
            @endforeach
          </flux:select>
          <flux:error name="phase1_drafter_id" class="text-xs text-error mt-1" />
        </flux:field>
      @else
        <flux:heading size="xs">Drafter</flux:heading>
        <flux:text class="mt-1">{{ $project->drafterPhase1?->name_drafter ?? '—' }}</flux:text>
      @endif
    </div>

    {{-- Status --}}
    <div>
      <flux:heading size="xs">Status</flux:heading>
      <div class="mt-2 flex items-center gap-3 flex-wrap">
        @if($p1Label)
          <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset shadow-sm {{ $toneClass }}">
            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $p1Label }}
          </span>
        @else
          <flux:text class="text-base-content/60">—</flux:text>
        @endif

        @if($editing)
          @if($project->phase1Status?->key !== 'complete'
              && $project->phase1_drafter_id
              && $project->phase1_start_date)
            <flux:button size="sm"  @click="$dispatch('open-phase1-complete-modal')">
              Mark Phase 1 Complete
            </flux:button>
          @endif
          <flux:error name="phase1_status_id" class="text-xs text-error mt-1" />
        @endif
      </div>
    </div>

    {{-- Duration --}}
    <div>
      <flux:heading size="xs">Duration</flux:heading>
      <flux:text class="mt-2">
        {{ $project->phase1_duration ? $project->phase1_duration.' days' : '—' }}
      </flux:text>
    </div>

    {{-- Start date --}}
    <div>
      @if($editing)
        <flux:field>
          <flux:label for="phase1_start_date">Start</flux:label>
          <flux:input id="phase1_start_date" type="date" wire:model.defer="phase1_start_date" class="w-full" />
          <flux:error name="phase1_start_date" class="text-xs text-error mt-1" />
        </flux:field>
      @else
        <flux:heading size="xs">Start</flux:heading>
        <flux:text class="mt-2">{{ optional($project->phase1_start_date)->format('Y-m-d') ?? '—' }}</flux:text>
      @endif
    </div>

    {{-- End date --}}
    <div>
      @if($editing)
        <flux:field>
          <flux:label for="phase1_end_date">End</flux:label>
          <flux:input id="phase1_end_date" type="date" wire:model.defer="phase1_end_date" class="w-full" />
          <flux:error name="phase1_end_date" class="text-xs text-error mt-1" />
        </flux:field>
      @else
        <flux:heading size="xs">End</flux:heading>
        <flux:text class="mt-2">{{ optional($project->phase1_end_date)->format('Y-m-d') ?? '—' }}</flux:text>
      @endif
    </div>
  </div>
</div>
