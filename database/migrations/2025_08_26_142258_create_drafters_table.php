<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('drafters', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->text('description_drafter')->nullable(); // descripción
            $table->boolean('status')->default(true); // activo/inactivo  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drafters');
    }
};
