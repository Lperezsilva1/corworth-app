<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" >
  <head>
    @include('partials.head')

   
    @livewireStyles
  </head>
  <body class="min-h-screen bg-white dark:bg-zinc-800">
  
    {{-- ===================== SIDEBAR ===================== --}}
    <flux:sidebar
      sticky
      stashable
      class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900"
    >
      <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

      <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
        <x-app-logo />
      </a>

      <flux:navlist variant="outline">
        <flux:navlist.group :heading="__('Platform')" class="grid">
          <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate  class="{{ request()->routeIs('dashboard') ? 'nav-active' : '' }}">
            {{ __('Dashboard') }}
          </flux:navlist.item>

          <flux:navlist.item icon="calendar" :href="route('projects.index')" :current="request()->routeIs('projects.index')" wire:navigate class="{{ request()->routeIs('projects.index') ? 'nav-active' : '' }}">
            Projects
          </flux:navlist.item>
        </flux:navlist.group>

        <flux:navlist.group expandable heading="Master" class="hidden lg:grid">
          <flux:navlist.item :href="route('drafters.index')" :current="request()->routeIs('drafters.index')" wire:navigate class="{{ request()->routeIs('drafters.index') ? 'nav-active' : '' }}">
            Drafters
          </flux:navlist.item>
          <flux:navlist.item :href="route('sellers.index')" :current="request()->routeIs('sellers.index')" wire:navigate class="{{ request()->routeIs('sellers.index') ? 'nav-active' : '' }}">
            Sellers
          </flux:navlist.item>
          <flux:navlist.item :href="route('buildings.index')" :current="request()->routeIs('buildings.index')" wire:navigate class="{{ request()->routeIs('buildings.index') ? 'nav-active' : '' }}">
            Models
          </flux:navlist.item>
        </flux:navlist.group>
      </flux:navlist>

      <flux:spacer />

      <flux:navlist variant="outline">
        <flux:navlist.item icon="folder-git-2" :href="route('activity.index')" :current="request()->routeIs('activity.index')" wire:navigate>
          {{ __('Activity') }}
        </flux:navlist.item>
      </flux:navlist>
    </flux:sidebar>

    {{-- ===================== HEADER MÓVIL (solo mobile) ===================== --}}
    <flux:header class="lg:hidden sticky top-0 z-50 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700 overflow-visible">
      <div class="h-14 flex items-center gap-3 px-4">
        {{-- Toggle sidebar en mobile --}}
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <div class="flex-1"></div>

        {{-- Campana --}}
        @auth
          @livewire('notifications.bell')
        @endauth

        {{-- Perfil --}}
        <flux:dropdown position="bottom" align="end">
          <flux:profile
            :name="auth()->user()->name"
            :initials="auth()->user()->initials()"
            icon:trailing="chevrons-up-down"
          />
          <flux:menu class="w-[220px]">
            <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>Settings</flux:menu.item>
            <flux:menu.separator />
            <form method="POST" action="{{ route('logout') }}" class="w-full">
              @csrf
              <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                Log Out
              </flux:menu.item>
            </form>
          </flux:menu>
        </flux:dropdown>
      </div>
    </flux:header>

 {{-- ===================== HEADER DESKTOP (solo desktop) ===================== --}}

<flux:header class="hidden lg:block sticky top-0 z-50 bg-white dark:bg-zinc-900 
                  border-b border-zinc-200 dark:border-zinc-700 lg:pl-64 overflow-visible">
                  
  <div class="h-14 flex items-center gap-3 px-6">

    {{-- Acciones a la derecha (buscador + campana + perfil) --}}
    <div class="ml-auto flex items-center gap-3">
      {{-- 🔍 Buscador global --}}
      <div class="">
        @livewire('global-search', [], key('global-search-header'))
      </div>

      {{-- 🔔 Notificaciones --}}
      @auth
        @livewire('notifications.bell')
      @endauth

      {{-- 👤 Perfil --}}
      <flux:dropdown position="bottom" align="end">
        <flux:profile
          :name="auth()->user()->name"
          :initials="auth()->user()->initials()"
          icon:trailing="chevrons-up-down"
        />
        <flux:menu class="w-[220px]">
          <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>Settings</flux:menu.item>
          <flux:menu.item :href="route('admin.users.index')" icon="users" wire:navigate>Admin → Users</flux:menu.item>
           <flux:menu.item :href="route('settings.roles-permissions')" icon="users" wire:navigate>Permission</flux:menu.item>
           <flux:menu.item :href="route('settings.roles')" icon="users" wire:navigate>Roles</flux:menu.item>
           <flux:menu.item :href="route('settings.assign-roles')" icon="users" wire:navigate>Assign Roles</flux:menu.item>
           
      <flux:navlist.item :href="route('settings.rbac')" wire:navigate>
    Roles & Permissions
  </flux:navlist.item>

          <flux:menu.separator />
          <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
              Log Out
            </flux:menu.item>
          </form>
        </flux:menu>
      </flux:dropdown>
    </div>
  </div>
</flux:header>
    {{-- ===================== CONTENIDO ===================== --}}
    {{ $slot }}


    
    @livewireScripts
        @fluxScripts
    {{-- ===================== TOAST GLOBAL ===================== --}}
    <div
      x-data="{ show: false, message: '' }"
      x-on:notify.window="message = $event.detail; show = true; setTimeout(() => show = false, 7000)"
      class="fixed top-4 right-4 z-50"
    >
      <div
        x-show="show"
        x-transition
        class="mb-4 rounded-md bg-green-500/10 text-green-700 px-4 py-2 text-sm border border-green-500/30"
      >
        <span x-text="message"></span>
      </div>
    </div>
  </body>
  <script>
  // Monta Flux al cargar y después de cada render de Livewire
  document.addEventListener('livewire:load', () => {
    window?.Flux?.mount?.();
  });
  document.addEventListener('livewire:navigated', () => {
    window?.Flux?.mount?.();
  });
  // Cuando Livewire actualiza el DOM del componente actual
  document.addEventListener('livewire:initialized', () => {
    Livewire.hook('message.processed', () => {
      window?.Flux?.mount?.();
    });
  });
</script>
<style>
  /* 1) Quita padding vertical del contenedor principal de Flux */
  [data-flux-container] [data-flux-main]{
    padding-top: 0 !important;
    padding-bottom: 0 !important;
  }

  /* 2) Quita padding vertical del PRIMER hijo que trae p-6 */
  [data-flux-main] > .p-6{
    padding-top: 0 !important;
    padding-bottom: 0 !important;
  }

  /* 3) En desktop, anula también lg:p-8 del hijo (Tailwind la genera como .lg\:p-8) */
  @media (min-width: 1024px){
    [data-flux-main] > .lg\:p-8{
      padding-top: 0 !important;
      padding-bottom: 0 !important;
    }
  }
</style>
</html>
