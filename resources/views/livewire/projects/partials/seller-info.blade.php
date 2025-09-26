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

<div class="rounded-md border border-base-300 bg-base-100 shadow-sm overflow-hidden">
  <div class="px-4 py-3 flex items-center justify-between border-b border-base-200">
    <flux:subheading size="sm">Seller info</flux:subheading>
    <flux:text size="xs" class="text-base-content/60">{{ $done }} / {{ $total }} complete</flux:text>
  </div>

  <ul class="divide-y divide-base-200">
    @foreach($flags as $row)
      @php
        $propOk    = $row['key'];
        $propNotes = $row['notesKey'];
        $okServer  = (bool)($project->{$propOk} ?? false);
      @endphp

      <li class="px-4 py-3" x-data="{ ok: @entangle($propOk).live }">
        <div class="flex items-start justify-between gap-4">
          <div class="flex items-start gap-3">
            <span class="mt-1 h-2.5 w-2.5 rounded-full"
                  :class="ok ? 'bg-emerald-500' : 'bg-amber-400'"
                  @class(['bg-emerald-500' => !$editing && $okServer, 'bg-amber-400' => !$editing && !$okServer])></span>
            <div>
              <flux:text size="sm" class="font-medium">{{ $row['label'] }}</flux:text>
              @unless($editing)
                <flux:text size="xs" class="text-base-content/60">
                  {{ $okServer ? 'All set.' : ($project->{$propNotes} ?: 'Missing — add the details.') }}
                </flux:text>
              @endunless
            </div>
          </div>

          <div class="shrink-0">
            @if(!$editing)
              @if($okServer)
                <x-ui.badge tone="success" label="Complete" />
              @else
                <x-ui.badge tone="warning" label="Missing" />
              @endif
            @else
              <label class="flex items-center gap-2">
                <input type="checkbox" class="toggle toggle-sm" x-model="ok" x-on:change="$wire.set('{{ $propOk }}', ok)">
                <span class="text-xs opacity-70" x-text="ok ? 'Complete' : 'Missing'"></span>
              </label>
            @endif
          </div>
        </div>

        @if($editing)
          <div class="mt-2" x-show="!ok" x-transition>
            <textarea class="textarea textarea-bordered w-full" rows="2" wire:model.defer="{{ $propNotes }}"
                      placeholder="Add details if missing..."></textarea>
            @error($propNotes) <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
          </div>
        @endif
      </li>
    @endforeach
  </ul>
</div>
