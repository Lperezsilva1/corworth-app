<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            // Básicos
            $table->string('project_name');

            // Relaciones
            $table->foreignId('building_id')->nullable()
                  ->constrained('buildings')->nullOnDelete();
            $table->foreignId('seller_id')->nullable()
                  ->constrained('sellers')->nullOnDelete();

            // Phase 1 (un solo drafter)
            $table->foreignId('phase1_drafter_id')->nullable()
                  ->constrained('drafters')->nullOnDelete();
            $table->string('phase1_status', 64)->nullable();   // "Working on Phase 1's" | "Phase 1's Complete"
            $table->date('phase1_start_date')->nullable();
            $table->date('phase1_end_date')->nullable();

            // Full Set (opcional)
            $table->foreignId('fullset_drafter_id')->nullable()
                  ->constrained('drafters')->nullOnDelete();
            $table->string('fullset_status', 64)->nullable();  // "Working on Full Set" | "Ready w/o approval" | "Full Set Complete"
            $table->date('fullset_start_date')->nullable();
            $table->date('fullset_end_date')->nullable();

            // General
            $table->string('general_status', 32)->nullable();  // "Approved" | "Not Approved" | "Cancelled"
            $table->text('notes')->nullable();

            $table->timestamps();
            // $table->softDeletes(); // ← descomenta si usarás papelera

            // Índices útiles para filtros/búsquedas
            $table->index(['seller_id', 'building_id']);
            $table->index(['phase1_drafter_id', 'phase1_status']);
            $table->index(['fullset_drafter_id', 'fullset_status']);
            $table->index('general_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
