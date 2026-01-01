<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        // Chunked creation to avoid memory issues
        Customer::factory()
            ->count(100) // adjust as needed
            ->create();
    }
}