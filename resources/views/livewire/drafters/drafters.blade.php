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
  </div>
</div>
