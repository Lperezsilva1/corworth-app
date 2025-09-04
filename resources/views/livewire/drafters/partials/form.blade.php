<form wire:submit.prevent="save" class="space-y-5">
        {{-- Name --}}
        <div>
          <label class="block text-sm font-medium mb-1">Name</label>
          <input
            type="text"
            class="input input-bordered w-full bg-transparent focus:bg-transparent"
            wire:model.defer="name_drafter"
            placeholder="Drafter name"
          >
          @error('name_drafter')
            <p class="text-error text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Description --}}
        <div>
          <label class="block text-sm font-medium mb-1">Description</label>
          <textarea
            class="textarea textarea-bordered w-full bg-transparent focus:bg-transparent"
            rows="3"
            wire:model.defer="description_drafter"
            placeholder="Optional description"
          ></textarea>
          @error('description_drafter')
            <p class="text-error text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Status --}}
        <div>
          <div class="form-control flux-white-ring">
          <label class="block text-sm font-medium mb-1">Status</label>
        <flux:select wire:model.defer="status" placeholder="Selecciona estado" class="w-full border-white text-white  focus:border-white focus:ring-2 focus:ring-white focus:outline-none">
            <flux:select.option value="1">Enabled</flux:select.option>
            <flux:select.option value="0">Disabled</flux:select.option>
        </flux:select>

          @error('status')
            <p class="text-error text-sm mt-1">{{ $message }}</p>
          @enderror
        </div></div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 pt-2">
          <a wire:navigate href="{{ route('drafters.index') }}" class="btn btn-ghost">Cancel</a>
          <button type="submit" class="btn btn-primary btn-active">Save</button>


          
        </div>
      </form>