<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Building;
use App\Models\Seller;
use App\Models\Drafter;
use App\Models\Project;

class ProjectsDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Garantizar algo de catálogo base para relaciones
        Building::factory(10)->create();
        Seller::factory(8)->create();
        Drafter::factory(12)->create();

        // Crear proyectos
        Project::factory(80)->create();
    }
}
