<div>
 {{-- resources/views/livewire/notifications/bell.blade.php --}}
<div class="relative" x-data="{open:false}" @keydown.escape.window="open=false">
<flux:button variant="ghost"  @click="open = !open" class="relative">
    {{-- Icono SVG campana --}}
    <svg xmlns="http://www.w3.org/2000/svg"
         class="h-6 w-6"
         viewBox="0 0 24 24" fill="none"
         stroke="currentColor" stroke-width="1.8">
        <path d="M6 8a6 6 0 1 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
        <path d="M10 22h4"/>
    </svg>

    {{-- Badge indicador --}}
    @if($this->unreadCount > 0)
        <flux:badge
            color="primary"
            size="sm"
            class="absolute -top-1 -right-1"
        >
            {{ $this->unreadCount }}
        </flux:badge>
    @endif
</flux:button>

  <div x-show="open" x-transition @click.outside="open=false"
       class="absolute right-0 mt-2 w-96 max-w-[95vw] bg-base-100 border border-base-300 rounded-xl shadow-xl z-[60]">
    <div class="p-3 flex items-center justify-between border-b border-base-300">
      <div class="font-semibold">Notifications</div>
      <button type="button" class="btn btn-ghost btn-xs" wire:click="markAllAsRead" @click="open=true">Mark all read</button>
    </div>

    <ul class="max-h-[60vh] overflow-y-auto divide-y divide-base-300">
      @forelse($this->latestNotifications as $n)
        @php
          $data = $n->data ?? [];
          $isUnread = is_null($n->read_at);
          $title = $data['type'] === 'comment_added'
            ? 'New comment on ' . ($data['project_name'] ?? 'project')
            : ($data['title'] ?? 'Notification');
          $desc = $data['body'] ?? '';
          $url  = $data['url'] ?? '#';
          $actor= $data['actor_name'] ?? null;
        @endphp
        <li class="p-3 hover:bg-base-200/50">
          <div class="flex items-start gap-3">
            <div class="mt-0.5">
              <div class="w-2.5 h-2.5 rounded-full {{ $isUnread ? 'bg-primary' : 'bg-base-300' }}"></div>
            </div>
            <div class="min-w-0">
              <div class="text-sm font-medium">
                {{ $title }}
                @if($actor) <span class="opacity-60 font-normal">• {{ $actor }}</span> @endif
              </div>
              @if($desc) <div class="text-xs opacity-80 line-clamp-2">{{ $desc }}</div> @endif
              <div class="mt-2 flex gap-2">
                <a href="{{ $url }}" class="btn btn-xs" wire:click="markAsRead('{{ $n->id }}')">Open</a>
                @if($isUnread)
                  <button type="button" class="btn btn-xs btn-ghost"
                          wire:click="markAsRead('{{ $n->id }}')">Mark read</button>
                @endif
              </div>
              <div class="text-[11px] opacity-60 mt-1">{{ $n->created_at->diffForHumans() }}</div>
            </div>
          </div>
        </li>
      @empty
        <li class="p-4 text-sm opacity-70">No notifications yet.</li>
      @endforelse
    </ul>
  </div>
</div>

</div>
