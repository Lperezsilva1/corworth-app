@props(['key' => null, 'label' => null, 'size' => 'md'])

@php
  $key   = $key   ?? 'pending';
  $label = $label ?? ucfirst($key);

  // color del texto/borde (Daisy) + color del puntito (bg-*)
  [$badgeClass, $dotClass] = match ($key) {
    'approved', 'complete'       => ['text-success border-success/40', 'bg-success'],
    'working'                    => ['text-info border-info/40',       'bg-info'],
    'awaiting_approval'          => ['text-warning border-warning/40', 'bg-warning'],
    'cancelled'                  => ['text-error border-error/40',     'bg-error'],
    default                      => ['text-base-content/70 border-base-300', 'bg-base-content/50'],
  };

  // tamaño: md por defecto, puedes pasar "sm" o "lg"
  $pad = match ($size) { 'sm' => 'px-2 py-1 text-xs', 'lg' => 'px-4 py-2 text-sm', default => 'px-3 py-1.5 text-sm' };
@endphp

<span class="inline-flex items-center gap-2 rounded-full bg-base-100 badge badge-outline {{ $pad }} {{ $badgeClass }}">
  <span class="inline-block w-2 h-2 rounded-full {{ $dotClass }}"></span>
  <span class="font-medium">{{ $label }}</span>
</span>
