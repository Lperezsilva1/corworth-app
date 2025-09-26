{{-- resources/views/livewire/projects/partials/modals.blade.php --}}

{{-- Modal: Deviated --}}
<div x-data
     x-on:open-deviate-modal.window="$refs.deviateDialog.showModal()"
     class="relative">
  <dialog x-ref="deviateDialog" class="modal" x-on:keydown.escape="$refs.deviateDialog.close()">
    <div class="modal-box">
      <h3 class="font-bold text-lg">Move to Deviated</h3>
      <p class="py-3 text-sm">
        This will return the project to <span class="font-semibold">Deviated</span> for internal reviews.
        You can approve later once everything is ready.
      </p>

      <div class="modal-action">
        <flux:button class="btn btn-ghost" @click="$refs.deviateDialog.close()">Cancel</flux:button>

        {{-- Amarillo warning sin variant --}}
        <flux:button
          class="btn btn-warning"
          wire:click="markAsDeviated"
          wire:loading.attr="disabled"
          wire:target="markAsDeviated"
          @click="$refs.deviateDialog.close()">
          Confirm Deviated
        </flux:button>
      </div>
    </div>
    <form method="dialog" class="modal-backdrop">
      <button>close</button>
    </form>
  </dialog>
</div>

{{-- Modal: Approve --}}
<div x-data
     x-on:open-approve-modal.window="$refs.approveDialog.showModal()"
     class="relative">
  <dialog x-ref="approveDialog" class="modal" x-on:keydown.escape="$refs.approveDialog.close()">
    <div class="modal-box">
      <h3 class="font-bold text-lg">Approve project</h3>
      <p class="py-3 text-sm">
        This will mark the project as <span class="font-semibold">Approved</span>.
        Make sure both phases are <em>Complete</em>.
      </p>

      <div class="modal-action">
        <flux:button class="btn btn-ghost" @click="$refs.approveDialog.close()">Cancel</flux:button>

        {{-- Verde success sin variant --}}
        <flux:button
          class="btn btn-success"
          wire:click="approveProject"
          wire:loading.attr="disabled"
          wire:target="approveProject"
          @click="$refs.approveDialog.close()">
          Confirm Approve
        </flux:button>
      </div>
    </div>
    <form method="dialog" class="modal-backdrop">
      <button>close</button>
    </form>
  </dialog>
</div>

{{-- Modal: Phase 1 Complete (mini) --}}
<div x-data
     x-on:open-phase1-complete-modal.window="$refs.p1CompleteDialog.showModal()"
     class="relative">
  <dialog x-ref="p1CompleteDialog" class="modal" x-on:keydown.escape="$refs.p1CompleteDialog.close()">
    <div class="modal-box w-11/12 max-w-sm">
      <h3 class="font-bold text-lg">Complete Phase 1</h3>
      <p class="py-2 text-sm">
        This will set Phase 1 status to <span class="font-semibold">Complete</span> and stamp the end date as today.
      </p>

      <div class="modal-action">
        <flux:button class="btn btn-ghost" @click="$refs.p1CompleteDialog.close()">Cancel</flux:button>

        <flux:button
          class="btn btn-success"
          wire:click="markPhase1Complete"
          wire:loading.attr="disabled"
          wire:target="markPhase1Complete"
          @click="$refs.p1CompleteDialog.close()">
          Confirm
        </flux:button>
      </div>
    </div>
    <form method="dialog" class="modal-backdrop">
      <button>close</button>
    </form>
  </dialog>
</div>

{{-- Modal: Full Set Complete (mini) --}}
<div x-data
     x-on:open-fullset-complete-modal.window="$refs.fsCompleteDialog.showModal()"
     class="relative">
  <dialog x-ref="fsCompleteDialog" class="modal" x-on:keydown.escape="$refs.fsCompleteDialog.close()">
    <div class="modal-box w-11/12 max-w-sm">
      <h3 class="font-bold text-lg">Complete Full Set</h3>
      <p class="py-2 text-sm">
        This will set Full Set to <span class="font-semibold">Complete</span> and stamp the end date as today.
      </p>

      <div class="modal-action">
        <flux:button class="btn btn-ghost" @click="$refs.fsCompleteDialog.close()">Cancel</flux:button>

        <flux:button
          class="btn btn-success"
          wire:click="markFullsetComplete"
          wire:loading.attr="disabled"
          wire:target="markFullsetComplete"
          @click="$refs.fsCompleteDialog.close()">
          Confirm
        </flux:button>
      </div>
    </div>
    <form method="dialog" class="modal-backdrop">
      <button>close</button>
    </form>
  </dialog>
</div>
