<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    /**
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        $user = new User();

        $user->name = $data['name'];

        $user->save();

        return $user;
    }

    /**
     * @param User $user
     * @param int $points
     * @return User
     */
    public function addPoints(User $user, int $points): User
    {
        $user->score += $points;

        $user->save();

        return $user;
    }
}
