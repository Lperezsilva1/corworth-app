@props([
  // clave lógica para colorear el estado (slug en minúsculas)
  'key'   => null,     // ej: 'working', 'waiting', 'approved', 'rejected', 'pfs_approval'
  // etiqueta visible
  'label' => null,     // ej: 'Working', 'Waiting', 'PFS approval'
  // tamaño: xs | sm
  'size'  => 'xs',
  // estilo: solid | soft | outline
  'variant' => 'soft',
  // mostrar puntico al inicio
  'dot' => true,
])

@php
  $k = strtolower((string) $key);

  // Paleta por estado (ajústalo a tus claves reales)
  $map = [
    'working'        => ['bg'=>'bg-indigo-100', 'text'=>'text-indigo-700', 'border'=>'border-indigo-200', 'dot'=>'bg-indigo-500'],
    'in_progress'    => ['bg'=>'bg-indigo-100', 'text'=>'text-indigo-700', 'border'=>'border-indigo-200', 'dot'=>'bg-indigo-500'],
    'pending'        => ['bg'=>'bg-amber-100',  'text'=>'text-amber-800',  'border'=>'border-amber-200',  'dot'=>'bg-amber-500'],
    'waiting'        => ['bg'=>'bg-amber-100',  'text'=>'text-amber-800',  'border'=>'border-amber-200',  'dot'=>'bg-amber-500'],
    'pfs_approval'   => ['bg'=>'bg-amber-100',  'text'=>'text-amber-800',  'border'=>'border-amber-200',  'dot'=>'bg-amber-500'],
    'approved'       => ['bg'=>'bg-emerald-100','text'=>'text-emerald-800','border'=>'border-emerald-200','dot'=>'bg-emerald-500'],
    'rejected'       => ['bg'=>'bg-rose-100',   'text'=>'text-rose-800',   'border'=>'border-rose-200',   'dot'=>'bg-rose-500'],
    'on_hold'        => ['bg'=>'bg-zinc-200',   'text'=>'text-zinc-800',   'border'=>'border-zinc-300',   'dot'=>'bg-zinc-500'],
    'default'        => ['bg'=>'bg-base-200',   'text'=>'text-base-content/70','border'=>'border-base-300','dot'=>'bg-base-content/50'],
  ];
  $c = $map[$k] ?? $map['default'];

  $sizeCls = $size === 'sm'
    ? 'text-[11px] leading-[16px] px-2 py-[1px]'
    : 'text-[10px] leading-[14px] px-1.5 py-[1px]'; // xs por defecto

  $base = 'inline-flex items-center gap-1 rounded-full whitespace-nowrap';

  $variantCls = match($variant) {
    'solid'   => "{$c['text']} {$c['dot']}/10 text-white px-2 py-[2px] bg-opacity-90",
    'outline' => "{$c['text']} border {$c['border']} bg-transparent",
    default   => "{$c['text']} {$c['bg']} border {$c['border']}",
  };

  $dotCls = "w-1.5 h-1.5 rounded-full ".($map[$k]['dot'] ?? $map['default']['dot']);
@endphp

<span class="{{ $base }} {{ $sizeCls }} {{ $variantCls }}">
  @if($dot)
    <span class="{{ $dotCls }}"></span>
  @endif
  <span class="font-medium">{{ $label ?? ucfirst($k) }}</span>
</span>
