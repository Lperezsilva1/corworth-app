<div class="relative w-56 sm:w-64 lg:w-72">
  {{-- INPUT --}}
  <label class="input input-bordered  w-full flex items-center gap-2 rounded-lg">
    <svg class="h-4 w-4 opacity-60" viewBox="0 0 24 24" fill="none"
         stroke="currentColor" stroke-width="1.8" aria-hidden="true">
      <circle cx="11" cy="11" r="7"/>
      <path d="m21 21-4.3-4.3"/>
    </svg>

    <input
      type="text"
      placeholder="Search…"
      wire:model.live="q"
      class="grow outline-none bg-transparent"
      autocomplete="off"
      wire:keydown.enter.prevent.stop="goFirst"
      wire:keydown.escape.stop="clear"
      wire:keydown.arrow-down.prevent="moveSelection('down')"
      wire:keydown.arrow-up.prevent="moveSelection('up')"
    />

    <span class="hidden loading loading-spinner loading-xs"
          wire:loading.class.remove="hidden" wire:target="q"></span>
  </label>

  @php
    $groups = [
      'projects' => 'Projects',
      'sellers'  => 'Sellers',
      'drafters' => 'Drafters',
      'models'   => 'Models',
    ];

    $counts = [];
    foreach (array_keys($groups) as $g) {
      $counts[$g] = collect($results[$g] ?? [])->count();
    }
    $hasAny = array_sum($counts) > 0;
  @endphp

  @if($hasAny)
    <div
      class="absolute left-0 top-full mt-2 w-full rounded-md border border-base-300
             bg-base-100 shadow-lg z-50 overflow-hidden"
    >
      @php $loopIndex = 0; @endphp

      @foreach ($groups as $key => $title)
        @if(!empty($results[$key]))
          <div class="px-3 pt-2 text-xs font-semibold opacity-70">{{ $title }}</div>
          <ul class="p-1">
            @foreach($results[$key] as $row)
              @php $isSelected = ($loopIndex === $selectedIndex); @endphp
              <li>
                <button
                  type="button"
                  class="w-full text-left px-3 py-2 text-sm rounded
                         hover:bg-base-200/60
                         {{ $isSelected ? 'bg-base-200/80 ring-1 ring-base-300' : '' }}"
                  wire:click="go('{{ $row['url'] }}')"
                  wire:mouseenter="$set('selectedIndex', {{ $loopIndex }})"
                >
                  <div class="font-medium truncate">{{ $row['label'] }}</div>
                  @if(!empty($row['sub']))
                    <div class="text-xs opacity-70 truncate">{{ $row['sub'] }}</div>
                  @endif
                </button>
              </li>
              @php $loopIndex++; @endphp
            @endforeach
          </ul>
        @endif
      @endforeach

      <div class="p-2 text-right border-t border-base-200">
        <button class="btn btn-ghost btn-xs" wire:click="clear">Clear</button>
      </div>
    </div>
  @elseif(strlen($q ?? '') >= 2)
    {{-- Sin resultados (cuando hay 2+ caracteres) --}}
    <div class="absolute left-0 top-full mt-2 w-full rounded-md border border-base-300 bg-base-100 shadow-md z-50 p-3 text-sm opacity-80">
      No results for “{{ $q }}”.
    </div>
  @endif
</div>
