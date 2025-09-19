@props([
    'href' => '#',        // ruta cuando está permitido
    'role' => null,       // ej: 'Admin'   (opcional)
    'can'  => null,       // ej: 'users.viewAny' (opcional)
    'icon' => null,       // SVG opcional (string o slot)
    'label' => null,      // texto del item
])

@php
    $user = auth()->user();
    $allowed = (bool) $user;

    if ($allowed && $role) {
        $allowed = $user->hasRole($role);
    }
    if ($allowed && $can) {
        $allowed = $user->can($can);
    }
@endphp

@if($allowed)
    {{-- Link normal --}}
    <a href="{{ $href }}"
       wire:navigate
       class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 hover:text-zinc-900 transition"
       aria-label="{{ $label ?? $slot }}">
        @if ($icon)
            {!! $icon !!}
        @endif
        <span>{{ $label ?? $slot }}</span>
    </a>
@else
    {{-- Item bloqueado --}}
    <div class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-zinc-400 bg-zinc-50/50 ring-1 ring-inset ring-zinc-200 cursor-not-allowed"
         x-data="{ tip:false }"
         @mouseenter="tip=true" @mouseleave="tip=false"
         aria-label="{{ $label ?? $slot }}"
         aria-disabled="true" role="button" tabindex="-1">

        {{-- Icono (candado + estilo tenue) --}}
        @if ($icon)
            <div class="opacity-50">
                {!! $icon !!}
            </div>
        @endif

        <span>{{ $label ?? $slot }}</span>

        <span class="ml-auto inline-flex items-center gap-1 text-[10px] uppercase tracking-wide opacity-70">
            {{-- Candado inline (Heroicon-style) --}}
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 class="h-4 w-4" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="4" y="11" width="16" height="9" rx="2"></rect>
                <path d="M8 11V8a4 4 0 1 1 8 0v3"></path>
            </svg>
            
        </span>

        {{-- Tooltip simple con Alpine (opcional) --}}
        <div x-show="tip" x-transition
             class="absolute translate-y-8 rounded-md bg-zinc-800 text-white text-xs px-2 py-1 shadow"
             style="margin-left: 2rem">
           No permission. Please contact the administrator.
        </div>
    </div>
@endif
