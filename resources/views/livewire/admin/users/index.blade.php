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
</div></div>