<div class="rounded-md border border-base-300 bg-base-100 shadow-sm overflow-hidden">
  {{-- Header --}}
  <div class="px-4 py-3 flex items-center justify-between">
    <flux:heading>Basic details</flux:heading>
  </div>

  <div class="border-t border-base-300"></div>

  <div class="px-4 py-4">
    {{-- ===== Grid: Building + Seller ===== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      {{-- Building --}}
      <div>
        @if($editing)
          <flux:field>
            <flux:label for="building_id">Building</flux:label>
            <flux:select id="building_id" wire:model.defer="building_id" searchable placeholder="Select building" class="w-full ">
              <flux:select.option value="">— Select building —</flux:select.option>
              @foreach($buildings as $b)
                <flux:select.option value="{{ $b->id }}">{{ $b->name_building }}</flux:select.option>
              @endforeach
            </flux:select>
            <flux:error name="building_id" class=" text-error mt-1" />
          </flux:field>
        @else
          <flux:text class=" text-base-content/60 mb-1">Building</flux:text>
          <flux:text class=" font-medium text-base-content">{{ $project->building?->name_building ?? '—' }}</flux:text>
        @endif
      </div>

      {{-- Seller --}}
      <div>
        @if($editing)
          <flux:field>
            <flux:label for="seller_id">Seller</flux:label>
            <flux:select id="seller_id" wire:model.defer="seller_id" searchable placeholder="Select seller" class="w-full ">
              <flux:select.option value="">— Select seller —</flux:select.option>
              @foreach($sellers as $s)
                <flux:select.option value="{{ $s->id }}">{{ $s->name_seller }}</flux:select.option>
              @endforeach
            </flux:select>
            <flux:error name="seller_id" class=" text-error mt-1" />
          </flux:field>
        @else
          <flux:text class=" text-base-content/60 mb-1">Seller</flux:text>
          <flux:text class=" font-medium text-base-content">{{ $project->seller?->name_seller ?? '—' }}</flux:text>
        @endif
      </div>
    </div>

    {{-- sep: grid -> status --}}
    <div class="my-4 border-t border-base-300"></div>

    {{-- ===== General Status ===== --}}
    @php
      $currentId   = $project->general_status;
      $current     = collect($statuses ?? [])->firstWhere('id', (int) $currentId);
      $key         = $current['key']   ?? \App\Models\Project::statusKeyById($project->general_status ?? null);
      $label       = $current['label'] ?? $project->generalStatus?->label ?? null;

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

    <div>
      <flux:text class=" text-base-content/60 mb-1">General status</flux:text>
      <div class="flex items-center gap-3 flex-wrap">
        @if($label)
          <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset shadow-sm {{ $toneClass }}">
            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $label }}
          </span>
        @else
          <flux:text class="text-base-content/60">—</flux:text>
        @endif
      </div>
    </div>

    {{-- sep: status -> notes --}}
    <div class="my-4 border-t border-base-300"></div>

    {{-- ===== Notes ===== --}}
    <div>
      <flux:text class="  text-base-content/60 mb-1">Notes</flux:text>
      @if($editing)
        <flux:field>
          <flux:textarea id="notes" rows="4" wire:model.defer="notes" class="w-full" placeholder="Project notes" />
          <flux:error name="notes" class=" text-error mt-1" />
        </flux:field>
      @else
        <flux:text class=" text-base-content whitespace-pre-line">{{ $project->notes ?: '—' }}</flux:text>
      @endif
    </div>
  </div>

  {{-- Footer con acciones --}}
  @if(!$editing)
    @php
      $generalKey = $project->status?->key;
      $p1Complete = $project->phase1Status?->key === 'complete';
      $fsComplete = $project->fullsetStatus?->key === 'complete';

      $canDeviate = $generalKey === 'awaiting_approval';
      $canApprove = in_array($generalKey, ['awaiting_approval','deviated'], true) && $p1Complete && $fsComplete;
    @endphp

    <div class="border-t border-base-300 bg-base-50 px-4 py-3 flex justify-end gap-2">
      @if($canDeviate)
        <button type="button"
                class="btn btn-warning btn-sm"
                @click="$dispatch('open-deviate-modal')">
          ↩︎ Deviated
        </button>
      @endif

      @if($canApprove)
        <button type="button"
                class="btn btn-success btn-sm"
                @click="$dispatch('open-approve-modal')">
          ✅ Approved
        </button>
      @endif
    </div>
  @endif
</div>

{{-- Overlay corporativo a pantalla completa --}}
<div wire:loading.flex
     wire:target="approveProject,markAsDeviated"
     class="fixed inset-0 z-50 items-center justify-center bg-base-100/70 backdrop-blur-sm">
  <div class="rounded-xl border border-base-300 bg-base-100/95 shadow-xl px-6 py-4">
    <div class="flex items-center gap-3">
      <span class="loading loading-spinner loading-lg text-primary"></span>
      <div class="text-sm">
        <div class="font-semibold text-base-content">Processing request</div>
        <div class="text-base-content/60">Please wait while we update the project…</div>
      </div>
    </div>
  </div>
</div>
