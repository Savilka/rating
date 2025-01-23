<?php

namespace App\Services;

use App\Enums\Periods;
use App\Models\ScoreTransaction;
use App\Models\User;
use App\Repositories;
use Illuminate\Support\Carbon;
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
        $startDay = match ($period) {
            Periods::DAY->value   => Carbon::now()->day,
            Periods::WEEK->value  => Carbon::now()->startOfWeek()->day,
            Periods::MONTH->value => Carbon::now()->startOfMonth()->day,
        };

        $today = Carbon::now()->day;

        $allUserData = $this->userRepository->getAllUsersScoreData();

        $userScoresById = [];
        foreach ($allUserData as $userData) {
            $userDataArr       = json_decode($userData->scores_data, true);
            $userScoreByPeriod = 0;
            for ($i = $startDay; $i <= $today; $i++) {
                $userScoreByPeriod += $userDataArr[$i];
            }
            $userScoresById[$userData->user_id] = $userScoreByPeriod;
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
     * @return array
     */
    public function getTopTenUsersInLeaderboard(string $period): array
    {
        $startDay = match ($period) {
            Periods::DAY->value   => Carbon::now()->day,
            Periods::WEEK->value  => Carbon::now()->startOfWeek()->day,
            Periods::MONTH->value => Carbon::now()->startOfMonth()->day,
        };

        $today = Carbon::now()->day;

        $allUserData = $this->userRepository->getAllUsersScoreData();

        $userScoresById = [];
        foreach ($allUserData as $userData) {
            $userDataArr       = json_decode($userData->scores_data, true);
            $userScoreByPeriod = 0;
            for ($i = $startDay; $i <= $today; $i++) {
                $userScoreByPeriod += $userDataArr[$i];
            }
            $userScoresById[$userData->user_id] = $userScoreByPeriod;
        }

        uasort($userScoresById, function ($a, $b) {
            if ($a == $b) {
                return 0;
            }
            return ($a > $b) ? -1 : 1;
        });

        $topTenUsersWithoutNames = array_slice($userScoresById, 0, 10, true);
        $topTenUsers             = $this->userRepository->getUsersByIds(array_keys($topTenUsersWithoutNames));

        $result = [];
        foreach ($topTenUsersWithoutNames as $id => $score) {
            $result[] = [
                'id'    => $id,
                'score' => $score,
                'name'  => $topTenUsers->find($id)->name,
            ];
        }
        return $result;
    }
}
