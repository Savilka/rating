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

    /**
     * @param User $user
     * @param string $period
     * @return array|int[]
     */
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

    /**
     * @param User $user
     * @param string $period
     * @return array
     */
    public function getUserRankAndScoreByPeriodNew(User $user, string $period): array
    {
        $scoreTransactions = $this->scoreTransactionRepository->getTransactionsByPeriod($period);
        $userScoresById    = [];
        foreach ($scoreTransactions as $scoreTransaction) {
            $userScoresById[$scoreTransaction->user_id] = $scoreTransaction->total;
        }

        if (!array_key_exists($user->id, $userScoresById)) {
            return [count($userScoresById), 0];
        }

        uasort($userScoresById, function ($a, $b) {
            if ($a == $b) {
                return 0;
            }
            return ($a > $b) ? -1 : 1;
        });

        return [array_search($user->id, array_keys($userScoresById)) + 1, $userScoresById[$user->id]];
    }

    /**
     * @param string $period
     * @return Collection
     */
    public function getTopTenUsersInLeaderboard(string $period): Collection
    {
        return $this->scoreTransactionRepository->getTopTenUsersInLeaderboard($period);
    }
}
