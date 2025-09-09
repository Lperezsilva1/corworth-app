<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();          // 'pending','approved','rejected','cancelled'
            $table->string('label');                  // 'Pending','Approved',...
            $table->string('ui_class')->nullable();   // 'badge badge-success', etc.
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_final')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};
