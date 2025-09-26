@if (session('success'))
  <div class="alert alert-success shadow-lg mb-4 w-fit">
    <span>{{ session('success') }}</span>
  </div>
@endif

<form wire:submit.prevent="save" class="space-y-6">

  {{-- Name --}}
  <flux:field>
    <flux:label for="name_seller">Name</flux:label>


    <flux:input
      id="name_seller"
      type="text"
      wire:model.defer="name_seller"
      placeholder="Seller name"
    />

    <flux:error name="name_seller" />
  </flux:field>

  {{-- Email --}}
  <flux:field>
    <flux:label for="email">Email</flux:label>
   

    <flux:input
      id="email"
      type="email"
      wire:model.defer="email"
      placeholder="seller@email"
    />

    <flux:error name="email" />
  </flux:field>

  {{-- Description --}}
  <flux:field>
    <flux:label for="description_seller">Description</flux:label>
   

    <flux:textarea
      id="description_seller"
      wire:model.defer="description_seller"
      rows="3"
      placeholder="Short description"
    />

    <flux:error name="description_seller" />
  </flux:field>

  {{-- Status --}}
  <flux:field>
    <flux:label for="status">Status</flux:label>
    <flux:description>Choose whether this seller is enabled.</flux:description>

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
  <div class="flex justify-end gap-3 pt-2">
    <flux:button type="submit" variant="primary">
      {{ $sellerId ? 'Update' : 'Save' }}
    </flux:button>
  </div>
</form>
