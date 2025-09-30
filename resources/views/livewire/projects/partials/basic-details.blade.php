@php
  // === Status badge tones ===
  $currentId = $project->general_status;
  $current   = collect($statuses ?? [])->firstWhere('id', (int) $currentId);
  $key       = $current['key']   ?? \App\Models\Project::statusKeyById($project->general_status ?? null);
  $label     = $current['label'] ?? $project->generalStatus?->label ?? null;

  $tones = [
    'pending'           => 'bg-zinc-50 text-zinc-700 ring-zinc-200',
    'working'           => 'bg-sky-50 text-sky-700 ring-sky-200',
    'awaiting_approval' => 'bg-amber-50 text-amber-700 ring-amber-200',
    'deviated'          => 'bg-amber-100 text-amber-800 ring-amber-300',
    'approved'          => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    'cancelled'         => 'bg-rose-50 text-rose-700 ring-rose-200',
  ];
  $toneClass = $tones[$key] ?? 'bg-zinc-50 text-zinc-700 ring-zinc-200';
@endphp

<div class="rounded-xl bg-base-100 shadow-sm border border-base-200/80 dark:border-white/10 font-[Inter] text-[15px]">
  {{-- Header --}}
  <div class="px-6 py-5 border-b border-base-200 dark:border-white/10">
    <div class="flex items-start gap-3">
      {{-- Icono --}}
      <svg class="h-6 w-6 text-primary/80 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none"
           viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M4 6h16M4 12h16M4 18h16"/>
      </svg>

      <div>
        <flux:heading size="lg" class="font-semibold leading-tight">General information</flux:heading>
        <flux:text size="xs" class="text-base-content/60 mt-1">
          Core project details.
        </flux:text>
      </div>
    </div>
  </div>

  {{-- Body (display list con separadores light/dark) --}}
  <dl class="divide-y divide-base-200 dark:divide-white/10">
    {{-- Building --}}
    <div class="px-8 py-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
      <dt>
        <div class="text-sm font-medium text-base-content">Building</div>
        <div class="text-xs text-base-content/60">Associated model/building for this project.</div>
      </dt>
      <dd class="sm:col-span-2">
        @if($editing)
          <flux:field>
            <flux:label for="building_id" class="sr-only">Building</flux:label>
            <flux:select id="building_id" wire:model.defer="building_id" searchable placeholder="— Select building —" class="w-full">
              <flux:select.option value="">— Select building —</flux:select.option>
              @foreach($buildings as $b)
                <flux:select.option value="{{ $b->id }}">{{ $b->name_building }}</flux:select.option>
              @endforeach
            </flux:select>
            <flux:error name="building_id" class="text-xs text-error mt-1" />
          </flux:field>
        @else
          <span class="text-base-content/70">{{ $project->building?->name_building ?? '—' }}</span>
        @endif
      </dd>
    </div>

    {{-- Seller --}}
    <div class="px-8 py-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
      <dt>
        <div class="text-sm font-medium text-base-content">Seller</div>
        <div class="text-xs text-base-content/60">Main commercial contact/owner.</div>
      </dt>
      <dd class="sm:col-span-2">
        @if($editing)
          <flux:field>
            <flux:label for="seller_id" class="sr-only">Seller</flux:label>
            <flux:select id="seller_id" wire:model.defer="seller_id" searchable placeholder="— Select seller —" class="w-full">
              <flux:select.option value="">— Select seller —</flux:select.option>
              @foreach($sellers as $s)
                <flux:select.option value="{{ $s->id }}">{{ $s->name_seller }}</flux:select.option>
              @endforeach
            </flux:select>
            <flux:error name="seller_id" class="text-xs text-error mt-1" />
          </flux:field>
        @else
          <span class="text-base-content/70">{{ $project->seller?->name_seller ?? '—' }}</span>
        @endif
      </dd>
    </div>

    {{-- General status --}}
    <div class="px-8 py-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
      <dt>
        <div class="text-sm font-medium text-base-content">General status</div>
        <div class="text-xs text-base-content/60">Overall state of the project.</div>
      </dt>
      <dd class="sm:col-span-2 flex items-center gap-3 flex-wrap">
        @if($label)
          <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-medium ring-1 ring-inset shadow-sm {{ $toneClass }}">
            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $label }}
          </span>
        @else
          <span class="text-base-content/60">—</span>
        @endif
      </dd>
    </div>

    {{-- Notes --}}
    <div class="px-8 py-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
      <dt>
        <div class="text-sm font-medium text-base-content">Notes</div>
        <div class="text-xs text-base-content/60">Context or important remarks.</div>
      </dt>
      <dd class="sm:col-span-2">
        @if($editing)
          <flux:field>
            <flux:label for="notes" class="sr-only">Notes</flux:label>
            <flux:textarea id="notes" rows="4" wire:model.defer="notes" class="w-full" placeholder="Project notes" />
            <flux:error name="notes" class="text-xs text-error mt-1" />
          </flux:field>
        @else
          <span class="text-base-content/70 whitespace-pre-line">{{ $project->notes ?: '—' }}</span>
        @endif
      </dd>
    </div>
  </dl>

  {{-- Footer acciones (condicional) --}}
  @if(!$editing)
    @php
      $generalKey = $project->status?->key;
      $p1Complete = $project->phase1Status?->key === 'complete';
      $fsComplete = $project->fullsetStatus?->key === 'complete';
      $canDeviate = $generalKey === 'awaiting_approval';
      $canApprove = in_array($generalKey, ['awaiting_approval','deviated'], true) && $p1Complete && $fsComplete;
    @endphp

    <div class="border-t border-base-200 dark:border-white/10 bg-base-50/60 px-6 py-4 flex justify-end gap-2">
      @if($canDeviate)
        <button type="button" class="btn btn-warning btn-sm" @click="$dispatch('open-deviate-modal')">
          ↩︎ Deviated
        </button>
      @endif
      @if($canApprove)
        <button type="button" class="btn btn-success btn-sm" @click="$dispatch('open-approve-modal')">
          ✅ Approved
        </button>
      @endif
    </div>
  @endif
</div>

{{-- Overlay corporativo --}}
<div wire:loading.flex
     wire:target="approveProject,markAsDeviated"
     class="fixed inset-0 z-50 items-center justify-center bg-base-100/70 backdrop-blur-sm">
  <div class="rounded-xl border border-base-200 dark:border-white/10 bg-base-100/95 shadow-xl px-6 py-4">
    <div class="flex items-center gap-3">
      <span class="loading loading-spinner loading-lg text-primary"></span>
      <div class="text-sm">
        <div class="font-semibold text-base-content">Processing request</div>
        <div class="text-base-content/60">Please wait while we update the project…</div>
      </div>
    </div>
  </div>
</div>
