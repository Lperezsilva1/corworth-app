<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_is_system_and_source_to_project_comments_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('project_comments', function (Blueprint $table) {
            $table->boolean('is_system')->default(false)->after('body');
            $table->string('source', 32)->nullable()->after('is_system')->index();
        });
    }
    public function down(): void {
        Schema::table('project_comments', function (Blueprint $table) {
            $table->dropColumn(['is_system','source']);
        });
    }
};
