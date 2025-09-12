<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'key'            => 'pending',
                'label'          => 'Pending',
                'ui_class'       => 'badge badge-warning',
                'display_order'  => 1,
                'is_final'       => false,
                'is_active'      => true,
                'description'    => 'Not started yet',
            ],
            [
                'key'            => 'working',
                'label'          => 'Working',
                'ui_class'       => 'badge badge-info',
                'display_order'  => 2,
                'is_final'       => false,
                'is_active'      => true,
                'description'    => 'In progress',
            ],
            // ✅ Solo para fases (Phase 1 / Full Set)
            [
                'key'            => 'complete',
                'label'          => 'Complete',
                'ui_class'       => 'badge badge-primary',
                'display_order'  => 3,
                'is_final'       => false,
                'is_active'      => true,
                'description'    => 'Phase finished (Phase 1 / Full Set only)',
            ],
            [
                'key'            => 'awaiting_approval',
                'label'          => 'Awaiting Approval',
                'ui_class'       => 'badge badge-secondary',
                'display_order'  => 4,
                'is_final'       => false,
                'is_active'      => true,
                'description'    => 'Waiting for PFS approval',
            ],
            [
                'key'            => 'approved',
                'label'          => 'Approved',
                'ui_class'       => 'badge badge-success',
                'display_order'  => 5,
                'is_final'       => true,
                'is_active'      => true,
                'description'    => 'Approved and ready',
            ],
            [
                'key'            => 'cancelled',
                'label'          => 'Cancelled',
                'ui_class'       => 'badge',
                'display_order'  => 6,
                'is_final'       => true,
                'is_active'      => true,
                'description'    => 'Cancelled by client/team',
            ],
        ];

        foreach ($rows as $r) {
            Status::updateOrCreate(['key' => $r['key']], $r);
        }
    }
}
