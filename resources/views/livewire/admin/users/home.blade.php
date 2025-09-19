<div class="p-6 space-y-6">
  <div class="overflow-x-auto">
    <div class="relative w-full">

      {{-- Breadcrumbs --}}
      <x-breadcrumbs :items="[
        ['label' => 'Home', 'url' => url('/dashboard')],
        ['label' => 'Users']
      ]" />

      {{-- Título + botón Crear (vista aparte, no modal) --}}
      <div class="mt-2 flex items-center justify-between">
        <div class="flex items-start gap-3">
          <div class="h-12 w-12 rounded-full bg-primary/10 text-primary ring-1 ring-primary/20 flex items-center justify-center shrink-0">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"
                 class="h-6 w-6" aria-hidden="true">
              <circle cx="9" cy="7" r="4"></circle>
              <path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"></path>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            </svg>
            <span class="sr-only">Users</span>
          </div>
          <div>
            <flux:heading size="xl" level="1">{{ __('Users management') }}</flux:heading>
            <flux:subheading size="lg" class="mb-6">{{ __('Manage user') }}</flux:subheading>
          </div>
        </div>

        <flux:button wire:navigate href="{{ route('admin.users.create') }}">
          + New User
        </flux:button>
      </div>
    </div>

    <flux:separator variant="subtle" class="my-4" />

    {{-- Tabla PowerGrid --}}
    <livewire:admin.users.index />
  </div>

  {{-- ========================= --}}
  {{-- Modal DaisyUI SOLO EDITAR --}}
  {{-- ========================= --}}
  @if ($this->showModal)
    <div class="modal modal-open">
      <div class="modal-box w-full max-w-3xl relative" wire:ignore.self>

        {{-- Cabecera: X deshabilitable mientras guarda --}}
        <div class="flex items-center justify-between border-b pb-2 mb-4"
             x-data="{ saving:false }"
             @modal-saving.window="saving = !!($event.detail?.saving)">
          <h2 class="text-lg font-semibold">{{ __('Editar Usuario') }}</h2>

          <button type="button"
                  class="btn btn-sm btn-circle btn-ghost"
                  :class="{ 'btn-disabled opacity-60 pointer-events-none': saving }"
                  :disabled="saving"
                  wire:click="closeUserModal"
                  aria-label="Close modal"
                  title="Close">
            ✕
          </button>
        </div>

        {{-- Formulario hijo (solo si hay ID) --}}
        @if ($this->editingId)
          <livewire:admin.users.users-formulario
            :user-id="$this->editingId"
            :show-breadcrumbs="false"
            wire:key="user-form-{{ $this->editingId }}"
          />
        @endif
      </div>

      {{-- Backdrop: clic para cerrar (se bloquea si saving=true por la X) --}}
      <div class="modal-backdrop">
        <button @click="$wire.closeUserModal()">close</button>
      </div>
    </div>
  @endif
</div>
