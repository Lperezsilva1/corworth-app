<div class="overflow-x-auto">
  <div class="relative mb-6 w-full">
    <!-- Contenedor con título + botón -->
    <div class="flex items-center justify-between">
      <div>
        <flux:heading size="xl" level="1">{{ __('Drafters') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manages project drafters') }}</flux:subheading>
      </div>

      <!-- Botón a la derecha -->
      <button class="btn btn-neutral-content">
        + Add New
      </button>
    </div>

    <flux:separator variant="subtle" />
  </div>

<livewire:drafters.drafters-table/>

</div>





