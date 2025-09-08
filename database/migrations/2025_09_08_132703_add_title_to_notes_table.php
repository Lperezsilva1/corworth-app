<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('project_comments', function (Blueprint $table) {
        $table->string('title', 255)->after('id');
    });
}

public function down(): void
{
    Schema::table('project_comments', function (Blueprint $table) {
        $table->dropColumn('title');
    });
}
};
