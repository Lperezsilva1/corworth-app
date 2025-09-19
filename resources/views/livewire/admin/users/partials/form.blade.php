{{-- resources/views/livewire/admin/users/partials/_form.blade.php --}}
@php
  /** @var ?int $userId viene del componente */
  $editing = (bool) $userId; // true si editas, false si creas
@endphp

<form wire:submit.prevent="save" class="space-y-5">
  {{-- Name --}}
  <div>
    <label class="block text-sm font-medium mb-1">Name</label>
    <input
      type="text"
      class="input input-bordered w-full bg-transparent focus:bg-transparent"
      wire:model.defer="name"
      placeholder="Full name"
      required
      autocomplete="name"
    >
    @error('name')
      <p class="text-error text-sm mt-1">{{ $message }}</p>
    @enderror
  </div>

  {{-- Email --}}
  <div>
    <label class="block text-sm font-medium mb-1">Email address</label>
    <input
      type="email"
      class="input input-bordered w-full bg-transparent focus:bg-transparent"
      wire:model.defer="email"
      placeholder="email@example.com"
      required
      autocomplete="email"
    >
    @error('email')
      <p class="text-error text-sm mt-1">{{ $message }}</p>
    @enderror
  </div>

  {{-- Password + Confirm --}}
  @if (!$editing)
    {{-- Solo al crear --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div>
        <label class="block text-sm font-medium mb-1">Password</label>
        <input
          type="password"
          class="input input-bordered w-full bg-transparent focus:bg-transparent"
          wire:model.defer="password"
          placeholder="********"
          required
          autocomplete="new-password"
        >
        @error('password')
          <p class="text-error text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Confirm password</label>
        <input
          type="password"
          class="input input-bordered w-full bg-transparent focus:bg-transparent"
          wire:model.defer="password_confirmation"
          placeholder="********"
          required
          autocomplete="new-password"
        >
      </div>
    </div>
  @else
    {{-- En edición: opcional (desplegable) --}}
    <div x-data="{ open: false }" class="space-y-2">
      <button type="button" class="link link-primary text-sm" x-on:click="open = !open">
        <span x-show="!open">Change password</span>
        <span x-show="open">Cancel password change</span>
      </button>

      <div x-show="open" x-transition x-cloak>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-medium mb-1">New password (optional)</label>
            <input
              type="password"
              class="input input-bordered w-full bg-transparent focus:bg-transparent"
              wire:model.defer="password"
              placeholder="********"
              autocomplete="new-password"
            >
            @error('password')
              <p class="text-error text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Confirm new password</label>
            <input
              type="password"
              class="input input-bordered w-full bg-transparent focus:bg-transparent"
              wire:model.defer="password_confirmation"
              placeholder="********"
              autocomplete="new-password"
            >
          </div>
        </div>
      </div>
    </div>
  @endif

  {{-- Roles --}}
  <div>
    <label class="block text-sm font-medium mb-2">Roles</label>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
      @forelse ($availableRoles as $roleName)
        <label class="flex items-center gap-2 text-sm">
          <input
            type="checkbox"
            class="checkbox checkbox-sm"
            value="{{ $roleName }}"
            wire:model.defer="rolesSelected"
          >
          <span>{{ $roleName }}</span>
        </label>
      @empty
        <p class="text-sm text-gray-500">No roles defined.</p>
      @endforelse
    </div>
    @error('rolesSelected.*')
      <p class="text-error text-sm mt-1">{{ $message }}</p>
    @enderror
  </div>

  {{-- Actions --}}
  <div class="flex items-center justify-end gap-3 pt-2">
    <a wire:navigate href="{{ route('admin.users.index') }}" class="btn btn-ghost">Cancel</a>
    <button type="submit" class="btn btn-primary btn-active">
      {{ $editing ? __('Save changes') : __('Create account') }}
    </button>
  </div>
</form>
