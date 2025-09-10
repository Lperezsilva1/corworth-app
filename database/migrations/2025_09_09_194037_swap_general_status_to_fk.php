<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $driver = DB::getDriverName(); // 'mysql' | 'sqlite' | etc.

        // 1) Columna temporal (sin AFTER para compatibilidad)
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedBigInteger('general_status_new')->nullable();
        });

        // 2) Backfill sin JOIN (compatible con MySQL y SQLite)
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

        // 3) Endurecer sólo en MySQL: NOT NULL + FK
        if ($driver === 'mysql') {
            Schema::table('projects', function (Blueprint $table) {
                // requiere doctrine/dbal para ->change() si tu versión no lo soporta nativo
                $table->unsignedBigInteger('general_status_new')->nullable(false)->change();
            });

            Schema::table('projects', function (Blueprint $table) {
                $table->foreign('general_status_new')
                      ->references('id')->on('statuses')
                      ->cascadeOnUpdate()
                      ->restrictOnDelete();
            });
        }

        // 3.5) Quitar índice antiguo sobre general_status si existía
        $this->dropIndexIfExists('projects', 'projects_general_status_index');
        try {
            Schema::table('projects', function (Blueprint $table) {
                // por si el índice fue creado automáticamente por nombre de columna
                $table->dropIndex(['general_status']);
            });
        } catch (\Throwable $e) {
            // ignorar si no existe
        }

        // 4) Borrar string viejo y renombrar la nueva columna
        Schema::disableForeignKeyConstraints();

        // Borrar columna vieja si aún existe
        if (Schema::hasColumn('projects', 'general_status')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropColumn('general_status');
            });
        }

        // Renombrar temporal -> definitiva
        Schema::table('projects', function (Blueprint $table) {
            $table->renameColumn('general_status_new', 'general_status');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        // 1) Renombrar la actual a *_id para recrear string
        if (Schema::hasColumn('projects', 'general_status')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->renameColumn('general_status', 'general_status_id');
            });
        }

        // 2) Crear string original
        Schema::table('projects', function (Blueprint $table) {
            $table->string('general_status')->nullable();
        });

        // 3) Backfill inverso (id -> key)
        DB::statement("
            UPDATE projects
            SET general_status = (
                SELECT `key`
                FROM statuses
                WHERE statuses.id = projects.general_status_id
                LIMIT 1
            )
        ");

        // 4) Quitar FK sólo si existía (MySQL)
        if ($driver === 'mysql' && Schema::hasColumn('projects', 'general_status_id')) {
            Schema::table('projects', function (Blueprint $table) {
                try {
                    $table->dropForeign(['general_status_id']);
                } catch (\Throwable $e) {
                    // ignorar si no existía
                }
            });
        }

        // 5) Eliminar *_id
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'general_status_id')) {
                $table->dropColumn('general_status_id');
            }
        });
    }

    // Helper para dropear un índice si existe (SQLite/MySQL)
    private function dropIndexIfExists(string $table, string $index): void
    {
        $driver = DB::getDriverName();

        try {
            if ($driver === 'sqlite') {
                $exists = DB::selectOne(
                    "SELECT name FROM sqlite_master WHERE type='index' AND name=?",
                    [$index]
                );
                if ($exists) {
                    DB::statement('DROP INDEX "'.$index.'"');
                }
            } else {
                Schema::table($table, function (Blueprint $t) use ($index) {
                    try { $t->dropIndex($index); } catch (\Throwable $e) {}
                });
            }
        } catch (\Throwable $e) {
            // silencioso
        }
    }
};
