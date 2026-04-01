<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Currency::create([
            'nom' => "MAD",
        ]);
        Currency::create([
            'nom' => "EUR",
        ]);
        Currency::create([
            'nom' => "USD",
        ]);
    }
}
