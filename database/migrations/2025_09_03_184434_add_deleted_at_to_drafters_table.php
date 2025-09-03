<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('drafters', function (Blueprint $table) {
            $table->softDeletes(); // agrega deleted_at
        });
    }

    public function down(): void
    {
        Schema::table('drafters', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
