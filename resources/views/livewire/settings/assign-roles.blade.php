{{-- resources/views/livewire/settings/assign-roles.blade.php --}}
<div class="space-y-4">
  <div class="flex items-center gap-3">
    <h2 class="text-lg font-semibold">Assign Roles</h2>
    @if (session('ok')) <div class="badge badge-success">{{ session('ok') }}</div> @endif
    <div class="ml-auto">
      <flux:button size="sm" variant="primary"
                   wire:click="save"
                   :disabled="!$userId"
                   wire:loading.attr="disabled"
                   wire:target="save,userId">
        Save
      </flux:button>
    </div>
  </div>

  <div class="flex items-center gap-3">
    <label class="text-sm opacity-80">User</label>
    <select class="select select-bordered select-sm" wire:model.live="userId">
      <option value="">Select user…</option>
      @foreach($users as $u)
        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
      @endforeach
    </select>
    <div wire:loading wire:target="userId" class="text-xs opacity-70">Loading…</div>
  </div>

  @if($userId)
    <div class="card bg-base-100 border border-base-300" wire:key="roles-{{ $userId }}">
      <div class="card-body p-4">
        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-2">
          @foreach($roles as $r)
            <label class="flex items-center gap-2 text-sm" wire:key="r-{{ $userId }}-{{ $r }}">
              <input type="checkbox"
                     class="checkbox checkbox-sm"
                     value="{{ $r }}"
                     wire:model.defer="selectedRoles">
              <span>{{ $r }}</span>
            </label>
          @endforeach
        </div>
      </div>
    </div>
  @endif
</div>
