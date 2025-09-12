<?php


namespace App\Observers;

use App\Models\Project;

class ProjectObserver
{
    public function saved(Project $p): void
    {
        if ($p->wasChanged(['phase1_status_id','fullset_status_id','phase1_drafter_id','fullset_drafter_id'])) {
            $p->recalcGeneralStatus();
        }
    }
}
