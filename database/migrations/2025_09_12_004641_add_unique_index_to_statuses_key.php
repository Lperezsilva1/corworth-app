<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Eliminar duplicados de `key` (conserva el de menor id)
        DB::statement("
            DELETE t1 FROM statuses t1
            JOIN statuses t2
              ON t1.`key` = t2.`key`
             AND t1.id > t2.id
        ");

        // 2) Crear índice único si no existe (sin Doctrine)
        $exists = DB::table('information_schema.statistics')
            ->whereRaw('table_schema = DATABASE()')
            ->where('table_name', 'statuses')
            ->where('index_name', 'statuses_key_unique')
            ->exists();

        if (! $exists) {
            DB::statement('CREATE UNIQUE INDEX statuses_key_unique ON statuses (`key`)');
        }
    }

    public function down(): void
    {
        // Quitar el índice si existe
        $exists = DB::table('information_schema.statistics')
            ->whereRaw('table_schema = DATABASE()')
            ->where('table_name', 'statuses')
            ->where('index_name', 'statuses_key_unique')
            ->exists();

        if ($exists) {
            DB::statement('DROP INDEX statuses_key_unique ON statuses');
        }
    }
};
