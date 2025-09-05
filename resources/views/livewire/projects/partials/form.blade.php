<form wire:submit.prevent="save" class="space-y-8">
  {{-- ========== General ========== --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Project Name --}}
    <div class="md:col-span-2">
      <label class="block text-sm font-medium mb-1">Project Name</label>
      <input
        type="text"
        class="input input-bordered w-full bg-transparent focus:bg-transparent"
        wire:model.defer="project_name"
        placeholder="Enter project name"
      >
      @error('project_name') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Building --}}
    <div>
      <label class="block text-sm font-medium mb-1">Model</label>
      <select
        class="select select-bordered w-full bg-transparent focus:bg-transparent"
        wire:model.defer="building_id"
      >
        <option value="">— Select Model —</option>
        @foreach($buildings as $b)
          <option value="{{ $b->id }}">{{ $b->name_building }}</option>
        @endforeach
      </select>
      @error('building_id') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Seller --}}
    <div>
      <label class="block text-sm font-medium mb-1">Seller</label>
      <select
        class="select select-bordered w-full bg-transparent focus:bg-transparent"
        wire:model.defer="seller_id"
      >
        <option value="">— Select seller —</option>
        @foreach($sellers as $s)
          <option value="{{ $s->id }}">{{ $s->name_seller }}</option>
        @endforeach
      </select>
      @error('seller_id') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Notes --}}
    <div class="md:col-span-2">
      <label class="block text-sm font-medium mb-1">Notes</label>
      <textarea
        class="textarea textarea-bordered w-full bg-transparent focus:bg-transparent"
        rows="3"
        wire:model.defer="notes"
        placeholder="Optional notes"
      ></textarea>
      @error('notes') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
    </div>
  </div>
  
 {{-- ========== Actions ========== --}}
  <div class="flex items-center justify-end gap-3 pt-2">
    <a wire:navigate href="{{ route('projects.index') }}" class="btn btn-ghost">Cancel</a>
    <button type="submit" class="btn btn-primary btn-active">Save</button>
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


