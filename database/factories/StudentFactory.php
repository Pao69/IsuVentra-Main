<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = \App\Models\Student::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(), // create a linked user if not provided
            'student_id' => $this->faker->unique()->numerify('S####'),
            'name' => $this->faker->name(),
            'course' => $this->faker->randomElement(['BSCS', 'BSIT', 'BSECE']),
            'year_lvl' => $this->faker->numberBetween(1, 4),
            'campus' => $this->faker->randomElement(['Main', 'Satellite']),
        ];
    }
}
