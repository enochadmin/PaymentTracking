<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Team;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Team::create(['name' => 'Commercial']);
        Team::create(['name' => 'Operation']);
        Team::create(['name' => 'Procurement']);
        Team::create(['name' => 'Finance']);
    }
}
