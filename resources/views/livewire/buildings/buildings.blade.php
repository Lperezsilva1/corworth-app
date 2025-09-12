<div class="p-6">
  <div class="overflow-x-auto">
    <div class="relative mb-6 w-full">
      
      {{-- Breadcrumbs --}}
      <x-breadcrumbs :items="[
        ['label' => 'Home', 'url' => url('/dashboard')],
        ['label' => 'Buildings']
      ]" />

      <!-- Contenedor título + botón -->
      <div class="flex items-center justify-between">
        <div>
          <flux:heading size="xl" level="1">{{ __('Buildings') }}</flux:heading>
          <flux:subheading size="lg" class="mb-6">{{ __('Manages project buildings') }}</flux:subheading>
        </div>

        <!-- Botón a la derecha (SPA) -->
        <flux:button wire:navigate href="{{ route('buildings.create') }}">+ Add New</flux:button>
      </div>
    </div>

    <flux:separator variant="subtle" />

    {{-- Tabla (PowerGrid/Livewire) --}}
    <livewire:buildings.buildings-table />

    {{-- Modal DaisyUI --}}
    @if($this->modalOpen)
      <div class="modal modal-open">
        <div class="modal-box relative">
          <button wire:click="closeModal" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>

          <x-breadcrumbs :items="[
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Buildings', 'url' => route('buildings.index')],
            ['label' => $buildingId ? 'Edit' : 'Create'],
          ]" />

          <div class="flex items-center justify-between">
            <div>
              <flux:heading size="xl" level="1">{{ __('Buildings') }}</flux:heading>
              <flux:subheading size="lg" class="mb-6">{{ __('Update building') }}</flux:subheading>
            </div>
          </div>

          {{-- Formulario (usa el partial adaptado) --}}
          <livewire:buildings.building-formulario
            :buildingId="$this->buildingId"
            :showBreadcrumbs="false"
            :key="'form-'.$this->buildingId"
          />
        </div>
      </div>
    @endif
    
    {{-- Modal Delete --}}
    @if($confirmingDeleteId)
      <div class="modal modal-open">
        <div class="modal-box">
          <h3 class="font-bold text-lg">Are you sure?</h3>
          <p class="py-4">This building will be moved to trash.</p>
          <div class="modal-action">
            <button wire:click="confirmDelete" class="btn btn-error">Yes, delete</button>
            <button wire:click="$set('confirmingDeleteId', null)" class="btn">Cancel</button>
          </div>
        </div>
      </div>
    @endif

  </div>
</div>
