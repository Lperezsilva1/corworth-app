@props([
    // Ejemplo: [['label'=>'Home','url'=>route('dashboard')], ['label'=>'Sellers']]
    'items' => [],
    'showHomeIcon' => true,
])

<flux:breadcrumbs {{ $attributes->class(['mb-4']) }}>
    @foreach($items as $i => $item)
        @php
            $label   = $item['label'] ?? null;
            $url     = $item['url']   ?? null;
            $isLast  = $i === count($items) - 1;
        @endphp

        @if($label)
            {{-- Ítems con enlace (no es el último) --}}
            @if(!$isLast && $url)
                <flux:breadcrumbs.item href="{{ $url }}" wire:navigate>
                    @if($showHomeIcon && $i === 0)
                        <svg class="inline-block -mt-px mr-1 h-4 w-4 opacity-80"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 10.5L12 3l9 7.5"></path>
                            <path d="M5 10.5V21h14V10.5"></path>
                        </svg>
                    @endif
                    {{ $label }}
                </flux:breadcrumbs.item>
            @else
                {{-- Último ítem o sin URL --}}
                <flux:breadcrumbs.item aria-current="page">
                    @if($showHomeIcon && $i === 0)
                        <svg class="inline-block -mt-px mr-1 h-4 w-4 opacity-80"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 10.5L12 3l9 7.5"></path>
                            <path d="M5 10.5V21h14V10.5"></path>
                        </svg>
                    @endif
                    {{ $label }}
                </flux:breadcrumbs.item>
            @endif
        @endif
    @endforeach

    {{-- Permite usar directamente slots en vez de :items --}}
    {{ $slot }}
</flux:breadcrumbs>
