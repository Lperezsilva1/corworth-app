<div class="p-6">
  {{-- Breadcrumbs --}}
  <x-breadcrumbs :items="[
    ['label' => 'Home', 'url' => url('/')],
    ['label' => 'Projects', 'url' => route('projects.index')],
    [$editing ? 'Edit' : 'Show'],
  ]" />

  <div class="flex items-center justify-between mb-6">
    <div class="w-full md:w-auto md:pr-6">
      <div class="text-sm font-medium mb-1">Project</div>

      {{-- Project Name en solo lectura --}}
      <flux:heading size="xl" level="1">
        {{ $project->project_name }} {{ $project->building?->name_building ?? '—' }}
      </flux:heading>
    </div>

    <div class="flex gap-2">
      @if(!$editing)
        <button wire:click="startEdit" class="btn btn-primary">Edit</button>
        <a wire:navigate href="{{ route('projects.index') }}" class="btn">Back</a>
      @else
        <button wire:click="saveEdit" class="btn btn-primary" wire:loading.attr="disabled">Save</button>
        <button wire:click="cancelEdit" class="btn" wire:loading.attr="disabled">Cancel</button>
      @endif
    </div>
  </div>

  @if (session('success'))
    <div class="mb-4 rounded-md bg-green-500/10 text-green-700 px-4 py-2 text-sm border border-green-500/30">
      ✅ {{ session('success') }}
    </div>
  @endif

  <flux:separator variant="subtle" class="my-2" />

  {{-- ===== Tabs DaisyUI ===== --}}
  <div role="tablist" class="tabs tabs-boxed tabs tabs-lift" wire:loading.class="opacity-60 pointer-events-none">

    {{-- ===== TAB: General ===== --}}
    <input type="radio" name="project_tabs" role="tab" class="tab" aria-label="General Information" checked />
    <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-5">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Building --}}
        <div>
          <div class="text-sm text-base-content/70 mb-1">Building</div>
          @if($editing)
            <select class="select select-bordered w-full" wire:model.defer="building_id">
              <option value="">— Select building —</option>
              @foreach($buildings as $b)
                <option value="{{ $b->id }}">{{ $b->name_building }}</option>
              @endforeach
            </select>
            @error('building_id') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
          @else
            <div class="font-medium">{{ $project->building?->name_building ?? '—' }}</div>
          @endif
        </div>

        {{-- Seller --}}
        <div>
          <div class="text-sm text-base-content/70 mb-1">Seller</div>
          @if($editing)
            <select class="select select-bordered w-full" wire:model.defer="seller_id">
              <option value="">— Select seller —</option>
              @foreach($sellers as $s)
                <option value="{{ $s->id }}">{{ $s->name_seller }}</option>
              @endforeach
            </select>
            @error('seller_id') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
          @else
            <div class="font-medium">{{ $project->seller?->name_seller ?? '—' }}</div>
          @endif
        </div>

        {{-- General Status --}}
        <div class="md:col-span-2">
          <div class="text-sm text-base-content/70 mb-1">General Status</div>
          @if($editing)
            <select class="select select-bordered w-full" wire:model.defer="general_status">
              <option value="">— Select status —</option>
              @foreach($generalStatusOptions as $opt)
                <option value="{{ $opt }}">{{ $opt }}</option>
              @endforeach
            </select>
            @error('general_status') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
          @else
            @switch($project->general_status)
              @case('Approved')
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Approved</span>
                @break
              @case('Cancelled')
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Cancelled</span>
                @break
              @case('Not Approved')
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Not Approved</span>
                @break
              @default
                —
            @endswitch
          @endif
        </div>

        {{-- Notes --}}
        <div class="md:col-span-2">
          <div class="text-sm text-base-content/70 mb-1">Notes</div>
          @if($editing)
            <textarea class="textarea textarea-bordered w-full" rows="4"
                      wire:model.defer="notes" placeholder="Project notes"></textarea>
            @error('notes') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
          @else
            <div class="font-medium whitespace-pre-line">{{ $project->notes ?: '—' }}</div>
          @endif
        </div>

      </div>
    </div>

    {{-- ===== TAB: Phase 1 ===== --}}
    <input type="radio" name="project_tabs" role="tab" class="tab" aria-label="Phase 1" />
    <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-5">
      <flux:subheading size="md" class="font-semibold mb-3">Phase 1</flux:subheading>

      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="md:col-span-2">
          <div class="text-sm text-base-content/70 mb-1">Drafter</div>
          @if($editing)
            <select class="select select-bordered w-full" wire:model.defer="phase1_drafter_id">
              <option value="">— Select drafter —</option>
              @foreach($drafters as $d)
                <option value="{{ $d->id }}">{{ $d->name_drafter }}</option>
              @endforeach
            </select>
            @error('phase1_drafter_id') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
          @else
            <div class="font-medium">{{ $project->drafterPhase1?->name_drafter ?? '—' }}</div>
          @endif
        </div>

        <div>
          <div class="text-sm text-base-content/70 mb-1">Status</div>
          @if($editing)
            <select class="select select-bordered w-full" wire:model.defer="phase1_status">
              <option value="">— Select status —</option>
              @foreach($phase1StatusOptions as $opt)
                <option value="{{ $opt }}">{{ $opt }}</option>
              @endforeach
            </select>
            @error('phase1_status') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
          @else
            @php $p1 = $project->phase1_status; @endphp
            @if(!$p1)
              <div>—</div>
            @elseif($p1 === "Phase 1's Complete")
              <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">{{ $p1 }}</span>
            @else
              <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">{{ $p1 }}</span>
            @endif
          @endif
        </div>

        <div>
          <div class="text-sm text-base-content/70 mb-1">Duration</div>
          <div class="font-medium">
            {{ $project->phase1_duration_computed ? $project->phase1_duration_computed.' days' : '—' }}
          </div>
        </div>

        <div>
          <div class="text-sm text-base-content/70 mb-1">Start</div>
          @if($editing)
            <input type="date" class="input input-bordered w-full" wire:model.defer="phase1_start_date">
            @error('phase1_start_date') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
          @else
            <div class="font-medium">{{ optional($project->phase1_start_date)->format('Y-m-d') ?? '—' }}</div>
          @endif
        </div>

        <div>
          <div class="text-sm text-base-content/70 mb-1">End</div>
          @if($editing)
            <input type="date" class="input input-bordered w-full" wire:model.defer="phase1_end_date">
            @error('phase1_end_date') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
          @else
            <div class="font-medium">{{ optional($project->phase1_end_date)->format('Y-m-d') ?? '—' }}</div>
          @endif
        </div>
      </div>
    </div>

    {{-- ===== TAB: Full Set ===== --}}
    <input type="radio" name="project_tabs" role="tab" class="tab" aria-label="Full Set" />
    <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-5">
      <flux:subheading size="md" class="font-semibold mb-3">Full Set</flux:subheading>

      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="md:col-span-2">
          <div class="text-sm text-base-content/70 mb-1">Drafter</div>
          @if($editing)
            <select class="select select-bordered w-full" wire:model.defer="fullset_drafter_id">
              <option value="">— Select drafter —</option>
              @foreach($drafters as $d)
                <option value="{{ $d->id }}">{{ $d->name_drafter }}</option>
              @endforeach
            </select>
            @error('fullset_drafter_id') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
          @else
            <div class="font-medium">{{ $project->drafterFullset?->name_drafter ?? '—' }}</div>
          @endif
        </div>

        <div>
          <div class="text-sm text-base-content/70 mb-1">Status</div>
          @if($editing)
            <select class="select select-bordered w-full" wire:model.defer="fullset_status">
              <option value="">— Select status —</option>
              @foreach($fullsetStatusOptions as $opt)
                <option value="{{ $opt }}">{{ $opt }}</option>
              @endforeach
            </select>
            @error('fullset_status') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
          @else
            @php $fs = $project->fullset_status; @endphp
            @if(!$fs)
              <div>—</div>
            @elseif($fs === "Full Set Complete")
              <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">{{ $fs }}</span>
            @else
              <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">{{ $fs }}</span>
            @endif
          @endif
        </div>

        <div>
          <div class="text-sm text-base-content/70 mb-1">Duration</div>
          <div class="font-medium">
            {{ $project->fullset_duration_computed ? $project->fullset_duration_computed.' days' : '—' }}
          </div>
        </div>

        <div>
          <div class="text-sm text-base-content/70 mb-1">Start</div>
          @if($editing)
            <input type="date" class="input input-bordered w-full" wire:model.defer="fullset_start_date">
            @error('fullset_start_date') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
          @else
            <div class="font-medium">{{ optional($project->fullset_start_date)->format('Y-m-d') ?? '—' }}</div>
          @endif
        </div>

        <div>
          <div class="text-sm text-base-content/70 mb-1">End</div>
          @if($editing)
            <input type="date" class="input input-bordered w-full" wire:model.defer="fullset_end_date">
            @error('fullset_end_date') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
          @else
            <div class="font-medium">{{ optional($project->fullset_end_date)->format('Y-m-d') ?? '—' }}</div>
          @endif
        </div>
      </div>
    </div>

    {{-- ===== TAB: Notes ===== --}}
    <input type="radio" name="project_tabs" role="tab" class="tab" aria-label="Notes" />
    <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-5">
      <flux:subheading size="md" class="font-semibold mb-3">Notes</flux:subheading>

      <livewire:projects.project-comments :projectId="$project->id" :key="'comments-'.$project->id.'-'.$commentsVersion"/>
      
    </div>

  </div>
</div>
