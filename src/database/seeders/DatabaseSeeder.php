<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(2)->create();
        \App\Models\Building::factory(2)->create();
        \App\Models\Task::factory(3)->create();
        \App\Models\Comment::factory(10)->create();
    }
}
