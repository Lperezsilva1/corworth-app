<div class="mt-6 flex flex-col">
  {{-- Flash --}}
  @if (session('comment_ok'))
    <div
      x-data="{show:true}"
      x-init="setTimeout(()=>show=false,3000)"
      x-show="show"
      x-transition
      class="mb-3 rounded-md bg-base-200/40 border border-base-300 px-3 py-2 text-sm"
    >
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
      // Newest first
      $items = $this->comments->sortByDesc('created_at');
    @endphp

    <div class="relative">
      {{-- vertical spine --}}
      <div class="absolute left-[18px] top-2 bottom-2 w-[2px] bg-base-300/70 rounded-full"></div>

      <ul class="space-y-6">
        @forelse($items as $c)
          @php
            // AUTO vs USER
            $isAuto = isset($c->is_system) ? (bool)$c->is_system : \Illuminate\Support\Str::startsWith($c->body ?? '', '[AUTO]');
            $name   = $c->user?->name ?? 'System';
            $stamp  = $c->created_at;
            $time   = $stamp->format('M d, Y · H:i');
            $title  = trim((string)($c->title ?? ''));

            // <-- NUEVO: cuerpo presentado (IDs -> nombres)
            $pretty = $this->presentCommentBody($c);

            // Look & feel
            if ($isAuto) {
              $iconBg   = 'bg-warning/10 border-warning/30 text-warning';
              $boxCls   = 'border-dashed border-warning/40 bg-warning/5 font-mono text-[13px]';
              $iconSvg  = '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 15.5A3.5 3.5 0 1 0 12 8.5a3.5 3.5 0 0 0 0 7Z"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 8 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 3.4 15a1.65 1.65 0 0 0-1.51-1H2a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 3.4 8a1.65 1.65 0 0 0-.33-1.82l-.06-.06A2 2 0 1 1 5.84 3.3l.06.06A1.65 1.65 0 0 0 7.72 3a1.65 1.65 0 0 0 1-1.51V1a2 2 0 1 1 4 0v.09A1.65 1.65 0 0 0 16 3.4c.5 0 .99-.2 1.35-.56l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06c-.36.36-.56.85-.56 1.35 0 .6.24 1.18.66 1.6.42.42 1 .66 1.6.66H22a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"/></svg>';
              $nameBadge = '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded border border-warning/30 bg-warning/10 text-warning text-[11px]">⚙️ AUTO</span>';
            } else {
              $iconBg   = 'bg-primary/10 border-primary/30 text-primary';
              $boxCls   = 'border-base-300 bg-base-200/40 dark:bg-base-300/20 shadow-sm';
              $iconSvg  = '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/></svg>';
              $nameBadge = '';
            }
          @endphp

          <li class="relative pl-12">
            {{-- round icon --}}
            <div class="absolute left-0 top-0">
              <div class="w-8 h-8 rounded-full border {{ $iconBg }} flex items-center justify-center shadow-sm">
                {!! $iconSvg !!}
              </div>
            </div>

            {{-- card --}}
            <div class="min-w-0 border rounded-lg p-3 {{ $boxCls }}">
              {{-- header row --}}
              <div class="flex items-center justify-between text-xs text-base-content/70">
                <div class="flex items-center gap-2">
                  {!! $nameBadge !!}
                  <span class="font-medium text-base-content">{{ $name }}</span>
                  @if(!empty($c->source))
                    <span class="opacity-60">• {{ $c->source }}</span>
                  @endif
                </div>
                <time class="opacity-70" title="{{ $stamp->toIso8601String() }}">{{ $time }}</time>
              </div>

              {{-- title --}}
              @if($title !== '')
                <h4 class="mt-2 text-sm font-semibold leading-tight break-words">
                  {{ $title }}
                </h4>
              @endif

              {{-- body (presentado) --}}
              @if(!empty($pretty))
                <div class="mt-1 whitespace-pre-line text-sm text-base-content">
                  {!! nl2br(e($pretty)) !!}
                </div>
              @endif

              {{-- Attachments --}}
              @if($c->attachments->count())
                @php
                  $atts = $c->attachments->sortBy('id')->values();
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
                  $viewerKey = 'viewer-'.$c->id.'-'.($atts->max('id') ?? 0).'-'.$atts->count();
                @endphp

                <div class="mt-2"
                     x-data="viewer({ items: @js($viewerItems) })"
                     @keydown.window="onKey($event)"
                     wire:key="{{ $viewerKey }}">

                  {{-- thumbs --}}
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

                  {{-- Modal viewer --}}
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

              {{-- Actions --}}
              @if(auth()->id() && $c->user_id === auth()->id() && !$isAuto)
                <div class="mt-2">
                  <button class="btn btn-xs btn-ghost text-error" wire:click="deleteComment({{ $c->id }})">Delete</button>
                </div>
              @endif
            </div>
          </li>
        @empty
          <li class="text-sm opacity-70 px-2 py-4">No comments yet.</li>
        @endforelse
      </ul>
    </div>
  </div>

  {{-- Composer (modal) --}}
  <div
    class="mt-6"
    x-data="{ open: @entangle('composerOpen').live }"
    x-on:comment-added.window="open = false"
    wire:key="comments-composer-{{ $projectId }}"
  >
    <div class="flex justify-end">
      <button
        type="button"
        form="__none"
        class="btn btn-primary"
        @click.prevent.stop="open = true"
      >
        Add comment
      </button>
    </div>

    <dialog
      x-ref="dialog"
      class="modal"
      x-init="$watch('open', v => v ? $refs.dialog?.showModal?.() : $refs.dialog?.close?.())"
      x-on:keydown.escape="open = false"
      wire:ignore.self
    >
      <div class="modal-box max-w-2xl" @click.stop>
        <button
          type="button"
          form="__none"
          class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
          @click.prevent.stop="open = false"
        >✕</button>

        <h3 class="text-lg font-semibold mb-4">New comment</h3>

        {{-- Título --}}
        <label class="block text-xs font-medium mb-1 opacity-70">Title</label>
        <input
          type="text"
          class="input input-bordered w-full mb-3"
          placeholder="Short, descriptive title"
          wire:model.defer="commentTitle"
          x-effect="if (open) $nextTick(() => $el.focus())"
          wire:keydown.ctrl.enter="addComment"
          wire:keydown.meta.enter="addComment"
        />
        @error('commentTitle') <p class="text-error text-xs mb-2">{{ $message }}</p> @enderror

        {{-- Cuerpo --}}
        <label class="block text-xs font-medium mb-1 opacity-70">Details</label>
        <textarea
          class="textarea textarea-bordered w-full min-h-28"
          wire:model.defer="commentBody"
          placeholder="Describe the update… (Ctrl/Cmd + Enter to send)"
          wire:keydown.ctrl.enter="addComment"
          wire:keydown.meta.enter="addComment"
        ></textarea>
        @error('commentBody') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror

        {{-- Adjuntos --}}
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
            <progress max="100" :value="progress" class="progress progress-primary w-full"></progress>
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
          <button
            type="button"
            form="__none"
            class="btn"
            @click.prevent.stop="open = false"
          >Cancel</button>

          <button
            type="button"
            form="__none"
            class="btn btn-primary"
            wire:click="addComment"
            wire:loading.attr="disabled"
            wire:target="addComment,uploads"
          >
            <span wire:loading wire:target="addComment" class="loading loading-spinner loading-xs"></span>
            <span wire:loading.remove wire:target="addComment">Send</span>
            <span wire:loading.delay wire:target="addComment">Sending…</span>
          </button>
        </div>
      </div>

      {{-- Backdrop --}}
      <form method="dialog" class="modal-backdrop">
        <button>close</button>
      </form>
    </dialog>
  </div>
</div>

{{-- Alpine helpers --}}
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
