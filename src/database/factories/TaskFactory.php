<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\Building;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title'       => fake()->sentence(),
            'description' => 'The task to be performed is ' . fake()->paragraph(),
            'status'      => fake()->randomElement(['Open', 'In Progress', 'Completed', 'Rejected']),
            'building_id' => Building::inRandomOrder()->first()->id,
            'created_by'  => User::inRandomOrder()->first()->id,
            'assigned_to' => fake()->boolean(70) ? User::inRandomOrder()->first()->id : null,
        ];
    }
}
