{{-- resources/views/livewire/projects/project-comments.blade.php --}}
<div class="mt-6 flex flex-col">
  {{-- Flash --}}
  @if (session('comment_ok'))
    <div class="mb-3 rounded-md bg-base-200/40 border border-base-300 px-3 py-2 text-sm">
      {{ session('comment_ok') }}
    </div>
  @endif

  {{-- TIMELINE (scrollable) --}}
  <div class="max-h-[34rem] overflow-y-auto pr-2"
       x-data="{
         scroll() { $nextTick(() => { if ($refs.list) { $refs.list.scrollTop = $refs.list.scrollHeight } }) }
       }"
       x-init="scroll()"
       x-on:comment-added.window="scroll()"
       x-ref="list">

    @php
      // Orden ascendente (lo más nuevo abajo)
      $items = $this->comments->sortBy('created_at');
    @endphp

   <ul class="timeline timeline-vertical timeline-left">
      @forelse($items as $c)
        @php
          $isAuto  = isset($c->is_system) ? (bool)$c->is_system : \Illuminate\Support\Str::startsWith($c->body, '[AUTO]');
          $body    = $isAuto ? (string)\Illuminate\Support\Str::of($c->body)->replaceFirst('[AUTO] ', '') : $c->body;

          $name    = $c->user?->name ?? 'System';
          $stamp   = $c->created_at;                // Carbon
          $pill    = strtoupper($stamp->format('M, Y')); // "MAY, 2025"
          $time    = $stamp->format('M d, Y · H:i');

          // Clases para caja segun tipo
          $boxClasses = $isAuto
            ? 'border-dashed border-warning/40 bg-warning/5 font-mono text-[13px]'
            : 'border-base-300 bg-base-100';
        @endphp

        <li>
          {{-- Fecha en columna izquierda (estilo pastilla) --}}
          <div class="timeline-start w-28 text-right pr-2">
            <span class="inline-flex items-center rounded-full border border-base-300 bg-base-100 px-2 py-0.5 text-[10px] font-medium text-base-content/70">
              {{ $pill }}
            </span>
          </div>

          {{-- Dot del timeline (puedes cambiar color con bg-*) --}}
          <div class="timeline-middle">
            <span class="block h-2.5 w-2.5 rounded-full {{ $isAuto ? 'bg-warning' : 'bg-primary' }}"></span>
          </div>

          {{-- Tarjeta de contenido --}}
          <div class="timeline-end timeline-box p-3 border rounded-lg {{ $boxClasses }}">
            <div class="flex items-center justify-between text-xs text-base-content/70">
              <div class="flex items-center gap-2">
                @if($isAuto)
                  <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded border border-warning/30 bg-warning/10 text-warning text-[11px]">
                    ⚙️ AUTO
                  </span>
                @endif
                <span class="font-medium text-base-content">{{ $name }}</span>
                @if(!empty($c->source))
                  <span class="opacity-60">• {{ $c->source }}</span>
                @endif
              </div>
              <time class="opacity-70" title="{{ $stamp->toIso8601String() }}">{{ $time }}</time>
            </div>

            <div class="mt-1 whitespace-pre-line text-sm text-base-content">
              {{ $body }}
            </div>

            {{-- Acciones: borrar sólo si es del usuario y no es automático --}}
            @if(auth()->id() && $c->user_id === auth()->id() && !$isAuto)
              <div class="mt-2">
                <button class="btn btn-xs btn-ghost text-error"
                        wire:click="deleteComment({{ $c->id }})">
                  Delete
                </button>
              </div>
            @endif
          </div>
        </li>

        {{-- Separador entre items (línea) --}}
        <li><hr/></li>
      @empty
        <li class="text-sm opacity-70 px-2 py-4">No comments yet.</li>
      @endforelse
    </ul>
  </div>

  {{-- Composer --}}
  <div class="mt-4">
    <label class="block text-sm mb-1">Add a comment</label>
    <div class="flex gap-2">
      <textarea
        class="textarea textarea-bordered w-full"
        rows="2"
        wire:model.defer="commentBody"
        placeholder="Write an update... (Ctrl/Cmd + Enter to send)"
        wire:keydown.ctrl.enter="addComment"
        wire:keydown.meta.enter="addComment"
      ></textarea>
      <button class="btn btn-primary" wire:click="addComment" wire:loading.attr="disabled">
        Send
      </button>
    </div>
    @error('commentBody') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
  </div>
</div>
