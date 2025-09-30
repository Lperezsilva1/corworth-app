{{-- resources/views/projects/show.blade.php --}}
<div class="p-6" x-data>

  {{-- ===== Toasts (success/error) ===== --}}
  <x-ui.toast />

  {{-- ===== Header ===== --}}
  <x-breadcrumbs :items="[
    ['label' => 'Home', 'url' => url('/')],
    ['label' => 'Projects', 'url' => route('projects.index')],
    [$editing ? 'Edit' : 'Show'],
  ]" />

  <div class="flex items-center justify-between mb-6">
    <div class="w-full md:w-auto md:pr-6">
      {{-- Título con icono --}}
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
         <h3 class="text-1xl/5 font-bold text-base-content sm:truncate sm:text-2xl sm:tracking-tight">
  {{ $project->project_name }}
</h3>
          <flux:subheading size="lg" class="mb-6">{{ __('Model') }} {{ $project->building?->name_building ?? '—' }}</flux:subheading>
        </div>
      </div>
    </div>

    {{-- Botones Edit/Save/Cancel --}}
{{-- Botones Edit/Save/Cancel --}}
<div class="flex items-center gap-2">
  @if(!$editing)
    <button
      type="button"
      wire:click="startEdit"
      wire:target="startEdit"
      wire:loading.attr="disabled"
      class="btn btn-primary flex items-center gap-2 min-w-[110px] justify-center">
      <span wire:loading wire:target="startEdit" class="loading loading-spinner loading-xs"></span>
      <span wire:loading.remove wire:target="startEdit">Edit</span>
      <span wire:loading.delay wire:target="startEdit">Loading…</span>
    </button>
    <a wire:navigate href="{{ route('projects.index') }}" class="btn">Back</a>
  @else
   <button
  type="button"
  wire:click="saveEdit"
  wire:target="saveEdit"
  wire:loading.attr="disabled"
  class="btn btn-primary flex items-center gap-2 min-w-[110px] justify-center"
>
  {{-- Texto normal (oculto cuando carga) --}}
  <span wire:loading.remove wire:target="saveEdit">Save</span>

  {{-- Estado “Saving…” + spinner (visible cuando carga) --}}
  <span class="inline-flex items-center gap-2" wire:loading.flex wire:target="saveEdit">
    <span class="loading loading-spinner loading-xs"></span>
    <span>Saving…</span>
  </span>
</button>

    <button
      type="button"
      wire:click="cancelEdit"
      wire:target="cancelEdit"
      wire:loading.attr="disabled"
      class="btn">
      Cancel
    </button>
  @endif
</div>

  </div>

  <flux:separator variant="subtle" class="my-2" />

  {{-- ===== Tabs ===== --}}
  <div role="tablist" class="tabs  tabs-box" wire:loading.class="opacity-60 pointer-events-none">

    {{-- ===================== TAB: General ===================== --}}
    <input type="radio" name="project_tabs" role="tab" class="tab" aria-label="General Information"
       @checked(request('tab', 'general') === 'general') />
    <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-5">

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Project info --}}
        @include('livewire.projects.partials.basic-details')

        {{-- Seller Info --}}
       @include('livewire.projects.partials.seller-info')

      </div>
    </div>

    {{-- ===================== TAB: Phase 1 ===================== --}}
    <input type="radio" name="project_tabs" role="tab" class="tab" aria-label="Phase 1"
       @checked(request('tab') === 'phase1') />
    <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-5">
      @include('livewire.projects.partials.phase1')
    </div>

    {{-- ===================== TAB: Full Set ===================== --}}
   <input type="radio" name="project_tabs" role="tab" class="tab" aria-label="Full Set"
       @checked(request('tab') === 'fullset') />
    <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-5">
      @include('livewire.projects.partials.fullset')
    </div>

    {{-- ===================== TAB: Notes ===================== --}}
    <input type="radio" name="project_tabs" role="tab" class="tab" aria-label="Notes"
       @checked(request('tab') === 'notes') />
    <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-5">
      <div class="rounded-md border border-base-300 bg-base-100 shadow-sm p-4">
        <flux:subheading size="md" class="font-semibold mb-3">Notes</flux:subheading>
        <livewire:projects.project-comments :projectId="$project->id" :key="'comments-'.$project->id.'-'.$commentsVersion"/>
      </div>
    </div>
  </div>

  {{-- ===== Modals ===== --}}
@include('livewire.projects.partials.modals')

</div>


@if($editing)
  <div
    wire:loading.flex
    wire:target="saveEdit"
    class="fixed inset-0 z-50 items-center justify-center bg-base-100/70 backdrop-blur"
    aria-live="polite" aria-busy="true"
  >
    <div class="rounded-xl border border-base-300 bg-base-100/95 shadow-xl px-6 py-4">
      <div class="flex items-center gap-3">
        <span class="loading loading-spinner loading-lg text-primary"></span>
        <div class="text-sm">
          <div class="font-semibold text-base-content">Saving changes</div>
          <div class="text-base-content/60">Please wait while we update the project…</div>
        </div>
      </div>
    </div>
  </div>
@endif
