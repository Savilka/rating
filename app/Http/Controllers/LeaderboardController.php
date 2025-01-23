<?php

namespace App\Http\Controllers;

use App\Enums\Periods;
use App\Models\User;
use App\Services;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class LeaderboardController extends Controller
{
    public function __construct(
        protected Services\LeaderboardService $leaderboardService
    ) {
    }

    /**
     * @param User $user
     * @param Request $request
     * @return JsonResponse
     */
    public function rank(User $user, Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            ['period' => ['required', Rule::in(Periods::DAY->value, Periods::WEEK->value, Periods::MONTH->value)]]
        );
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->messages(),
            ], 400);
        }

        $period = $request->get('period');
        if ($request->get('new') == '1') {
            [$rank, $score] = $this->leaderboardService->getUserRankAndScoreByPeriodNew($user, $period);
        } else {
            [$rank, $score] = $this->leaderboardService->getUserRankAndScoreByPeriod($user, $period);
        }

        return response()->json([
            'id'     => $user->id,
            'period' => $period,
            'score'  => $score,
            'rank'   => $rank,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function top(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            ['period' => ['required', Rule::in(Periods::DAY->value, Periods::WEEK->value, Periods::MONTH->value)]]
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
                'id'    => $user['id'],
                'score' => $user['score'],
                'name'  => $user['name'],
            ];
        }

        return response()->json([
            'period' => $period,
            'scores' => [$data]
        ]);
    }
}
