<form wire:submit.prevent="save" class="space-y-6">

  {{-- Name --}}
  <flux:field>
    <flux:label for="name_building">Name</flux:label>
   

    <flux:input
      id="name_building"
      wire:model.defer="name_building"
      placeholder="Building name"
      autocomplete="off"
    />

    <flux:error name="name_building" />
  </flux:field>

  {{-- Description --}}
  <flux:field>
    <flux:label for="description_building">Description</flux:label>
    

    <flux:textarea
      id="description_building"
      wire:model.defer="description_building"
      rows="3"
      placeholder="Optional description"
    />

    <flux:error name="description_building" />
  </flux:field>

  {{-- Status --}}
  <flux:field>
    <flux:label for="status">Status</flux:label>
    <flux:description>Choose whether this building is enabled.</flux:description>

    <flux:select
      id="status"
      wire:model.defer="status"
      placeholder="Select status"
      class="w-full"
    >
      <flux:select.option value="1">Enabled</flux:select.option>
      <flux:select.option value="0">Disabled</flux:select.option>
    </flux:select>

    <flux:error name="status" />
  </flux:field>

  {{-- Actions --}}
  <div class="flex items-center justify-end gap-3 pt-2">
    <flux:button variant="ghost" wire:navigate href="{{ route('buildings.index') }}">
      Cancel
    </flux:button>

    <flux:button type="submit" variant="primary">
      Save
    </flux:button>
  </div>
</form>
