<form wire:submit.prevent="save" class="space-y-8">

  {{-- ========== General ========== --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Project Name --}}
    <div class="md:col-span-2">
      <flux:field>
        <flux:label for="project_name">Project Name</flux:label>
        <flux:description>Enter a clear project title.</flux:description>

        <flux:input
          id="project_name"
          wire:model.defer="project_name"
          placeholder="Enter project name"
          autocomplete="off"
        />

        <flux:error name="project_name" />
      </flux:field>
    </div>

    {{-- Model (Building) --}}
    <div>
      <flux:field>
        <flux:label for="building_id">Model</flux:label>
        <flux:description>Search and select a model for this project.</flux:description>

        <flux:select
            id="building_id"
            wire:model.defer="building_id"
            searchable
            placeholder="Search model..."
            class="w-full"
        >
            <flux:select.option value="">— Select Model —</flux:select.option>
            @foreach($buildings as $b)
                <flux:select.option value="{{ $b->id }}">
                    {{ $b->name_building }}
                </flux:select.option>
            @endforeach
        </flux:select>

        <flux:error name="building_id" />
      </flux:field>
    </div>


    


    {{-- Seller --}}
    <div>
      <flux:field>
        <flux:label for="seller_id">Seller</flux:label>
        <flux:description>Search and select the seller for this project.</flux:description>

        <flux:select
            id="seller_id"
            wire:model.defer="seller_id"
            searchable
            placeholder="Search seller..."
            class="w-full"
        >
            <flux:select.option value="">— Select Seller —</flux:select.option>
            @foreach($sellers as $s)
                <flux:select.option value="{{ $s->id }}">
                    {{ $s->name_seller }}
                </flux:select.option>
            @endforeach
        </flux:select>

        <flux:error name="seller_id" />
      </flux:field>
    </div>

    {{-- Notes --}}
    <div class="md:col-span-2">
      <flux:field>
        <flux:label for="notes">Notes</flux:label>
        <flux:description>Optional notes or context for your team.</flux:description>

        <flux:textarea
          id="notes"
          wire:model.defer="notes"
          rows="3"
          placeholder="Optional notes"
        />

        <flux:error name="notes" />
      </flux:field>
    </div>
  </div>

  {{-- ========== Actions ========== --}}
  <div class="flex items-center justify-end gap-3 pt-2">
    <flux:button variant="ghost" wire:navigate href="{{ route('projects.index') }}">
      Cancel
    </flux:button>

    <flux:button type="submit" variant="primary">
      Save
    </flux:button>
  </div>

  <flux:separator variant="subtle" />

  {{-- Flash --}}
  @if (session('success'))
    <p
      x-data="{ show: true }"
      x-show="show"
      x-init="setTimeout(() => show = false, 3000)"
      x-transition
      class="text-green-600 text-sm font-medium"
    >
      {{ session('success') }}
    </p>
  @endif
</form>
