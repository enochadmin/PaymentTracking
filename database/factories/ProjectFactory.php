<?php

namespace Database\Factories;

use App\Models\Discipline;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_identifier' => $this->faker->unique()->word(3, true),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'discipline_id' => Discipline::factory(),
            'client_name' => $this->faker->company,
            'project_manager' => $this->faker->name,
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date('Y-m-d', '+1 year'),
            'status' => $this->faker->randomElement(['Planned', 'Active', 'Closed']),
        ];
    }
}
