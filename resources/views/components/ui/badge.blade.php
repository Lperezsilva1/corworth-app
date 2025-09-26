@props(['tone' => 'neutral', 'label' => ''])
@php
$tones = [
  'success' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
  'warning' => 'bg-amber-50 text-amber-700 ring-amber-200',
  'info'    => 'bg-sky-50 text-sky-700 ring-sky-200',
  'neutral' => 'bg-zinc-50 text-zinc-700 ring-zinc-200',
];
$cls = $tones[$tone] ?? $tones['neutral'];
@endphp
<span {{ $attributes->class("px-2 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset $cls") }}>
  {{ $label }}
</span>
