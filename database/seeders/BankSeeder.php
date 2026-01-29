<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Bank::create([
            'name' => 'National Bank',
            'branch' => 'Main Branch',
            'address' => '123 Main St',
            'contact_person' => 'John Doe',
        ]);

        Bank::create([
            'name' => 'Community Credit',
            'branch' => 'Downtown Branch',
            'address' => '456 Oak Ave',
            'contact_person' => 'Jane Smith',
        ]);
    }
}
