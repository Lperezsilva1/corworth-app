@props(['key' => null, 'label' => null])

@php
  $key   = $key ?? 'pending';
  $label = $label ?? ucfirst($key);

  [$icon, $color] = match($key) {
    'complete', 'approved'       => ['✅', 'text-success'],
    'working'                    => ['⏳', 'text-info'],
    'awaiting_approval'          => ['⏳', 'text-warning'],
    'cancelled'                  => ['❌', 'text-error'],
    default                      => ['⚪', 'text-base-content/60'],
  };
@endphp
<div class="text-xs {{ $color }}">{{ $icon }} {{ $label }}</div>
