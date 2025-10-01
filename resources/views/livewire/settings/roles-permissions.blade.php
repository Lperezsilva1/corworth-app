{{-- resources/views/livewire/settings/roles-permissions.blade.php --}}
<div class="p-6">
  
<div class="space-y-4">
  {{-- Encabezado + Save a la derecha --}}
  <div class="flex items-center gap-3">
    
    @if (session('ok'))
      <div class="badge badge-success">{{ session('ok') }}</div>
    @endif

    <div class="ml-auto flex items-center gap-2">
     
    </div>
  </div>

  {{-- Selector de rol (live = aplica de inmediato) --}}
  <div class="flex items-center gap-3">
    <label class="text-sm opacity-80">Role</label>

    <select class="select select-bordered select-sm"
            wire:model.live="roleId">
      @foreach($roles as $r)
        <option value="{{ $r->id }}">{{ $r->name }}</option>
      @endforeach
    </select>
   
     <flux:button wire:click="save"  wire:loading.attr="disabled" wire:target="save,roleId" :disabled="!$roleId"> Save</flux:button>
    <div wire:loading wire:target="roleId" class="ml-2 text-xs opacity-70">Loading…</div>
  </div>

  {{-- Grupos de permisos --}}
  @foreach($groups as $group => $perms)
    @if(count($perms))
      <div class="card bg-base-100 border border-base-300"
           wire:key="group-{{ $group }}-role-{{ $roleId }}">
        <div class="card-body p-3">
          <div class="flex items-center justify-between">
            <div class="font-medium">{{ $group }}</div>
            <div class="flex items-center gap-2">
              <button class="btn btn-xs" wire:click="toggleAll('{{ $group }}', true)">Select all</button>
              <button class="btn btn-xs btn-ghost" wire:click="toggleAll('{{ $group }}', false)">Clear</button>
            </div>
          </div>

          <div class="mt-2 grid md:grid-cols-2 lg:grid-cols-3 gap-2">
            @foreach($perms as $p)
              <label class="flex items-center gap-2 text-sm"
                     wire:key="perm-{{ $roleId }}-{{ md5($p) }}">
                <input type="checkbox"
                       class="checkbox checkbox-sm"
                       value="{{ $p }}"
                       wire:model.defer="selectedPerms">
                <span>{{ $p }}</span>
              </label>
            @endforeach
          </div>
        </div>
      </div>
    @endif
  @endforeach
</div>
