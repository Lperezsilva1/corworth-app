<?php

// database/migrations/2025_09_08_000001_add_seller_items_to_projects_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('projects', function (Blueprint $table) {
            // 6 ítems fijos (ok + notas)
            $table->boolean('seller_door_ok')->default(false);
            $table->text('seller_door_notes')->nullable();

            $table->boolean('seller_accessories_ok')->default(false);
            $table->text('seller_accessories_notes')->nullable();

            $table->boolean('seller_exterior_finish_ok')->default(false);
            $table->text('seller_exterior_finish_notes')->nullable();

            $table->boolean('seller_plumbing_fixture_ok')->default(false);
            $table->text('seller_plumbing_fixture_notes')->nullable();

            $table->boolean('seller_utility_direction_ok')->default(false);
            $table->text('seller_utility_direction_notes')->nullable();

            $table->boolean('seller_electrical_ok')->default(false);
            $table->text('seller_electrical_notes')->nullable();

            // “Otro” (opcional)
            $table->boolean('other_ok')->default(false);
            $table->string('other_label', 120)->nullable();
            $table->text('other_notes')->nullable();
        });
    }

    public function down(): void {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'seller_door_ok','seller_door_notes',
                'seller_accessories_ok','seller_accessories_notes',
                'seller_exterior_finish_ok','seller_exterior_finish_notes',
                'seller_plumbing_fixture_ok','seller_plumbing_fixture_notes',
                'seller_utility_direction_ok','seller_utility_direction_notes',
                'seller_electrical_ok','seller_electrical_notes',
                'other_ok','other_label','other_notes',
            ]);
        });
    }
};
