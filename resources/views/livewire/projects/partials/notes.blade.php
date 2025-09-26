<div class="rounded-md border border-base-300 bg-base-100 shadow-sm p-4">
  <flux:subheading size="sm" class="mb-3">Notes</flux:subheading>

  @if($editing)
    <flux:field>
      <flux:label for="notes">Notes</flux:label>
      <flux:textarea id="notes" wire:model.defer="notes" rows="4" placeholder="Project notes" class="w-full" />
      <flux:error name="notes" class="text-xs text-error mt-1" />
    </flux:field>
  @else
    <flux:text class="whitespace-pre-line">{{ $project->notes ?: '—' }}</flux:text>
  @endif
</div>
