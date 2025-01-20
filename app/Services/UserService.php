<?php

namespace App\Services;

use App\Models\User;
use App\Repositories;

class UserService
{
    public function __construct(
        protected Repositories\UserRepository $userRepository,
        protected ScoreTransactionService $scoreTransactionService
    ) {
    }

    public function createNewUserFromData(array $data): User
    {
        return $this->userRepository->createUser($data);
    }

    public function addPointsToUser(User $user, int $points): User
    {
        $user = $this->userRepository->addPoints($user, $points);
        $this->scoreTransactionService->addNewTransaction($user, $points);

        return $user;
    }
}
