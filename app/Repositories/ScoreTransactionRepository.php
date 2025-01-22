<?php

namespace App\Repositories;

use App\Enums\Periods;
use App\Models\ScoreTransaction;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ScoreTransactionRepository
{
    const TOP_TEN = 10;

    /**
     * @param User $user
     * @param int $score
     * @return ScoreTransaction
     */
    public function addNewTransaction(User $user, int $score): ScoreTransaction
    {
        $transaction = new ScoreTransaction();

        $transaction->user_id = $user->id;
        $transaction->score   = $score;

        $transaction->save();

        return $transaction;
    }

    /**
     * @param string $period
     * @return Collection
     */
    public function getTransactionsByPeriod(string $period): Collection
    {
        $startDate = match ($period) {
            Periods::DAY->value   => Carbon::now()->startOfDay(),
            Periods::WEEK->value  => Carbon::now()->startOfWeek(),
            Periods::MONTH->value => Carbon::now()->startOfMonth(),
        };

        return ScoreTransaction::query()
            ->selectRaw('user_id, sum(score) as total')
            ->where('created_at', '>=', $startDate)
            ->groupBy('user_id')
            ->get();
    }

    /**
     * @param User $user
     * @param string $period
     * @return Collection
     */
    public function getUserRankByPeriod(User $user, string $period): Collection
    {
        $startDate = match ($period) {
            Periods::DAY->value   => Carbon::now()->startOfDay(),
            Periods::WEEK->value  => Carbon::now()->startOfWeek(),
            Periods::MONTH->value => Carbon::now()->startOfMonth(),
        };

        $subQuery = DB::table('users')
            ->leftJoin('score_transactions', function ($join) use ($startDate) {
                $join->on('users.id', '=', 'score_transactions.user_id')
                    ->where('score_transactions.created_at', '>=', $startDate);
            })
            ->select(
                'users.id as user_id',
                DB::raw('COALESCE(SUM(score_transactions.score), 0) as total'),
                DB::raw('ROW_NUMBER() OVER (ORDER BY COALESCE(SUM(score_transactions.score), 0) DESC) as position')
            )
            ->groupBy('users.id');

        return DB::table(DB::raw("({$subQuery->toSql()}) as ranked_users"))
            ->mergeBindings($subQuery)
            ->where('user_id', '=', $user->id)
            ->orderBy('position')
            ->get();
    }

    /**
     * @param string $period
     * @return Collection
     */
    public function getTopTenUsersInLeaderboard(string $period): Collection
    {
        $startDate = match ($period) {
            Periods::DAY->value   => Carbon::now()->startOfDay(),
            Periods::WEEK->value  => Carbon::now()->startOfWeek(),
            Periods::MONTH->value => Carbon::now()->startOfMonth(),
        };

        $subQuery = DB::table('score_transactions')
            ->select('user_id', DB::raw('SUM(score) as total'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('user_id')
            ->limit(self::TOP_TEN);

        return DB::table(DB::raw("({$subQuery->toSql()}) as grouped"))
            ->mergeBindings($subQuery)
            ->join('users', 'grouped.user_id', '=', 'users.id')
            ->select('grouped.user_id', 'grouped.total', 'users.name')
            ->orderBy('total', 'desc')
            ->get();
    }
}
