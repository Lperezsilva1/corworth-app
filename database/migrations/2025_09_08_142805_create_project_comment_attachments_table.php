<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_project_comment_attachments_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_comment_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_comment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('original_name');
            $table->string('disk')->default('public');
            $table->string('path');
            $table->unsignedBigInteger('size');
            $table->string('mime', 191)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_comment_attachments');
    }
};
