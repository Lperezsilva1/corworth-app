<div class="p-6">
  <div class="overflow-x-auto">
    <div class="relative mb-6 w-full">
      
      {{-- Breadcrumbs --}}
      {{-- Breadcrumbs solo si $showBreadcrumbs == true --}}
      @if($showBreadcrumbs)
        <x-breadcrumbs :items="[
          ['label' => 'Home', 'url' => url('/')],
          ['label' => 'Buildings', 'url' => route('buildings.index')],
          ['label' => $buildingId ? 'Edit' : 'Create'],
        ]" />

        <!-- Header with title -->
        <div class="flex items-center justify-between">
          <div>
            <flux:heading size="xl" level="1">{{ __('Buildings') }}</flux:heading>
            <flux:subheading size="lg" class="mb-6">{{ $buildingId ? __('Update building') : __('Create new building') }}</flux:subheading>
          </div>
        </div>
      @endif

      <flux:separator variant="subtle" />

      {{-- Form card aligned to the left and slightly wider --}}
      <div class="w-full max-w-2xl p-6 space-y-6 mt-6 bg-transparent border-none shadow-none">

        @include('livewire.buildings.partials.form')

        @if (session('success'))
          <p
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 3000)"
            x-transition
            class="text-green-600 text-sm font-medium mb-4"
          >
            {{ session('success') }}
          </p>
        @endif

      </div>
    </div>
  </div>
</div>

