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
          <label class="block text-sm font-medium mb-1">Status</label>
          <select class="select select-bordered w-full" wire:model.defer="status">
            <option value="1">Enabled</option>
            <option value="0">Disabled</option>
          </select>
          @error('status')
            <p class="text-error text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 pt-2">
          <a wire:navigate href="{{ route('drafters.index') }}" class="btn btn-ghost">Cancel</a>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>