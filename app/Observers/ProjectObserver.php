<?php


namespace App\Observers;

use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;


class ProjectObserver
{
    public function saved(Project $p): void
    {
        if ($p->wasChanged(['phase1_status_id','fullset_status_id','phase1_drafter_id','fullset_drafter_id'])) {
            $p->recalcGeneralStatus();
        }
    }


     /** NUEVO: notificar al crear */
    public function created(Project $project): void
    {
        if (app()->runningInConsole()) return; // evita ruido en seeders/tests

        $actor = auth()->user();
        $recipients = $this->recipients($actor?->id);

        Notification::send(
            $recipients,
            new ProjectNotification('created', $project, $actor?->id, $actor?->name)
        );
    }




       /** NUEVO: notificar al eliminar */
    public function deleted(Project $project): void
    {
        if (app()->runningInConsole()) return;

        $actor = auth()->user();
        $recipients = $this->recipients($actor?->id);

        Notification::send(
            $recipients,
            new ProjectNotification('deleted', $project, $actor?->id, $actor?->name)
        );
    }


      /** Helpers */
    protected function recipients(?int $actorId)
    {
        return User::query()
            ->when($actorId, fn($q) => $q->whereKeyNot($actorId)) // excluye autor
            // ->where('team_id', optionalTenantId()) // si usas multi-tenant
            ->get();
    }


}
