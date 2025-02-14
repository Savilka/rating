<?php

namespace App\Services;

use App\Models\ScoreTransaction;
use App\Models\User;
use App\Repositories;

class ScoreTransactionService
{
    public function __construct(
        protected Repositories\ScoreTransactionRepository $scoreTransactionRepository
    ) {
    }

    /**
     * @param User $user
     * @param int $score
     * @return ScoreTransaction
     */
    public function addNewTransaction(User $user, int $score): ScoreTransaction
    {
        return $this->scoreTransactionRepository->addNewTransaction($user, $score);
    }
}
