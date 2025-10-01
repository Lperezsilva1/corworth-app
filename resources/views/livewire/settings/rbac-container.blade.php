<div class="p-6">
  <div class="overflow-x-auto">
    <div class="relative mb-6 w-full">
      {{-- Breadcrumbs --}}
      <x-breadcrumbs :items="[
        ['label' => 'Home', 'url' => url('/dashboard')],
        ['label' => 'Roles and Permissions']
      ]" />

      {{-- Título + subtítulo (izquierda) --}}
      <div class="flex items-center justify-between">
        <div>
          <flux:heading size="xl" level="1">{{ __('Roles & Permissions') }}</flux:heading>
          <flux:subheading size="lg" class="mb-6">{{ __('Access control') }}</flux:subheading>
        </div>
        {{-- (Opcional) acciones a la derecha --}}
      </div>
    </div>

    <flux:separator variant="subtle" />

    {{-- 🔧 Antes: mx-auto max-w-6xl (centraba). Ahora: w-full (alineado a la izquierda) --}}
    <div class="w-full py-8">
      <div class="flex items-start max-md:flex-col">
        {{-- Sidebar --}}
        <div class="me-10 w-full pb-4 md:w-[220px]">
          <flux:navlist>
            <flux:navlist.item
              :href="route('settings.rbac', ['tab' => 'roles'])"
              wire:navigate
              :current="$tab==='roles'">
              Roles
            </flux:navlist.item>

            <flux:navlist.item
              :href="route('settings.rbac', ['tab' => 'permissions'])"
              wire:navigate
              :current="$tab==='permissions'">
              Permissions
            </flux:navlist.item>

            <flux:navlist.item
              :href="route('settings.rbac', ['tab' => 'assign'])"
              wire:navigate
              :current="$tab==='assign'">
              Assign roles
            </flux:navlist.item>
          </flux:navlist>
        </div>

        <flux:separator class="md:hidden" />

        {{-- Contenido --}}
        <div class="flex-1 self-stretch max-md:pt-6 min-w-0">
         
          
          {{-- Mantén ancho completo; si quieres limitar en "assign" deja el max-w-lg --}}
          <div class=" w-full {{ $tab==='assign' ? 'max-w-lg' : '' }}">
            @switch($tab)
              @case('roles')
                <livewire:settings.roles-index />
                @break

              @case('permissions')
                <livewire:settings.roles-permissions />
                @break

              @case('assign')
                <livewire:settings.assign-roles />
                @break
            @endswitch
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
