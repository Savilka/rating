<?php

namespace App\Http\Controllers;

use App\Services;
use DB;

class ScoreTransactionController extends Controller
{

    public function __construct(
        protected Services\LeaderboardService $leaderboardService
    ) {
    }

    public function index()
    {
        return view('transactions', [
            'transactions' => DB::table('score_transactions')->orderBy('created_at', 'desc')->paginate(10)
        ]);
    }
}
