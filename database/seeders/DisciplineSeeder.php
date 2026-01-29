<?php

namespace Database\Seeders;

use App\Models\Discipline;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DisciplineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Discipline::create(['name' => 'Software Development']);
        Discipline::create(['name' => 'Web Design']);
        Discipline::create(['name' => 'Project Management']);
        Discipline::create(['name' => 'Quality Assurance']);
    }
}
