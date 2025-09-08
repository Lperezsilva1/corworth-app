<div class="mt-6 flex flex-col">
  {{-- Flash --}}
 @if (session('comment_ok'))
  <div x-data="{show:true}" x-init="setTimeout(()=>show=false,3000)" x-show="show" x-transition
       class="mb-3 rounded-md bg-base-200/40 border border-base-300 px-3 py-2 text-sm">
    {{ session('comment_ok') }}
  </div>
@endif

  {{-- TIMELINE (scrollable) --}}
  <div class="max-h-[34rem] overflow-y-auto pr-2"
       x-data="{ scrollTop() { $nextTick(() => { if ($refs.list) { $refs.list.scrollTop = 0 } }) } }"
       x-init="scrollTop()"
       x-on:comment-added.window="scrollTop()"
       x-ref="list">

    @php
      $items = $this->comments->sortByDesc('created_at'); // más recientes primero
    @endphp

    <ul class="timeline timeline-vertical timeline-left">
      @forelse($items as $c)
        @php
          $isAuto  = isset($c->is_system) ? (bool)$c->is_system : \Illuminate\Support\Str::startsWith($c->body ?? '', '[AUTO]');
          $bodyRaw = $c->body ?? '';
          $body    = $isAuto ? (string)\Illuminate\Support\Str::of($bodyRaw)->replaceFirst('[AUTO] ', '') : $bodyRaw;

          $name    = $c->user?->name ?? 'System';
          $stamp   = $c->created_at;
          $pill    = strtoupper($stamp->format('M, Y'));
          $time    = $stamp->format('M d, Y · H:i');

          $boxClasses = $isAuto
  ? 'border-dashed border-warning/40 bg-warning/5 font-mono text-[13px]'
  : 'border-base-300 bg-base-200/40 dark:bg-base-300/20 shadow-sm transition-colors';

          $title = trim((string)($c->title ?? ''));
        @endphp

        <li>
          <div class="timeline-start w-28 text-right pr-2">
            <span class="inline-flex items-center rounded-full border border-base-300 bg-base-100 px-2 py-0.5 text-[10px] font-medium text-base-content/70">
              {{ $pill }}
            </span>
          </div>

          <div class="timeline-middle">
            <span class="block h-2.5 w-2.5 rounded-full {{ $isAuto ? 'bg-warning' : 'bg-primary' }}"></span>
          </div>

          <div class="timeline-end timeline-box p-3 border rounded-lg {{ $boxClasses }}">
            <div class="flex items-center justify-between text-xs text-base-content/70">
              <div class="flex items-center gap-2">
                @if($isAuto)
                  <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded border border-warning/30 bg-warning/10 text-warning text-[11px]">⚙️ AUTO</span>
                @endif
                <span class="font-medium text-base-content">{{ $name }}</span>
                @if(!empty($c->source))
                  <span class="opacity-60">• {{ $c->source }}</span>
                @endif
              </div>
              <time class="opacity-70" title="{{ $stamp->toIso8601String() }}">{{ $time }}</time>
            </div>

            @if($title !== '')
              <h4 class="mt-2 text-sm font-semibold leading-tight break-words">
                {{ $title }}
              </h4>
            @endif

            @if(!empty($body))
              <div class="mt-1 whitespace-pre-line text-sm text-base-content">
                {{ $body }}
              </div>
            @endif

            {{-- Adjuntos (con visor modal) --}}
           {{-- Adjuntos (con visor modal) --}}
@if($c->attachments->count())
  @php
    // 1) Orden único y consistente:
    $atts = $c->attachments->sortBy('id')->values();

    // 2) Items para el visor:
    $viewerItems = $atts->map(function($att) {
      $mime = (string)($att->mime ?? '');
      $isImage = \Illuminate\Support\Str::startsWith($mime, 'image/');
      $isPdf   = \Illuminate\Support\Str::of($mime)->lower()->exactly('application/pdf')
                || \Illuminate\Support\Str::of((string)$att->original_name)->lower()->endsWith('.pdf');
      return [
        'id'          => $att->id,
        'name'        => $att->original_name,
        'mime'        => $mime,
        'isImage'     => $isImage,
        'isPdf'       => $isPdf,
        'viewUrl'     => route('attachments.view', $att),
        'downloadUrl' => route('attachments.download', $att),
        'thumbUrl'    => $isImage ? route('attachments.view', $att) : null,
      ];
    })->values();

    // 3) Clave para re-montar el viewer cuando haya nuevos adjuntos
    $viewerKey = 'viewer-'.$c->id.'-'.($atts->max('id') ?? 0).'-'.$atts->count();
  @endphp

  <div class="mt-2"
       x-data="viewer({ items: @js($viewerItems) })"
       @keydown.window="onKey($event)"
       wire:key="{{ $viewerKey }}">

    {{-- Grid de miniaturas / tarjetas --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
      @foreach($atts as $att)
        @php $isImage = \Illuminate\Support\Str::startsWith((string)$att->mime, 'image/'); @endphp

        <button type="button"
                class="group relative block"
                @click="openAt({{ $loop->index }})"
                wire:key="att-{{ $c->id }}-{{ $att->id }}">
          @if($isImage)
            <img src="{{ route('attachments.view', $att) }}"
                 alt="{{ $att->original_name }}"
                 class="h-24 w-full object-cover rounded-md border border-base-300 group-hover:opacity-90"
                 loading="lazy">
          @else
            <div class="h-24 w-full rounded-md border border-base-300 flex items-center justify-center p-2 text-xs text-left">
              <div class="w-full">
                <div class="font-medium truncate">{{ $att->original_name }}</div>
                <div class="opacity-60 truncate">{{ $att->mime ?? 'file' }}</div>
                <div class="mt-1 inline-flex items-center gap-1 text-primary underline">Preview</div>
              </div>
            </div>
          @endif
        </button>
      @endforeach
    </div>

    {{-- Modal / Visor --}}
    <dialog x-ref="dialog" class="modal">
      <div class="modal-box max-w-5xl">
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" @click="close()">✕</button>

        <template x-if="current">
          <div>
            <div class="mb-2 flex items-center justify-between gap-3">
              <div class="font-medium text-sm truncate" x-text="current.name"></div>
              <div class="flex gap-2 shrink-0">
                <a class="btn btn-sm" :href="current.downloadUrl">Download</a>
              </div>
            </div>

            <div class="border rounded-md overflow-hidden bg-base-200">
              <template x-if="current.isImage">
                <img :src="current.viewUrl" class="w-full max-h-[70vh] object-contain" />
              </template>

              <template x-if="!current.isImage && current.isPdf">
                <iframe :src="current.viewUrl" class="w-full h-[70vh]" frameborder="0"></iframe>
              </template>

              <template x-if="!current.isImage && !current.isPdf">
                <div class="p-6 text-center">
                  <div class="font-medium mb-2">No preview available.</div>
                  <a class="btn btn-primary btn-sm" :href="current.downloadUrl">Download file</a>
                </div>
              </template>
            </div>

            <div class="mt-3 flex justify-between">
              <button class="btn btn-sm" @click.prevent="prev()">◀ Prev</button>
              <button class="btn btn-sm" @click.prevent="next()">Next ▶</button>
            </div>
          </div>
        </template>
      </div>
      <div class="modal-backdrop" @click="close()"></div>
    </dialog>
  </div>
@endif


            {{-- Acciones --}}
            @if(auth()->id() && $c->user_id === auth()->id() && !$isAuto)
              <div class="mt-2">
                <button class="btn btn-xs btn-ghost text-error" wire:click="deleteComment({{ $c->id }})">Delete</button>
              </div>
            @endif
          </div>
        </li>

        <li><hr/></li>
      @empty
        <li class="text-sm opacity-70 px-2 py-4">No comments yet.</li>
      @endforelse
    </ul>
  </div>

  {{-- Composer (modal) --}}
  <div class="mt-6" x-data="composer()" x-on:comment-added.window="close()">
    <div class="flex justify-end">
      <button class="btn btn-primary" @click="open()">Add comment</button>
    </div>

    {{-- Modal: IGNORAR re-render del propio dialog --}}
    <dialog x-ref="dialog" class="modal" wire:ignore.self>
      <div class="modal-box max-w-2xl" @click.stop>
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" @click="close()">✕</button>

        <h3 class="text-lg font-semibold mb-4">New comment</h3>

        {{-- Title --}}
        <label class="block text-xs font-medium mb-1 opacity-70">Title</label>
        <input
          x-ref="title"
          type="text"
          class="input input-bordered w-full mb-3"
          placeholder="Short, descriptive title"
          wire:model.defer="commentTitle"
          wire:keydown.ctrl.enter="addComment"
          wire:keydown.meta.enter="addComment"
        />
        @error('commentTitle') <p class="text-error text-xs mb-2">{{ $message }}</p> @enderror

        {{-- Body --}}
        <label class="block text-xs font-medium mb-1 opacity-70">Details</label>
        <textarea
          class="textarea textarea-bordered w-full min-h-28"
          wire:model.defer="commentBody"
          placeholder="Describe the update… (Ctrl/Cmd + Enter to send)"
          wire:keydown.ctrl.enter="addComment"
          wire:keydown.meta.enter="addComment"
        ></textarea>
        @error('commentBody') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror

        {{-- Files + progress --}}
        <div class="mt-4"
             x-data="{progress:0}"
             x-on:livewire-upload-start="progress = 0"
             x-on:livewire-upload-progress="progress = $event.detail.progress"
             x-on:livewire-upload-finish="progress = 0"
             x-on:livewire-upload-error="progress = 0">
          <label class="block text-xs font-medium mb-1 opacity-70">Attachments</label>
          <input type="file" multiple wire:model="uploads" class="file-input file-input-bordered file-input-sm w-full" />
          @error('uploads.*') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror

          <div class="mt-2" x-show="progress > 0">
            <progress max="100" x-bind:value="progress" class="progress progress-primary w-full"></progress>
          </div>

          @if(!empty($uploads))
            <ul class="mt-2 text-xs opacity-80 space-y-1">
              @foreach($uploads as $f)
                <li class="flex items-center gap-2">
                  📎 <span class="truncate">{{ $f->getClientOriginalName() }}</span>
                  <span class="opacity-60">({{ number_format($f->getSize()/1024, 1) }} KB)</span>
                </li>
              @endforeach
            </ul>
          @endif
        </div>

        <div class="mt-5 flex justify-end gap-2">
          <button class="btn" @click="close()">Cancel</button>
         <button class="btn btn-primary"
            wire:click="addComment"
            wire:loading.attr="disabled"
            wire:target="addComment,uploads">
              Send
            </button>
        </div>
      </div>
      <div class="modal-backdrop" @click="close()"></div>
    </dialog>
  </div>
</div>

{{-- Script del visor (puede ir aquí o en tu layout) --}}
<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('viewer', ({ items = [] }) => ({
      items,
      open: false,
      idx: 0,
      get current() { return this.items[this.idx] || null },
      openAt(i) { this.idx = i; this.open = true; this.$nextTick(() => this.$refs.dialog?.showModal?.()); },
      close()   { this.open = false; this.$refs.dialog?.close?.(); },
      next()    { if (!this.items.length) return; this.idx = (this.idx + 1) % this.items.length; },
      prev()    { if (!this.items.length) return; this.idx = (this.idx - 1 + this.items.length) % this.items.length; },
      onKey(e)  { if (!this.open) return; if (e.key === 'Escape') this.close(); if (e.key === 'ArrowRight') this.next(); if (e.key === 'ArrowLeft') this.prev(); },
    }));

    Alpine.data('composer', () => ({
      open()  { this.$refs.dialog?.showModal?.(); this.$nextTick(() => this.$refs.title?.focus()); },
      close() { this.$refs.dialog?.close?.(); },
    }));
  });
</script>
