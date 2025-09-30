@php
  $flags = [
    ['key'=>'seller_door_ok','label'=>'Door information','notesKey'=>'seller_door_notes'],
    ['key'=>'seller_accessories_ok','label'=>'Accessories information','notesKey'=>'seller_accessories_notes'],
    ['key'=>'seller_exterior_finish_ok','label'=>'Exterior finish','notesKey'=>'seller_exterior_finish_notes'],
    ['key'=>'seller_plumbing_fixture_ok','label'=>'Plumbing fixtures','notesKey'=>'seller_plumbing_fixture_notes'],
    ['key'=>'seller_utility_direction_ok','label'=>'Utility direction','notesKey'=>'seller_utility_direction_notes'],
    ['key'=>'seller_electrical_ok','label'=>'Electrical information','notesKey'=>'seller_electrical_notes'],
  ];
  $done  = collect($flags)->filter(fn($f)=> (bool)($project->{$f['key']} ?? false))->count();
  $total = count($flags);
@endphp

<div class="rounded-xl bg-base-100 shadow-sm border border-base-200/80 dark:border-white/10 font-[Inter] text-[15px]">
  {{-- Header --}}
  <div class="px-6 py-5 border-b border-base-200 dark:border-white/10">
    <div class="flex items-start justify-between gap-6">
      <div class="flex items-start gap-3">
        {{-- Icono --}}
        <svg class="h-6 w-6 text-primary/80 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M4 7h16M4 12h16m-7 5h7"/>
        </svg>

        <div>
          <flux:heading size="lg" class="font-semibold leading-tight">Seller information</flux:heading>
          
        </div>
      </div>

      {{-- Progreso --}}
      <div class="hidden sm:flex items-center gap-3">
        <flux:text size="xs" class="text-base-content/60 whitespace-nowrap">{{ $done }} / {{ $total }} complete</flux:text>
        <progress class="progress progress-primary w-40" value="{{ $done }}" max="{{ $total }}"></progress>
      </div>
    </div>

    {{-- Progreso en mobile --}}
    <div class="sm:hidden mt-3">
      <div class="flex items-center justify-between text-xs text-base-content/60 mb-1">
        <span>Status</span>
        <span>{{ $done }} / {{ $total }}</span>
      </div>
      <progress class="progress progress-primary w-full" value="{{ $done }}" max="{{ $total }}"></progress>
    </div>
  </div>

  {{-- Lista --}}
  <ul class="divide-y divide-base-200 dark:divide-white/10">
    @foreach($flags as $row)
      @php
        $propOk    = $row['key'];
        $propNotes = $row['notesKey'];
        $okServer  = (bool)($project->{$propOk} ?? false);
      @endphp

      <li x-data="{ ok: @entangle($propOk).live }" class="px-6 py-4">
        {{-- Item en 3 columnas: etiqueta + descripción / acciones --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          {{-- Columna izquierda: etiqueta + hint --}}
          <div class="min-w-0">
            <div class="flex items-start gap-2">
              <span class="mt-1 h-2.5 w-2.5 rounded-full transition-colors"
                    :class="ok ? 'bg-emerald-500' : 'bg-amber-400'"
                    @class(['bg-emerald-500' => !$editing && $okServer, 'bg-amber-400' => !$editing && !$okServer])></span>
              <div class="min-w-0">
                <div class="text-sm font-medium text-base-content">{{ $row['label'] }}</div>

                @unless($editing)
                  <div class="text-xs text-base-content/60 truncate">
                    {{ $okServer ? 'All set.' : ($project->{$propNotes} ?: 'Missing — add the details.') }}
                  </div>
                @else
                  <div class="text-xs text-base-content/60" x-show="ok">All set.</div>
                @endunless
              </div>
            </div>
          </div>

          {{-- Columna derecha: badge o toggle --}}
          <div class="sm:col-span-2 flex items-center justify-between sm:justify-end gap-3">
            @if(!$editing)
              @if($okServer)
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium border border-emerald-300/60 text-emerald-700 bg-emerald-50">
                  Complete
                </span>
              @else
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium border border-amber-300/70 text-amber-700 bg-amber-50">
                  Missing
                </span>
              @endif
            @else
              <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" class="toggle toggle-sm" x-model="ok"
                       x-on:change="$wire.set('{{ $propOk }}', ok)">
                <span class="text-xs text-base-content/70" x-text="ok ? 'Complete' : 'Missing'"></span>
              </label>
            @endif
          </div>

          {{-- Notas (cuando falta y se edita) --}}
          @if($editing)
            <div class="sm:col-span-3" x-show="!ok" x-transition>
              <textarea
                class="textarea textarea-bordered w-full"
                rows="2"
                wire:model.defer="{{ $propNotes }}"
                placeholder="Add details if missing..."></textarea>
              @error($propNotes)
                <p class="text-error text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>
          @endif
        </div>
      </li>
    @endforeach
  </ul>
</div>
