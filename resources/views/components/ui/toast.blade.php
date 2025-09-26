@props(['type' => 'success', 'message' => null, 'autohideMs' => 3500, 'top' => 'top-4'])
@php
$palette = $type === 'error'
  ? ['border'=>'border-red-500/30','bg'=>'bg-red-500/10','text'=>'text-red-700','ms'=>4000,'icon'=>'⚠️']
  : ['border'=>'border-green-500/30','bg'=>'bg-green-500/10','text'=>'text-green-700','ms'=>$autohideMs,'icon'=>'✅'];
@endphp
<div x-data="{ show: {{ $message ? 'true' : 'false' }}, msg: @js($message) }"
     x-init="if (show) setTimeout(() => show = false, {{ $palette['ms'] }})"
     x-show="show" x-transition.opacity
     class="fixed {{ $top }} left-1/2 -translate-x-1/2 z-50" style="display:none">
  <div class="rounded-md border {{ $palette['border'] }} {{ $palette['bg'] }} {{ $palette['text'] }} px-4 py-2 shadow-lg backdrop-blur">
    <div class="flex items-center gap-3">
      <span>{{ $palette['icon'] }} <span x-text="msg"></span></span>
      <button class="btn btn-xs btn-ghost" @click="show = false">Close</button>
    </div>
  </div>
</div>
