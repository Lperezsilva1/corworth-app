<div class="p-6">
  <div class="overflow-x-auto">
    <div class="relative mb-6 w-full">
      
      {{-- Breadcrumbs --}}
      <x-breadcrumbs :items="[
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Sellers']
      ]" />

      <!-- Contenedor título + botón -->
      <div class="flex items-center justify-between">
        <div>
          <flux:heading size="xl" level="1">{{ __('Sellers') }}</flux:heading>
          <flux:subheading size="lg" class="mb-6">{{ __('Manage sellers') }}</flux:subheading>
        </div>

        <!-- Botón a la derecha (SPA) -->
        <flux:button wire:navigate href="{{ route('sellers.create') }}">+ Add New</flux:button>
      </div>
    </div>

    <flux:separator variant="subtle" />

    {{-- Tabla (PowerGrid/Livewire) --}}
    <livewire:sellers.sellers-table />

    {{-- Modal DaisyUI (crear/editar) --}}
    @if($this->modalOpen)
      <div class="modal modal-open">
        <div class="modal-box relative">
          <button wire:click="closeModal" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>

          <x-breadcrumbs :items="[
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Sellers', 'url' => route('sellers.index')],
            ['label' => $sellerId ? 'Edit' : 'Create'],
          ]" />

          <!-- Header con título -->
          <div class="flex items-center justify-between">
            <div>
              <flux:heading size="xl" level="1">{{ __('Sellers') }}</flux:heading>
              <flux:subheading size="lg" class="mb-6">
                {{ $sellerId ? __('Update seller') : __('Create seller') }}
              </flux:subheading>
            </div>
          </div>

          {{-- Montar el componente que ya usa el partial --}}
          <livewire:sellers.sellers-formulario
            :sellerId="$this->sellerId"
            :showBreadcrumbs="false"
            :key="'seller-form-'.$this->sellerId"
          />
        </div>
      </div>
    @endif
    
    {{-- Modal Delete --}}
    @if($confirmingDeleteId)
      <div class="modal modal-open">
        <div class="modal-box">
          <h3 class="font-bold text-lg">Are you sure?</h3>
          <p class="py-4">This seller will be moved to trash.</p>
          <div class="modal-action">
            <button wire:click="confirmDelete" class="btn btn-error">Yes, delete</button>
            <button wire:click="$set('confirmingDeleteId', null)" class="btn">Cancel</button>
          </div>
        </div>
      </div>
    @endif

  </div>
</div>
