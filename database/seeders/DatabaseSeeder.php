<?php

namespace Database\Seeders;

use App\Models\User;

use App\Repositories\ScoreTransactionRepository;
use App\Repositories\UserRepository;
use App\Services\ScoreTransactionService;
use App\Services\UserService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(10000)->create()->each(function ($user) {
            $userService = new UserService(new UserRepository(), new ScoreTransactionService(new ScoreTransactionRepository()));
            for ($i = 0; $i < 1000; $i++) {
                $userService->addPointsToUser($user, rand(1, 100));
            }
        });
    }
}
