<x-breadcrumbs :items="[
  ['label' => 'Home', 'url' => url('/')],
  ['label' => 'Projects', 'url' => route('projects.index')],
  [$editing ? 'Edit' : 'Show'],
]" />

<div class="flex items-center justify-between mb-6">
  <div class="flex items-start gap-3">
    <div class="h-12 w-12 rounded-full bg-primary/10 text-primary ring-1 ring-primary/20 grid place-items-center">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-6 w-6">
        <rect x="3" y="3" width="7" height="18" rx="1"></rect>
        <path d="M3 9h7M3 13h7M3 17h7"></path>
        <rect x="14" y="7" width="7" height="14" rx="1"></rect>
        <path d="M14 11h7M14 15h7M14 19h7"></path>
      </svg>
    </div>
    <div>
      <flux:heading size="xl">{{ $project->project_name }} {{ $project->building?->name_building ?? '—' }}</flux:heading>
      <flux:subheading size="lg">General project details</flux:subheading>
    </div>
  </div>

  <div class="flex items-center gap-2">
    @if(!$editing)
      <flux:button wire:click="startEdit">Edit</flux:button>
      <a wire:navigate href="{{ route('projects.index') }}" class="btn">Back</a>
    @else
      <flux:button variant="primary" wire:click="saveEdit">Save</flux:button>
      <flux:button variant="ghost"   wire:click="cancelEdit">Cancel</flux:button>
    @endif
  </div>
</div>
