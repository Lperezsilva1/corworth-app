<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Index names (so we can drop them reliably in down())
    private const IDX_CREATED_ID      = 'pc_created_id_idx';
    private const IDX_PROJECT_CREATED = 'pc_project_created_idx';
    private const IDX_USER_CREATED    = 'pc_user_created_idx';

    public function up(): void
    {
        Schema::table('project_comments', function (Blueprint $table) {
            // For timeline order & cursor pagination: created_at DESC, id DESC
            $table->index(['created_at', 'id'], self::IDX_CREATED_ID);

            // For filtering by project with recent-first order
            $table->index(['project_id', 'created_at'], self::IDX_PROJECT_CREATED);

            // For filtering by user with recent-first order
            $table->index(['user_id', 'created_at'], self::IDX_USER_CREATED);
        });
    }

    public function down(): void
    {
        Schema::table('project_comments', function (Blueprint $table) {
            $table->dropIndex(self::IDX_CREATED_ID);
            $table->dropIndex(self::IDX_PROJECT_CREATED);
            $table->dropIndex(self::IDX_USER_CREATED);
        });
    }
};
