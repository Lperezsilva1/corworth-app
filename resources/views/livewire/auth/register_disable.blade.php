<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
   public function register(): mixed
{
    $validated = $this->validate([
        'name' => ['required','string','max:255'],
        'email' => ['required','string','lowercase','email','max:255','unique:' . \App\Models\User::class],
        'password' => ['required','string','confirmed', \Illuminate\Validation\Rules\Password::defaults()],
    ]);

    $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);

    $user = \App\Models\User::create($validated);
    event(new \Illuminate\Auth\Events\Registered($user));

    // Admin autenticado: no cambiar sesión y redirigir al índice admin
    if (auth()->check() && auth()->user()->hasRole('admin')) {
        session()->flash('status', 'User created');
        return $this->redirectRoute('admin.users.index', navigate: true);
    }

    // Invitado: iniciar sesión y redirigir a dashboard (según el test)
    \Illuminate\Support\Facades\Auth::login($user);
    return $this->redirectRoute('dashboard', navigate: true);
}
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header
        :title="__('Create an account')"
        :description="__('Enter your details below to create your account')"
    />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="register" class="flex flex-col gap-6">
        <!-- Name -->
        <flux:input
            wire:model="name"
            :label="__('Name')"
            type="text"
            required
            autofocus
            autocomplete="name"
            :placeholder="__('Full name')"
        />

        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            required
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="__('Password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Password')"
            viewable
        />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            :label="__('Confirm password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Confirm password')"
            viewable
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Create account') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span>{{ __('Already have an account?') }}</span>
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>
