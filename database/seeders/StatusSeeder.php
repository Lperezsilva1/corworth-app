<?php
// database/seeders/StatusSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['key'=>'pending',            'label'=>'Pending',            'ui_class'=>'badge badge-warning', 'display_order'=>1, 'is_final'=>false, 'is_active'=>true, 'description'=>'Not started yet'],
            ['key'=>'working',            'label'=>'Working',            'ui_class'=>'badge badge-info',    'display_order'=>2, 'is_final'=>false, 'is_active'=>true, 'description'=>'In progress'],
            ['key'=>'awaiting_approval',  'label'=>'Awaiting Approval',  'ui_class'=>'badge badge-secondary','display_order'=>3, 'is_final'=>false, 'is_active'=>true, 'description'=>'Waiting for PFS approval'],
            ['key'=>'approved',           'label'=>'Approved',           'ui_class'=>'badge badge-success', 'display_order'=>4, 'is_final'=>true,  'is_active'=>true, 'description'=>'Approved and ready'],
            ['key'=>'cancelled',          'label'=>'Cancelled',          'ui_class'=>'badge',               'display_order'=>5, 'is_final'=>true,  'is_active'=>true, 'description'=>'Cancelled by client/team'],
        ];

        Status::upsert($rows, ['key'], ['label','ui_class','display_order','is_final','is_active','description']);
    }
}
