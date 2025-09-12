<div class="p-6">
  <div class="overflow-x-auto">
    <div class="relative mb-6 w-full">
      {{-- Breadcrumbs --}}
      <x-breadcrumbs :items="[
        ['label' => 'Home', 'url' => url('/dashboard')],
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

    {{-- ===== STATS DINÁMICOS ===== --}}
    <div class="stats shadow" wire:poll.30s>
      <!-- Total -->
      <div class="stat">
        <div class="stat-figure text-secondary">
          <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-8 w-8 stroke-current" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div class="stat-title">Total Projects</div>
        <div class="stat-value">{{ number_format($this->stats['total'] ?? 0) }}</div>
        <div class="stat-desc">All projects</div>
      </div>

      <!-- Pending -->
      <div class="stat">
        <div class="stat-figure text-secondary">
          <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-8 w-8 stroke-current" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3M12 6a9 9 0 100 18 9 9 0 000-18z" />
          </svg>
        </div>
        <div class="stat-title">Pending</div>
        <div class="stat-value">{{ number_format($this->stats['pending'] ?? 0) }}</div>
        <div class="stat-desc">Not started</div>
      </div>

      <!-- Working -->
      <div class="stat">
        <div class="stat-figure text-secondary">
          <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-8 w-8 stroke-current" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 7h18M6 10h12M9 13h6M7 17h10" />
          </svg>
        </div>
        <div class="stat-title">Working</div>
        <div class="stat-value">{{ number_format($this->stats['working'] ?? 0) }}</div>
        <div class="stat-desc">In progress</div>
      </div>

      <!-- Approved -->
      <div class="stat">
        <div class="stat-figure text-secondary">
          <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-8 w-8 stroke-current" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <div class="stat-title">Approved</div>
        <div class="stat-value">{{ number_format($this->stats['approved'] ?? 0) }}</div>
        <div class="stat-desc">Final state</div>
      </div>
    </div>
    {{-- ===== /STATS DINÁMICOS ===== --}}
    <flux:separator variant="subtle" />
    {{-- Tabla (PowerGrid/Livewire) --}}
    <livewire:projects.projects-table />


    {{-- Modal DaisyUI (Create/Edit) --}}
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

    {{-- Modal Delete con confirmación por texto + motivo opcional --}}
  {{-- Modal Delete --}}
{{-- Modal Delete --}}
@if($confirmingDeleteId)
  <div class="modal modal-open">
    <div class="modal-box"
         x-data="{
           text: @entangle('confirmDeleteText').live,
           expected: @js($confirmingDeleteName),
           reason: @entangle('deleteReason').live
         }">

      <h3 class="font-bold text-lg">Delete project</h3>

      <div class="py-3 space-y-3 text-sm">
        <p>This action will move the project to the trash.</p>

        {{-- Nombre esperado --}}
        <p>Type the exact project name to confirm:</p>
        <div class="rounded-lg bg-base-200/60 px-3 py-2 text-xs">
          <span class="opacity-70">Expected name:</span>
          <span class="ml-2 font-mono font-semibold">{{ $confirmingDeleteName }}</span>
        </div>

        {{-- Input de confirmación --}}
        <input type="text"
               class="input input-bordered w-full"
               placeholder="Type the exact project name…"
               x-model="text" />
        @error('confirmDeleteText')
          <p class="text-error text-xs mt-1">{{ $message }}</p>
        @enderror

        {{-- Razón opcional --}}
        <div>
          <label class="label"><span class="label-text">Reason (optional)</span></label>
          <select class="select select-bordered w-full md:max-w-xs" x-model="reason">
            <option value="">— Select reason —</option>
            @foreach($deleteReasonOptions as $opt)
              <option value="{{ $opt }}">{{ ucfirst(str_replace('_',' ', $opt)) }}</option>
            @endforeach
          </select>
          @error('deleteReason')
            <p class="text-error text-xs mt-1">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <div class="modal-action">
        {{-- Botón habilitado solo cuando el texto coincide --}}
        <button class="btn btn-error"
                :disabled="text.trim().toLowerCase() !== expected.toLowerCase()"
                wire:click="confirmDelete">
          Yes, delete
        </button>
        <button wire:click="cancelDelete" class="btn">Cancel</button>
      </div>
    </div>
  </div>
@endif



  </div>
</div>
