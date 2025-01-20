<?php

namespace App\Services;

use App\Models\ScoreTransaction;
use App\Models\User;
use App\Repositories;
use Illuminate\Support\Collection;

class LeaderboardService
{
    public function __construct(
        protected Repositories\UserRepository $userRepository,
        protected Repositories\ScoreTransactionRepository $scoreTransactionRepository
    ) {
    }

    public function getUserRankAndScoreByPeriod(User $user, string $period): array
    {
        $userRankCollection = $this->scoreTransactionRepository->getUserRankByPeriod($user, $period);
        if (count($userRankCollection) !== 0) {
            $userRank = $userRankCollection->first();
            return [$userRank->position, $userRank->total];
        } else {
            return [0, 0];
        }
    }

    public function getTopTenUsersInLeaderboard(string $period): Collection
    {
        return $this->scoreTransactionRepository->getTopTenUsersInLeaderboard($period);
    }
}
