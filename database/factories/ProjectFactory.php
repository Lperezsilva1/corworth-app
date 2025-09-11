<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Building;
use App\Models\Seller;
use App\Models\Drafter;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        // Asegurar que existan relaciones (si no hay, crear una)
        $buildingId = Building::inRandomOrder()->value('id') ?? Building::factory()->create()->id;
        $sellerId   = Seller::inRandomOrder()->value('id')   ?? Seller::factory()->create()->id;
        $p1Id       = Drafter::inRandomOrder()->value('id')  ?? Drafter::factory()->create()->id;
        $fsId       = Drafter::inRandomOrder()->value('id')  ?? Drafter::factory()->create()->id;

        // Map de estados válidos del catálogo
        $statusKey = $this->faker->randomElement([
            'pending', 'working', 'awaiting_approval', 'approved', 'cancelled'
        ]);

        $statusId = Status::where('key', $statusKey)->value('id'); // requiere que ya exista StatusSeeder

        // Fechas coherentes
        $start1 = $this->faker->optional(0.8)->dateTimeBetween('-30 days', 'now');
        $end1   = $start1 ? $this->faker->optional(0.6)->dateTimeBetween($start1, '+20 days') : null;

        $startF = $this->faker->optional(0.6)->dateTimeBetween('-15 days', 'now');
        $endF   = $startF ? $this->faker->optional(0.5)->dateTimeBetween($startF, '+25 days') : null;

        return [
            'project_name'        => $this->faker->words(3, true) . ' ' . $this->faker->bothify('B###'),

            'building_id'         => $buildingId,
            'seller_id'           => $sellerId,

            'phase1_drafter_id'   => $p1Id,
            'phase1_status'       => $this->faker->randomElement(["Phase 1's Complete", 'In Progress', 'Queued', '']),
            'phase1_start_date'   => $start1,
            'phase1_end_date'     => $end1,

            'fullset_drafter_id'  => $fsId,
            'fullset_status'      => $this->faker->randomElement(["Full Set Complete", 'In Progress', 'Queued', '']),
            'fullset_start_date'  => $startF,
            'fullset_end_date'    => $endF,

            // tu modelo ya fuerza 1 (pending) en booted si viene null,
            // pero aquí lo elegimos explícitamente desde catálogo:
            'general_status'      => $statusId ?? 1,

            'notes'               => $this->faker->optional()->sentence(),
        ];
    }
}
