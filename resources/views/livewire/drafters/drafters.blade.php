<div class="p-6">
  <div class="overflow-x-auto">
    <div class="relative mb-6 w-full">
      
      {{-- Breadcrumbs --}}
      <x-breadcrumbs :items="[
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Drafters']
      ]" />

      <!-- Contenedor título + botón -->
      <div class="flex items-center justify-between">
        <div>
          <flux:heading size="xl" level="1">{{ __('Drafters') }}</flux:heading>
          <flux:subheading size="lg" class="mb-6">{{ __('Manages project drafters') }}</flux:subheading>
        </div>

        <!-- Botón a la derecha (SPA) -->
        <flux:button wire:navigate href="{{ route('drafters.create') }}">+ Add New</flux:button>
      </div>
    </div>

    <flux:separator variant="subtle" />

    {{-- Tabla (PowerGrid/Livewire) --}}
    <livewire:drafters.drafters-table />


{{-- Modal DaisyUI --}}
    @if($this->modalOpen)
      <div class="modal modal-open">
        <div class="modal-box relative">
          <button wire:click="closeModal" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                <x-breadcrumbs :items="[
                      ['label' => 'Home', 'url' => url('/')],
                      ['label' => 'Drafters', 'url' => route('drafters.index')],
                      ['label' => $drafterId ? 'Edit' : 'Create'],
                  ]" />
          <!-- Header with title + button -->
          <div class="flex items-center justify-between">
            <div>
              <flux:heading size="xl" level="1">{{ __('Drafters') }}</flux:heading>
              <flux:subheading size="lg" class="mb-6">{{ __('Update drafter') }}</flux:subheading>
            </div>

            <!-- Right aligned button (SPA) -->
          
          </div>

          {{-- Montar el componente que ya usa el partial --}}
          <livewire:drafters.drafters-formulario :drafterId="$this->drafterId" :showBreadcrumbs="false" :key="'form-'.$this->drafterId"/>
        </div>
      </div>
    @endif
    
{{-- Modal Delete --}}
    @if($confirmingDeleteId)
  <div class="modal modal-open">
    <div class="modal-box">
      <h3 class="font-bold text-lg">Are you sure?</h3>
      <p class="py-4">This drafter will be moved to trash.</p>
      <div class="modal-action">
        <button wire:click="confirmDelete" class="btn btn-error">Yes, delete</button>
        <button wire:click="$set('confirmingDeleteId', null)" class="btn">Cancel</button>
      </div>
    </div>
  </div>
@endif


  </div>
</div>