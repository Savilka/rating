<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function createUser(array $data): User
    {
        $user = new User();

        $user->name = $data['name'];

        $user->save();

        return $user;
    }

    public function addPoints(User $user, int $points): User
    {
        $user->score += $points;

        $user->save();

        return $user;
    }
}
