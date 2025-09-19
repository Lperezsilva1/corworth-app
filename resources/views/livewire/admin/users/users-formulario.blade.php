{{-- resources/views/livewire/admin/users/users-formulario.blade.php --}}
<div>
  <div class="p-6">
    <div class="overflow-x-auto">
      @if ($showBreadcrumbs)
        <div class="relative mb-6 w-full">
          {{-- Breadcrumbs --}}
          <x-breadcrumbs :items="[
            ['label' => 'Home', 'url' => url('/dashboard')],
            ['label' => 'Users', 'url' => route('admin.users.index')],
            ['label' => $userId ? 'Edit user' : 'New user']
          ]" />

          {{-- Título --}}
          <div class="flex items-center justify-between">
            <div class="flex items-start gap-3">
              <div class="h-12 w-12 rounded-full bg-primary/10 text-primary ring-1 ring-primary/20 flex items-center justify-center shrink-0">
                {{-- Icono usuarios --}}
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
                <flux:heading size="xl" level="1">
                  {{ $userId ? __('Edit user') : __('User') }}
                </flux:heading>
                <flux:subheading size="lg" class="mb-6">
                  {{ $userId ? __('Update user data') : __('Create new user') }}
                </flux:subheading>
              </div>
            </div>
          </div>
        </div>

        <flux:separator variant="subtle" />
      @endif

      {{-- Contenedor del formulario --}}
      <div class="p-6 max-w-3xl">
        @include('livewire.admin.users.partials.form')

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

  {{-- 🔔 Listener de toasts (no empuja layout) --}}
  <div x-data
       x-on:notify.window="
          const t = document.createElement('div');
          t.className = 'toast toast-top toast-end fixed z-[9999]';
          t.innerHTML = `<div class='alert alert-success'>${$event.detail}</div>`;
          document.body.appendChild(t);
          setTimeout(() => t.remove(), 3000);
       ">
  </div>
</div>
