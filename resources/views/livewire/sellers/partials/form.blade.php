@if (session('success'))
  <div class="alert alert-success shadow-lg mb-4 w-fit">
    <span>{{ session('success') }}</span>
  </div>
@endif

<form wire:submit.prevent="save" class="space-y-4">
  <div class="form-control">
    <label class="block text-sm font-medium mb-1"><span class="label-text">Name</span></label>
    <input type="text" wire:model="name_seller" class="input input-bordered w-full bg-transparent focus:bg-transparent" placeholder="Seller name">
    @error('name_seller') <span class="text-error text-sm">{{ $message }}</span> @enderror
  </div>

  <div class="form-control">
    <label class="block text-sm font-medium mb-1"><span class="label-text">Email</span></label>
    <input type="email" wire:model="email" class="input input-bordered w-full bg-transparent focus:bg-transparent" placeholder="seller@email">
    @error('email') <span class="text-error text-sm">{{ $message }}</span> @enderror
  </div>

  <div class="form-control">
    <label class="block text-sm font-medium mb-1"><span class="label-text">Description</span></label>
    <textarea wire:model="description_seller" class="textarea textarea-bordered w-full bg-transparent focus:bg-transparent" rows="3" placeholder="Short description"></textarea>
    @error('description_seller') <span class="text-error text-sm">{{ $message }}</span> @enderror
  </div>

  <div class="form-control flux-white-ring">
    <label class="block text-sm font-medium mb-1"><span class="label-text">Status</span></label>
  <flux:select wire:model="status" class="w-full border-white text-white  focus:border-white focus:ring-2 focus:ring-white focus:outline-none"> placeholder="Selecciona estado">
  <flux:select.option value="1">Enabled</flux:select.option>
  <flux:select.option value="0">Disabled</flux:select.option>
</flux:select>
    @error('status') <span class="text-error text-sm">{{ $message }}</span> @enderror
  </div>
  

  <div class="flex justify-end">
    <button type="submit" class="btn btn-primary">
      {{ $sellerId ? 'Update' : 'Save' }}
    </button>
  </div>
</form>
