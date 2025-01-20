<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class LeaderboardController extends Controller
{
    const DAY_PERIOD_NAME   = 'day';
    const WEEK_PERIOD_NAME  = 'week';
    const MONTH_PERIOD_NAME = 'month';

    public function __construct(
        protected Services\LeaderboardService $leaderboardService
    ) {
    }

    public function rank(User $user, Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            ['period' => ['required', Rule::in(self::DAY_PERIOD_NAME, self::WEEK_PERIOD_NAME, self::MONTH_PERIOD_NAME)]]
        );
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->messages(),
            ], 400);
        }

        $period = $request->get('period');
        [$rank, $score] = $this->leaderboardService->getUserRankAndScoreByPeriod($user, $period);

        return response()->json([
            'id'     => $user->id,
            'period' => $period,
            'score'  => $score,
            'rank'   => $rank,
        ]);
    }

    public function top(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            ['period' => ['required', Rule::in(self::DAY_PERIOD_NAME, self::WEEK_PERIOD_NAME, self::MONTH_PERIOD_NAME)]]
        );
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->messages(),
            ], 400);
        }

        $period = $request->get('period');
        $users  = $this->leaderboardService->getTopTenUsersInLeaderboard($period);

        $data = [];
        foreach ($users as $i => $user) {
            $data[$i + 1] = [
                'id'    => $user->user_id,
                'score' => $user->total,
                'name'  => $user->name,
            ];
        }

        return response()->json([
            'period' => $period,
            'scores' => [$data]
        ]);
    }
}
