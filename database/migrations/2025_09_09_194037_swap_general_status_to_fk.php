<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) Columna temporal INT
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedBigInteger('general_status_new')->nullable()->after('general_status');
        });

        // 2) Backfill (ajusta el mapeo de ser necesario)
        //    Asumimos valores viejos: 'approved', 'not_approved', 'cancelled'
        //    not_approved -> awaiting_approval (tu nuevo flujo)
        DB::statement("
            UPDATE projects p
            LEFT JOIN statuses s_pending   ON s_pending.`key`   = 'pending'
            LEFT JOIN statuses s_working   ON s_working.`key`   = 'working'
            LEFT JOIN statuses s_await     ON s_await.`key`     = 'awaiting_approval'
            LEFT JOIN statuses s_approved  ON s_approved.`key`  = 'approved'
            LEFT JOIN statuses s_cancelled ON s_cancelled.`key` = 'cancelled'
            SET p.general_status_new = CASE
                WHEN p.general_status = 'approved'     THEN s_approved.id
                WHEN p.general_status = 'not_approved' THEN s_await.id
                WHEN p.general_status = 'cancelled'    THEN s_cancelled.id
                WHEN p.general_status IS NULL OR p.general_status = '' THEN s_pending.id
                ELSE s_pending.id
            END
        ");

        // 3) NOT NULL + FK
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedBigInteger('general_status_new')->nullable(false)->change();
            $table->foreign('general_status_new')
                  ->references('id')->on('statuses')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
        });

        // 4) Eliminar string viejo y renombrar la nueva columna
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('general_status');
        });
        // Si al renombrar te pide doctrine/dbal, instala:
        // composer require doctrine/dbal
        Schema::table('projects', function (Blueprint $table) {
            $table->renameColumn('general_status_new', 'general_status');
        });
    }

    public function down(): void
    {
        // Rollback: volver a string
        Schema::table('projects', function (Blueprint $table) {
            $table->renameColumn('general_status', 'general_status_id');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->string('general_status')->nullable()->after('general_status_id');
        });

        DB::statement("
            UPDATE projects p
            LEFT JOIN statuses s ON s.id = p.general_status_id
            SET p.general_status = s.`key`
        ");

        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['general_status_id']);
            $table->dropColumn('general_status_id');
        });
    }
};
