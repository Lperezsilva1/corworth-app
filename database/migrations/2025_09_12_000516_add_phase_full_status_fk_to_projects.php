<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $t) {
            // Crea columnas FK como nullable (el seeder hará el backfill)
            if (!Schema::hasColumn('projects', 'phase1_status_id')) {
                $t->foreignId('phase1_status_id')
                  ->nullable()
                  ->constrained('statuses')
                  ->nullOnDelete(); // o ->cascadeOnDelete() si prefieres
            }

            if (!Schema::hasColumn('projects', 'fullset_status_id')) {
                $t->foreignId('fullset_status_id')
                  ->nullable()
                  ->constrained('statuses')
                  ->nullOnDelete();
            }

            // Si también manejas general_status_id, descomenta:
            // if (!Schema::hasColumn('projects', 'general_status_id')) {
            //     $t->foreignId('general_status_id')
            //       ->nullable()
            //       ->constrained('statuses')
            //       ->nullOnDelete();
            // }
        });

        // 👇 Importante:
        // No buscar IDs ni lanzar excepciones aquí.
        // El mapeo/valores por defecto van en un seeder de backfill.
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $t) {
            if (Schema::hasColumn('projects', 'phase1_status_id')) {
                $t->dropForeign(['phase1_status_id']);
                $t->dropColumn('phase1_status_id');
            }
            if (Schema::hasColumn('projects', 'fullset_status_id')) {
                $t->dropForeign(['fullset_status_id']);
                $t->dropColumn('fullset_status_id');
            }
            // if (Schema::hasColumn('projects', 'general_status_id')) {
            //     $t->dropForeign(['general_status_id']);
            //     $t->dropColumn('general_status_id');
            // }
        });
    }
};
