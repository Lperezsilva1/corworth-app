<div class="p-6">
  <div class="overflow-x-auto">
    <div class="relative mb-6 w-full">
      
      {{-- Breadcrumbs --}}
      {{-- Breadcrumbs solo si $showBreadcrumbs == true --}}
          @if($showBreadcrumbs)
               <x-breadcrumbs :items="[
      ['label' => 'Home', 'url' => url('/')],
      ['label' => 'Sellers', 'url' => route('sellers.index') ?? '/sellers'],
      ['label' => $sellerId ? 'Edit' : 'Create'],
    ]" />
          


      <!-- Header with title + button -->
      <div class="flex items-center justify-between">
        <div>
          <flux:heading size="xl" level="1">{{ __('Sellers') }}</flux:heading>
          <flux:subheading size="lg" class="mb-6">{{ __('Create new Seller') }}</flux:subheading>
        </div>

        <!-- Right aligned button (SPA) -->
       
      </div>
      @endif
    
    <flux:separator variant="subtle" />


    {{-- Form card aligned to the left and slightly wider --}}
<div class="w-full max-w-2xl p-6 space-y-6 mt-6 bg-transparent border-none shadow-none">

    @include('livewire.sellers.partials.form')
   
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












