<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $driver = DB::getDriverName(); // 'mysql' | 'sqlite' | etc.

        // 1) Columna temporal
        Schema::table('projects', function (Blueprint $table) use ($driver) {
            // en SQLite evito AFTER y cambios complejos
            $table->unsignedBigInteger('general_status_new')->nullable();
        });

        // 2) Backfill sin JOIN: con subconsultas (válido en MySQL y SQLite)
        DB::statement("
            UPDATE projects
            SET general_status_new = CASE
                WHEN general_status = 'approved' THEN (
                    SELECT id FROM statuses WHERE `key` = 'approved' LIMIT 1
                )
                WHEN general_status = 'not_approved' THEN (
                    SELECT id FROM statuses WHERE `key` = 'awaiting_approval' LIMIT 1
                )
                WHEN general_status = 'cancelled' THEN (
                    SELECT id FROM statuses WHERE `key` = 'cancelled' LIMIT 1
                )
                WHEN general_status IS NULL OR general_status = '' THEN (
                    SELECT id FROM statuses WHERE `key` = 'pending' LIMIT 1
                )
                ELSE (
                    SELECT id FROM statuses WHERE `key` = 'pending' LIMIT 1
                )
            END
        ");

        // 3) Restricciones (solo en MySQL: SQLite lo dejo laxo para tests)
        if ($driver === 'mysql') {
            // NOT NULL
            Schema::table('projects', function (Blueprint $table) {
                // requiere doctrine/dbal si tu versión no soporta change nativo
                $table->unsignedBigInteger('general_status_new')->nullable(false)->change();
            });

            // FK
            Schema::table('projects', function (Blueprint $table) {
                $table->foreign('general_status_new')
                    ->references('id')->on('statuses')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
            });
        }

        // 4) Eliminar string viejo y renombrar
        //    En SQLite, Laravel reconstruye la tabla; si tu entorno local se queja,
        //    instala: composer require doctrine/dbal --dev
        Schema::table('projects', function (Blueprint $table) {
            // Borro la vieja (string)
            if (Schema::hasColumn('projects', 'general_status')) {
                $table->dropColumn('general_status');
            }
        });

        Schema::table('projects', function (Blueprint $table) {
            // Renombro la temporal a definitiva
            $table->renameColumn('general_status_new', 'general_status');
        });
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        // Si bajamos, volvemos a string
        // 1) Renombrar columna actual a *_id para poder crear el string
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'general_status')) {
                $table->renameColumn('general_status', 'general_status_id');
            }
        });

        // 2) Crear string
        Schema::table('projects', function (Blueprint $table) {
            $table->string('general_status')->nullable()->after('general_status_id');
        });

        // 3) Backfill inverso usando subconsulta
        DB::statement("
            UPDATE projects
            SET general_status = (
                SELECT `key` FROM statuses WHERE statuses.id = projects.general_status_id
                LIMIT 1
            )
        ");

        // 4) Quitar FK solo si existía (MySQL)
        if ($driver === 'mysql' && Schema::hasColumn('projects', 'general_status_id')) {
            // El nombre de la FK podría variar; uso arreglo para drop por columna
            Schema::table('projects', function (Blueprint $table) {
                try {
                    $table->dropForeign(['general_status_id']);
                } catch (\Throwable $e) {
                    // si no existía, ignorar
                }
            });
        }

        // 5) Eliminar la *_id
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'general_status_id')) {
                $table->dropColumn('general_status_id');
            }
        });
    }
};
