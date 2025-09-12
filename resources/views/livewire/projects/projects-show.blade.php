<div class="p-6" x-data>
  {{-- ===== Floating Toast (auto-hide + dismiss) ===== --}}
  <div
    x-data="{ show: {{ session()->has('success') ? 'true' : 'false' }}, msg: @js(session('success')) }"
    x-init="if (show) { setTimeout(() => show = false, 3500) }"
    x-show="show"
    x-transition.opacity
    class="fixed top-4 left-1/2 -translate-x-1/2 z-50"
    style="display:none"
  >
    <div class="rounded-md border border-green-500/30 bg-green-500/10 text-green-700 px-4 py-2 shadow-lg backdrop-blur">
      <div class="flex items-center gap-3">
        <span>✅ <span x-text="msg || 'Update complete.'"></span></span>
        <button class="btn btn-xs btn-ghost" @click="show = false">Close</button>
      </div>
    </div>
  </div>

  {{-- ===== Header ===== --}}
  <x-breadcrumbs :items="[
    ['label' => 'Home', 'url' => url('/')],
    ['label' => 'Projects', 'url' => route('projects.index')],
    [$editing ? 'Edit' : 'Show'],
  ]" />

  <div class="flex items-center justify-between mb-6">
    <div class="w-full md:w-auto md:pr-6">
      

      {{-- Título con icono de construcción --}}
      <div class="flex items-start gap-3">
        <div class="h-12 w-12 rounded-full bg-primary/10 text-primary ring-1 ring-primary/20 flex items-center justify-center shrink-0">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
               stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"
               class="h-6 w-6" aria-hidden="true">
            <rect x="3" y="3" width="7" height="18" rx="1"></rect>
            <path d="M3 9h7M3 13h7M3 17h7"></path>
            <rect x="14" y="7" width="7" height="14" rx="1"></rect>
            <path d="M14 11h7M14 15h7M14 19h7"></path>
          </svg>
          <span class="sr-only">Project</span>
        </div>

        <div>
          <flux:heading size="xl" level="1">
            {{ $project->project_name }} {{ $project->building?->name_building ?? '—' }}
          </flux:heading>
          <flux:subheading size="lg" class="mb-6">{{ __('General project details') }}</flux:subheading>
        </div>
      </div>
    </div>

    <div class="flex gap-2">
      @if(!$editing)
        <button wire:click="startEdit" class="btn btn-primary">Edit</button>
        <a wire:navigate href="{{ route('projects.index') }}" class="btn">Back</a>
      @else
        <button wire:click="saveEdit" class="btn btn-primary" wire:loading.attr="disabled">Save</button>
        <button wire:click="cancelEdit" class="btn" wire:loading.attr="disabled">Cancel</button>
      @endif
    </div>
  </div>

  <flux:separator variant="subtle" class="my-2" />

  {{-- ===== Tabs (DaisyUI) ===== --}}
  <div role="tablist" class="tabs tabs-boxed tabs-lift" wire:loading.class="opacity-60 pointer-events-none">

    {{-- ===================== TAB: General ===================== --}}
    <input type="radio" name="project_tabs" role="tab" class="tab" aria-label="General Information" checked />
    <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-5">

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- LEFT: Project info --}}
        <div class="rounded-md border border-base-300 bg-base-100 shadow-sm overflow-hidden">
          <div class="px-4 py-3 flex items-center justify-between">
            <div class="text-sm font-semibold">Project info</div>
          </div>
          <div class="border-t border-base-300"></div>

          {{-- Basic details --}}
          <div class="px-4 py-4">
            <div class="text-xs font-semibold text-base-content/60 mb-2">Basic details</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              {{-- Building --}}
              <div>
                <div class="text-[11px] uppercase tracking-wide text-base-content/60 mb-1">Building</div>
                @if($editing)
                  <select class="select select-bordered w-full" wire:model.defer="building_id">
                    <option value="">— Select building —</option>
                    @foreach($buildings as $b)
                      <option value="{{ $b->id }}">{{ $b->name_building }}</option>
                    @endforeach
                  </select>
                  @error('building_id') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                @else
                  <div class="font-medium">{{ $project->building?->name_building ?? '—' }}</div>
                @endif
              </div>

              {{-- Seller --}}
              <div>
                <div class="text-[11px] uppercase tracking-wide text-base-content/60 mb-1">Seller</div>
                @if($editing)
                  <select class="select select-bordered w-full" wire:model.defer="seller_id">
                    <option value="">— Select seller —</option>
                    @foreach($sellers as $s)
                      <option value="{{ $s->id }}">{{ $s->name_seller }}</option>
                    @endforeach
                  </select>
                  @error('seller_id') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                @else
                  <div class="font-medium">{{ $project->seller?->name_seller ?? '—' }}</div>
                @endif
              </div>
            </div>
          </div>

          <div class="border-t border-base-300"></div>

          {{-- General status (FK → statuses.id) --}}
          <div class="px-4 py-4">
            <div class="text-xs font-semibold text-base-content/60 mb-2">General status</div>

            @if($editing)
              @php
                $current = collect($statuses)->firstWhere('id', (int) ($general_status ?? $project->general_status));
                $key     = $current['key'] ?? null;
                $label   = $current['label'] ?? null;

                $palette = [
                  'pending'           => 'bg-zinc-50 text-zinc-700 ring-zinc-200',
                  'working'           => 'bg-sky-50 text-sky-700 ring-sky-200',
                  'awaiting_approval' => 'bg-amber-50 text-amber-700 ring-amber-200',
                  'approved'          => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                  'cancelled'         => 'bg-rose-50 text-rose-700 ring-rose-200',
                ];
                $badge   = 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset shadow-sm';
                $tone    = $palette[$key] ?? 'bg-gray-50 text-gray-700 ring-gray-200';
                $generalKeys    = ['pending','working','awaiting_approval','approved','cancelled'];
                $generalOptions = collect($statuses)->whereIn('key', $generalKeys);
              @endphp

              <div class="flex items-center gap-3">
                {{-- aquí no hay select: auto-managed --}}
                @if($label)
                  <span class="{{ $badge }} {{ $tone }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $label }}
                  </span>
                @endif
              </div>

              @error('general_status') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
            @else
              @php
                $key   = $project->status?->key;
                $label = $project->general_status_label;
                $palette = [
                  'pending'           => 'bg-zinc-50 text-zinc-700 ring-zinc-200',
                  'working'           => 'bg-sky-50 text-sky-700 ring-sky-200',
                  'awaiting_approval' => 'bg-amber-50 text-amber-700 ring-amber-200',
                  'approved'          => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                  'cancelled'         => 'bg-rose-50 text-rose-700 ring-rose-200',
                ];
                $badge = 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset shadow-sm';
                $tone  = $palette[$key] ?? 'bg-gray-50 text-gray-700 ring-gray-200';
              @endphp

              @if($project->status)
                <div class="flex items-center gap-3">
                  <span class="{{ $badge }} {{ $tone }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $label }}
                  </span>

                  {{-- ✅ Botón APPROVE solo cuando ambas fases están complete y aún no está final --}}
                  @if(($project->phase1Status?->key === 'complete')
                      && ($project->fullsetStatus?->key === 'complete')
                      && !in_array($project->status?->key, ['approved','cancelled']))
                    <button class="btn btn-success btn-sm"
                            wire:click="approveProject"
                            wire:loading.attr="disabled">
                      Approve
                    </button>
                  @endif
                </div>
              @else
                —
              @endif
            @endif
          </div>

          <div class="border-t border-base-300"></div>

          {{-- Notes --}}
          <div class="px-4 py-4">
            <div class="text-xs font-semibold text-base-content/60 mb-2">Notes</div>
            @if($editing)
              <textarea class="textarea textarea-bordered w-full" rows="4"
                        wire:model.defer="notes" placeholder="Project notes"></textarea>
              @error('notes') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
            @else
              <div class="font-medium whitespace-pre-line">{{ $project->notes ?: '—' }}</div>
            @endif
          </div>
        </div>

        {{-- RIGHT: Seller Info --}}
        @php
          $flags = [
            ['key'=>'seller_door_ok',             'label'=>'Door information',        'notesKey'=>'seller_door_notes'],
            ['key'=>'seller_accessories_ok',      'label'=>'Accessories information', 'notesKey'=>'seller_accessories_notes'],
            ['key'=>'seller_exterior_finish_ok',  'label'=>'Exterior finish',         'notesKey'=>'seller_exterior_finish_notes'],
            ['key'=>'seller_plumbing_fixture_ok', 'label'=>'Plumbing fixtures',       'notesKey'=>'seller_plumbing_fixture_notes'],
            ['key'=>'seller_utility_direction_ok','label'=>'Utility direction',       'notesKey'=>'seller_utility_direction_notes'],
            ['key'=>'seller_electrical_ok',       'label'=>'Electrical information',  'notesKey'=>'seller_electrical_notes'],
          ];
          $done     = collect($flags)->filter(fn($f)=> (bool)($project->{$f['key']} ?? false))->count();
          $total    = count($flags);
          $useOther = (bool)($project->other_ok || $project->other_label || $project->other_notes);
          if ($useOther) { $total += 1; if ($project->other_ok) $done += 1; }
          $pct = $total ? intval(($done/$total)*100) : 0;
        @endphp

        <div class="rounded-md border border-base-300 bg-base-100 shadow-sm overflow-hidden"
             wire:key="seller-info-{{ $project->id }}-{{ optional($project->updated_at)->timestamp }}">
          <div class="px-4 py-3 flex items-center justify-between">
            <div class="text-sm font-semibold">Seller Info</div>
            <div class="flex items-center gap-3">
              <progress class="progress progress-primary w-44" value="{{ $pct }}" max="100"></progress>
              <span class="text-xs opacity-70">{{ $done }} / {{ $total }} complete</span>
            </div>
          </div>
          <div class="border-t border-base-300"></div>

          <div class="divide-y divide-base-300">
            @foreach($flags as $row)
              @php
                $propOk    = $row['key'];
                $propNotes = $row['notesKey'];
                $okServer  = (bool)($project->{$propOk} ?? false);
              @endphp

              <div class="px-4 py-3"
                   x-data="{ ok: @entangle($propOk).live }">
                <div class="flex items-start justify-between gap-4">
                  {{-- Left --}}
                  <div class="flex items-start gap-3">
                    <div class="mt-0.5 h-5 w-5 shrink-0 rounded-full flex items-center justify-center"
                         :class="ok ? 'bg-green-500 text-white' : 'bg-amber-400 text-white'">
                      <span x-text="ok ? '✓' : '!'"></span>
                    </div>
                    <div>
                      <div class="font-medium leading-tight">{{ $row['label'] }}</div>

                      @unless($editing)
                        <p class="text-xs text-base-content/70 mt-0.5" x-show="ok">All set.</p>
                        <p class="text-xs text-base-content/70 mt-0.5" x-show="!ok">
                          {{ $project->{$propNotes} ?: 'Missing — add the details.' }}
                        </p>
                      @endunless
                    </div>
                  </div>

                  {{-- Right (edit controls) --}}
                  @if($editing)
                    <div class="w-full md:w-1/2">
                      <div class="flex items-center justify-end gap-2 mb-2">
                        <span class="label-text text-sm">Status</span>
                        <input type="checkbox" class="toggle" x-model="ok">
                        <span class="text-xs opacity-70" x-text="ok ? 'Complete' : 'Missing'"></span>
                      </div>

                      <div x-show="!ok" x-transition>
                        <textarea class="textarea textarea-bordered w-full" rows="2"
                                  placeholder="What’s missing?"
                                  wire:model.defer="{{ $propNotes }}"></textarea>
                        @error($propNotes) <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                      </div>
                    </div>
                  @endif
                </div>
              </div>
            @endforeach
          </div>

          {{-- Other (optional) --}}
          <div class="border-t border-base-300"></div>
          <div class="px-4 py-3"
               x-data="{ open:@json($useOther), ok:@entangle('other_ok').live }">
            <div class="flex items-center justify-between">
              <div class="text-sm font-medium text-base-content/80">Other (optional)</div>
              @if($editing)
                <button class="btn btn-sm btn-outline" x-show="!open"
                        @click="open = true; $wire.set('other_label',''); $wire.set('other_notes',''); $wire.set('other_ok', false)">
                  + Add “Other”
                </button>
              @endif
            </div>

            <template x-if="open">
              <div class="mt-3 rounded-lg border p-3"
                   :class="ok ? 'bg-green-50/40 border-green-200' : 'bg-base-200/40 border-base-300'">
                <div class="flex items-start justify-between gap-3">
                  <div class="font-medium">{{ $project->other_label ?: 'Other' }}</div>
                  @if($editing)
                    <div class="flex gap-2">
                      <label class="label cursor-pointer gap-2">
                        <span class="label-text text-sm">Status</span>
                        <input type="checkbox" class="toggle" x-model="ok">
                        <span class="text-xs opacity-70" x-text="ok ? 'Complete' : 'Missing'"></span>
                      </label>
                      <button type="button" class="btn btn-xs btn-ghost text-error"
                              @click="$wire.clearOther(); open=false">Remove</button>
                    </div>
                  @endif
                </div>

                @unless($editing)
                  <div class="mt-1">
                    @if($project->other_ok)
                      <span class="badge badge-success badge-sm">Complete</span>
                    @else
                      <span class="badge badge-warning badge-sm">Missing</span>
                      @if($project->other_notes)
                        <span class="ml-2 text-xs opacity-80">— {{ $project->other_notes }}</span>
                      @endif
                    @endif
                  </div>
                @endunless

                @if($editing)
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                    <div>
                      <label class="text-xs opacity-70">Title</label>
                      <input type="text" class="input input-bordered w-full"
                             placeholder="(e.g., Base color ...)"
                             wire:model.defer="other_label">
                      @error('other_label') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2" x-show="!ok" x-transition>
                      <label class="text-xs opacity-70">Details</label>
                      <textarea class="textarea textarea-bordered w-full" rows="2"
                                placeholder="Describe what’s missing…"
                                wire:model.defer="other_notes"></textarea>
                      @error('other_notes') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                  </div>
                @endif
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>

    {{-- ===================== TAB: Phase 1 ===================== --}}
    <input type="radio" name="project_tabs" role="tab" class="tab" aria-label="Phase 1" />
    <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-5">
      <flux:subheading size="md" class="font-semibold mb-3">Phase 1</flux:subheading>

      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="md:col-span-2">
          <div class="text-sm text-base-content/70 mb-1">Drafter</div>
          @if($editing)
            <select class="select select-bordered w-full" wire:model.defer="phase1_drafter_id">
              <option value="">— Select drafter —</option>
              @foreach($drafters as $d)
                <option value="{{ $d->id }}">{{ $d->name_drafter }}</option>
              @endforeach
            </select>
            @error('phase1_drafter_id') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
          @else
            <div class="font-medium">{{ $project->drafterPhase1?->name_drafter ?? '—' }}</div>
          @endif
        </div>

        <div>
          <div class="text-sm text-base-content/70 mb-1">Status</div>
          @if($editing)
            @php
              $phaseKeys = ['pending','working','complete'];
              $phaseOptions = collect($statuses)->whereIn('key', $phaseKeys);
            @endphp

            <select class="select select-bordered w-full" wire:model.defer="phase1_status_id">
              <option value="">— Select status —</option>
              @foreach($phaseOptions as $st)
                <option value="{{ $st['id'] }}">{{ $st['label'] }}</option>
              @endforeach
            </select>
            @error('phase1_status_id') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
          @else
            @php
              $p1Key   = $project->phase1Status?->key;
              $p1Label = $project->phase1Status?->label;
              $palette = [
                'pending'           => 'bg-zinc-50 text-zinc-700 ring-zinc-200',
                'working'           => 'bg-sky-50 text-sky-700 ring-sky-200',
                'complete'          => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                'awaiting_approval' => 'bg-amber-50 text-amber-700 ring-amber-200',
                'approved'          => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                'cancelled'         => 'bg-rose-50 text-rose-700 ring-rose-200',
              ];
              $badge = 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset shadow-sm';
              $tone  = $palette[$p1Key] ?? 'bg-gray-50 text-gray-700 ring-gray-200';
            @endphp

            @if($p1Label)
              <span class="{{ $badge }} {{ $tone }}">
                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $p1Label }}
              </span>
            @else
              <div>—</div>
            @endif
          @endif
        </div>

        <div>
          <div class="text-sm text-base-content/70 mb-1">Duration</div>
          <div class="font-medium">
           {{ $project->phase1_duration ? $project->phase1_duration.' days' : '—' }}
          </div>
        </div>

        <div>
          <div class="text-sm text-base-content/70 mb-1">Start</div>
          @if($editing)
            <input type="date" class="input input-bordered w-full" wire:model.defer="phase1_start_date">
            @error('phase1_start_date') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
          @else
            <div class="font-medium">{{ optional($project->phase1_start_date)->format('Y-m-d') ?? '—' }}</div>
          @endif
        </div>

        <div>
          <div class="text-sm text-base-content/70 mb-1">End</div>
          @if($editing)
            <input type="date" class="input input-bordered w-full" wire:model.defer="phase1_end_date">
            @error('phase1_end_date') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
          @else
            <div class="font-medium">{{ optional($project->phase1_end_date)->format('Y-m-d') ?? '—' }}</div>
          @endif
        </div>
      </div>
    </div>

    {{-- ===================== TAB: Full Set ===================== --}}
    <input type="radio" name="project_tabs" role="tab" class="tab" aria-label="Full Set" />
    <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-5">
      <flux:subheading size="md" class="font-semibold mb-3">Full Set</flux:subheading>

      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="md:col-span-2">
          <div class="text-sm text-base-content/70 mb-1">Drafter</div>
          @if($editing)
            <select class="select select-bordered w-full" wire:model.defer="fullset_drafter_id">
              <option value="">— Select drafter —</option>
              @foreach($drafters as $d)
                <option value="{{ $d->id }}">{{ $d->name_drafter }}</option>
              @endforeach
            </select>
            @error('fullset_drafter_id') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
          @else
            <div class="font-medium">{{ $project->drafterFullset?->name_drafter ?? '—' }}</div>
          @endif
        </div>

        <div>
          <div class="text-sm text-base-content/70 mb-1">Status</div>
          @if($editing)
            @php
              $phaseKeys = ['pending','working','complete'];
              $phaseOptions = collect($statuses)->whereIn('key', $phaseKeys);
            @endphp

            <select class="select select-bordered w-full" wire:model.defer="fullset_status_id">
              <option value="">— Select status —</option>
              @foreach($phaseOptions as $st)
                <option value="{{ $st['id'] }}">{{ $st['label'] }}</option>
              @endforeach
            </select>
            @error('fullset_status_id') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
          @else
            @php
              $fsKey   = $project->fullsetStatus?->key;
              $fsLabel = $project->fullsetStatus?->label;
              $palette = [
                'pending'           => 'bg-zinc-50 text-zinc-700 ring-zinc-200',
                'working'           => 'bg-sky-50 text-sky-700 ring-sky-200',
                'complete'          => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                'awaiting_approval' => 'bg-amber-50 text-amber-700 ring-amber-200',
                'approved'          => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                'cancelled'         => 'bg-rose-50 text-rose-700 ring-rose-200',
              ];
              $badge = 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset shadow-sm';
              $tone  = $palette[$fsKey] ?? 'bg-gray-50 text-gray-700 ring-gray-200';
            @endphp

            @if($fsLabel)
              <span class="{{ $badge }} {{ $tone }}">
                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $fsLabel }}
              </span>
            @else
              <div>—</div>
            @endif
          @endif
        </div>

        <div>
          <div class="text-sm text-base-content/70 mb-1">Duration</div>
          <div class="font-medium">
            {{ $project->fullset_duration ? $project->fullset_duration.' days' : '—' }}
          </div>
        </div>

        <div>
          <div class="text-sm text-base-content/70 mb-1">Start</div>
          @if($editing)
            <input type="date" class="input input-bordered w-full" wire:model.defer="fullset_start_date">
            @error('fullset_start_date') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
          @else
            <div class="font-medium">{{ optional($project->fullset_start_date)->format('Y-m-d') ?? '—' }}</div>
          @endif
        </div>

        <div>
          <div class="text-sm text-base-content/70 mb-1">End</div>
          @if($editing)
            <input type="date" class="input input-bordered w-full" wire:model.defer="fullset_end_date">
            @error('fullset_end_date') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
          @else
            <div class="font-medium">{{ optional($project->fullset_end_date)->format('Y-m-d') ?? '—' }}</div>
          @endif
        </div>
      </div>
    </div>

    {{-- ===================== TAB: Notes ===================== --}}
    <input type="radio" name="project_tabs" role="tab" class="tab" aria-label="Notes" />
    <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-5">
      <div class="rounded-md border border-base-300 bg-base-100 shadow-sm p-4">
        <flux:subheading size="md" class="font-semibold mb-3">Notes</flux:subheading>
        <livewire:projects.project-comments :projectId="$project->id" :key="'comments-'.$project->id.'-'.$commentsVersion"/>
      </div>
    </div>

  </div>
</div>
