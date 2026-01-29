<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bank>
 */
class BankFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'branch' => $this->faker->city . ' Branch',
            'address' => $this->faker->address,
            'contact_person' => $this->faker->name,
            'bank_number' => $this->faker->numerify('##########'),
        ];
    }
}
