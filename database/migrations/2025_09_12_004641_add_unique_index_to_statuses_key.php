<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        // 1) Eliminar duplicados por key (MySQL / MariaDB)
        if ($driver === 'mysql') {
            DB::statement("
                DELETE t1 FROM statuses t1
                JOIN statuses t2
                  ON t1.`key` = t2.`key`
                 AND t1.id > t2.id
            ");
        } else {
            // Alternativa genérica con CTE (SQLite/Postgres modernos)
            // Borra filas donde rn > 1
            DB::statement("
                WITH ranked AS (
                  SELECT id, `key`,
                         ROW_NUMBER() OVER (PARTITION BY `key` ORDER BY id) AS rn
                  FROM statuses
                )
                DELETE FROM statuses WHERE id IN (SELECT id FROM ranked WHERE rn > 1)
            ");
        }

        // 2) Crear índice único si no existe
        if ($driver === 'mysql') {
            $exists = DB::table('information_schema.statistics')
                ->whereRaw('table_schema = DATABASE()')
                ->where('table_name', 'statuses')
                ->where('index_name', 'statuses_key_unique')
                ->exists();

            if (! $exists) {
                // MySQL 8+ soporta IF NOT EXISTS; si usas 5.7, quita la cláusula
                DB::statement('CREATE UNIQUE INDEX statuses_key_unique ON statuses (`key`)');
            }
        } else {
            // SQLite / Postgres
            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS statuses_key_unique ON statuses ("key")');
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            $exists = DB::table('information_schema.statistics')
                ->whereRaw('table_schema = DATABASE()')
                ->where('table_name', 'statuses')
                ->where('index_name', 'statuses_key_unique')
                ->exists();

            if ($exists) {
                DB::statement('DROP INDEX statuses_key_unique ON statuses');
            }
        } else {
            DB::statement('DROP INDEX IF EXISTS statuses_key_unique');
        }
    }
};
