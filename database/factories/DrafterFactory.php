<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DrafterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name_drafter' => $this->faker->name(),
        ];
    }
}
