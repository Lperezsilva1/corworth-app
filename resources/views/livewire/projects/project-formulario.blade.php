<div class="p-6">
  <div class="overflow-x-auto">
    <div class="relative mb-6 w-full">

      {{-- Breadcrumbs solo si $showBreadcrumbs == true --}}
      @if($showBreadcrumbs)
        <x-breadcrumbs :items="[
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Projects', 'url' => route('projects.index')],
            ['label' => $projectId ? 'Edit' : 'Create'],
        ]" />

        <!-- Header with title -->
        <div class="flex items-center justify-between">
          <div>
            <flux:heading size="xl" level="1">{{ __('Projects') }}</flux:heading>
            <flux:subheading size="lg" class="mb-6">
              {{ $projectId ? __('Edit project') : __('Create new project') }}
            </flux:subheading>
          </div>
          <!-- (opcional) botón a la derecha -->
          {{-- <flux:button wire:navigate href="{{ route('projects.index') }}">{{ __('Back to list') }}</flux:button> --}}
        </div>
      @endif

      <flux:separator variant="subtle" />

      {{-- Form card aligned to the left and slightly wider --}}
      <div class="w-full max-w-2xl p-6 space-y-6 mt-6 bg-transparent border-none shadow-none">

        {{-- Partial del formulario de Projects --}}
        @include('livewire.projects.partials.form')


      </div>
    </div>
  </div>
</div>
