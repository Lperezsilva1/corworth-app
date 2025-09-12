<?php
// database/migrations/xxxx_xx_xx_add_phase_full_status_fk_to_projects.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::table('projects', function (Blueprint $t) {
            // nuevas columnas FK (puedes agregar solo la que necesites)
            if (!Schema::hasColumn('projects', 'phase1_status_id')) {
                $t->foreignId('phase1_status_id')->nullable()->constrained('statuses');
            }
            if (!Schema::hasColumn('projects', 'fullset_status_id')) {
                $t->foreignId('fullset_status_id')->nullable()->constrained('statuses');
            }
        });

        // Backfill desde los strings actuales a IDs por key
        $keys = ['pending','working','complete','awaiting_approval','approved'];
        $ids  = collect($keys)->mapWithKeys(function ($k) {
            return [$k => DB::table('statuses')->where('key',$k)->value('id')];
        });

        // Si alguna key falta, evita nulls peligrosos
        if (!$ids['pending']) {
            throw new \RuntimeException("Falta el status 'pending' en tabla statuses");
        }

        // phase1_status -> phase1_status_id
        if (Schema::hasColumn('projects','phase1_status') && Schema::hasColumn('projects','phase1_status_id')) {
            DB::table('projects')->where('phase1_status','pending')->update(['phase1_status_id'=>$ids['pending']]);
            DB::table('projects')->where('phase1_status','working')->update(['phase1_status_id'=>$ids['working']]);
            DB::table('projects')->where('phase1_status','complete')->update(['phase1_status_id'=>$ids['complete']]);
        }

        // fullset_status -> fullset_status_id
        if (Schema::hasColumn('projects','fullset_status') && Schema::hasColumn('projects','fullset_status_id')) {
            DB::table('projects')->where('fullset_status','pending')->update(['fullset_status_id'=>$ids['pending']]);
            DB::table('projects')->where('fullset_status','working')->update(['fullset_status_id'=>$ids['working']]);
            DB::table('projects')->where('fullset_status','complete')->update(['fullset_status_id'=>$ids['complete']]);
        }
    }

    public function down(): void {
        Schema::table('projects', function (Blueprint $t) {
            if (Schema::hasColumn('projects','phase1_status_id')) {
                $t->dropConstrainedForeignId('phase1_status_id');
            }
            if (Schema::hasColumn('projects','fullset_status_id')) {
                $t->dropConstrainedForeignId('fullset_status_id');
            }
        });
    }
};
