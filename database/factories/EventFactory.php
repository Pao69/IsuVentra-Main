<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = \App\Models\Event::class;

    public function definition()
    {
        $start = $this->faker->dateTimeBetween('+1 days', '+10 days');
        $end = (clone $start)->modify('+2 hours');

        return [
            'title' => $this->faker->sentence(3),
            'time_start' => $start,
            'time_end' => $end,
            'description' => $this->faker->paragraph(),
            'location' => $this->faker->city(),
        ];
    }
}
