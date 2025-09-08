<div class="p-6">
  <div class="overflow-x-auto">
    <div class="relative mb-6 w-full">
      {{-- Breadcrumbs --}}
      <x-breadcrumbs :items="[
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Projects']
      ]" />

      <!-- Contenedor título + botón (con icono) -->
      <div class="flex items-center justify-between">
        <div class="flex items-start gap-3">
          <!-- Icono redondo de construcción -->
          <div class="h-12 w-12 rounded-full bg-primary/10 text-primary ring-1 ring-primary/20 flex items-center justify-center shrink-0">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"
                 class="h-6 w-6" aria-hidden="true">
              <!-- Edificio izquierdo -->
              <rect x="3" y="3" width="7" height="18" rx="1"></rect>
              <path d="M3 9h7M3 13h7M3 17h7"></path>
              <!-- Edificio derecho -->
              <rect x="14" y="7" width="7" height="14" rx="1"></rect>
              <path d="M14 11h7M14 15h7M14 19h7"></path>
            </svg>
            <span class="sr-only">Projects</span>
          </div>

          <div>
            <flux:heading size="xl" level="1">{{ __('Projects') }}</flux:heading>
            <flux:subheading size="lg" class="mb-6">{{ __('Manage your projects') }}</flux:subheading>
          </div>
        </div>

        <!-- Botón a la derecha (SPA) -->
        <flux:button wire:navigate href="{{ route('projects.create') }}">+ Add New</flux:button>
      </div>
    </div>

    <flux:separator variant="subtle" />

    <div class="stats shadow">
      <div class="stat">
        <div class="stat-figure text-secondary">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
               class="inline-block h-8 w-8 stroke-current">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <div class="stat-title">Total Projects</div>
        <div class="stat-value">31K</div>
        <div class="stat-desc">Jan 1st - Feb 1st</div>
      </div>

      <div class="stat">
        <div class="stat-figure text-secondary">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
               class="inline-block h-8 w-8 stroke-current">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
          </svg>
        </div>
        <div class="stat-title">Completed projects</div>
        <div class="stat-value">4,200</div>
        <div class="stat-desc">↗︎ 400 (22%)</div>
      </div>

      <div class="stat">
        <div class="stat-figure text-secondary">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
               class="inline-block h-8 w-8 stroke-current">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
          </svg>
        </div>
        <div class="stat-title">Pending Projects</div>
        <div class="stat-value">1,200</div>
        <div class="stat-desc">↘︎ 90 (14%)</div>
      </div>
    </div>

    {{-- Tabla (PowerGrid/Livewire) --}}
    <livewire:projects.projects-table />

    {{-- Modal DaisyUI --}}
    @if($this->modalOpen)
      <div class="modal modal-open">
        <div class="modal-box relative">
          <button wire:click="closeModal" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>

          <x-breadcrumbs :items="[
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Projects', 'url' => route('projects.index')],
            ['label' => $projectId ? 'Edit' : 'Create'],
          ]" />

          <!-- Header modal con icono (más compacto) -->
          <div class="flex items-center justify-between">
            <div class="flex items-start gap-3">
              <div class="h-10 w-10 rounded-full bg-primary/10 text-primary ring-1 ring-primary/20 flex items-center justify-center shrink-0">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"
                     class="h-5 w-5" aria-hidden="true">
                  <rect x="3" y="3" width="7" height="18" rx="1"></rect>
                  <path d="M3 9h7M3 13h7M3 17h7"></path>
                  <rect x="14" y="7" width="7" height="14" rx="1"></rect>
                  <path d="M14 11h7M14 15h7M14 19h7"></path>
                </svg>
                <span class="sr-only">Projects</span>
              </div>

              <div>
                <flux:heading size="xl" level="1">{{ __('Projects') }}</flux:heading>
                <flux:subheading size="lg" class="mb-6">
                  {{ $projectId ? __('Update project') : __('Create project') }}
                </flux:subheading>
              </div>
            </div>
          </div>

          {{-- Form (reutiliza tu formulario existente) --}}
          <livewire:projects.projects-formulario
            :projectId="$this->projectId"
            :showBreadcrumbs="false"
            :key="'form-'.$this->projectId"
          />
        </div>
      </div>
    @endif

    {{-- Modal Delete --}}
    @if($confirmingDeleteId)
      <div class="modal modal-open">
        <div class="modal-box">
          <h3 class="font-bold text-lg">Are you sure?</h3>
          <p class="py-4">This project will be moved to trash.</p>
          <div class="modal-action">
            <button wire:click="confirmDelete" class="btn btn-error">Yes, delete</button>
            <button wire:click="$set('confirmingDeleteId', null)" class="btn">Cancel</button>
          </div>
        </div>
      </div>
    @endif

  </div>
</div>
