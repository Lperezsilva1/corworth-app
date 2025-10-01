{{-- resources/views/livewire/settings/roles-index.blade.php --}}
<div class="space-y-4">
  <div class="flex items-center gap-3">
    <h2 class="text-lg font-semibold"></h2>
    @if (session('ok'))  <div class="badge badge-success">{{ session('ok') }}</div>  @endif
    @if (session('err')) <div class="badge badge-error">{{ session('err') }}</div> @endif
  </div>

  <div class="card bg-base-100 border border-base-300">
    <div class="card-body p-4">
      <div class="flex items-center gap-3">
        <label class="text-sm opacity-80">{{ $editingId ? 'Rename role' : 'New role' }}</label>
        <input type="text" class="input input-sm input-bordered w-64" wire:model.defer="name" placeholder="Role name">

        <div class="ml-auto flex items-center gap-2">
          @if($editingId)
            <flux:button
              size="sm"
              variant="primary"
              wire:click="update"
              :disabled="!$name"
              wire:loading.attr="disabled"
              wire:target="update"
            >Save</flux:button>

            <flux:button
              size="sm"
              variant="ghost"
              wire:click="$set('editingId', null); $set('name','')"
            >Cancel</flux:button>
          @else
            <flux:button
              size="sm"
              variant="primary"
              wire:click="create"
              :disabled="!$name"
              wire:loading.attr="disabled"
              wire:target="create"
            >Create</flux:button>
          @endif
        </div>
      </div>

      @error('name')
        <div class="text-xs text-error mt-2">{{ $message }}</div>
      @enderror
    </div>
  </div>

  <div class="card bg-base-100 border border-base-300">
    <div class="card-body p-0">
      <div class="overflow-x-auto">
        <table class="table table-sm">
          <thead>
            <tr>
              <th>Name</th>
              <th class="w-40 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($roles as $r)
              <tr wire:key="role-{{ $r->id }}">
                <td>{{ $r->name }}</td>
                <td class="text-right space-x-2">
                  <flux:button
                    size="xs"
                    variant="ghost"
                    wire:click="edit({{ $r->id }})"
                  >Rename</flux:button>

                  <flux:button
                    size="xs"
                    variant="ghost"                          {{-- usar variant soportado --}}
                    class="text-error hover:text-error/90"    {{-- estilo rojo --}}
                    wire:click="delete({{ $r->id }})"
                    :disabled="in_array($r->name, ['Admin','Manager','Operations','Viewer'])"
                    wire:loading.attr="disabled"
                    wire:target="delete"
                  >Delete</flux:button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
