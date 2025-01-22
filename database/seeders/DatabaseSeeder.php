<?php

namespace Database\Seeders;

use App\Models\ScoreTransaction;
use App\Models\User;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(10000)->has(ScoreTransaction::factory()->count(10000))->create();
    }
}
