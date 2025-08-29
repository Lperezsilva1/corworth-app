@props(['items' => []])

<nav class="text-sm mb-4" aria-label="Breadcrumb">
  <ol class="flex flex-wrap items-center gap-2 text-base-content/70">
    @foreach ($items as $i => $item)
      @php
        $isLast = $i === count($items) - 1;
        $label  = $item['label'] ?? '';
        $url    = $item['url']   ?? null;
      @endphp

      @if ($url && !$isLast)
        <li>
          <a wire:navigate href="{{ $url }}" class="hover:underline">
            {{ $label }}
          </a>
        </li>
      @else
        <li class="text-base-content font-medium">
          {{ $label }}
        </li>
      @endif

      @unless($isLast)
        <li aria-hidden="true" class="opacity-60">/</li>
      @endunless
    @endforeach
  </ol>
</nav>
