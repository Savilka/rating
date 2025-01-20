<?php

use App\Models\User;
use App\Services\LeaderboardService;


test('Valid period rank', function () {
    $user   = User::factory()->create();
    $period = 'week';

    $this->mock(LeaderboardService::class, function ($mock) use ($user) {
        $mock->shouldReceive('getUserRankAndScoreByPeriod')
            ->once()
            ->with(User::class, 'week')
            ->andReturn([3, 150]);
    });

    $response = $this->getJson("/api/leaderboard/rank/$user->id?period=$period");
    $response->assertStatus(200)
        ->assertJson([
            'id'     => $user->id,
            'period' => $period,
            'score'  => 150,
            'rank'   => 3,
        ]);
});

test('Invalid period rank', function () {
    $user   = User::factory()->create();
    $period = 'test';

    $response = $this->getJson("/api/leaderboard/rank/$user->id?period=$period",);
    $response->assertStatus(400)
        ->assertJson([
            'errors' => [
                'period' => ['The selected period is invalid.'],
            ],
        ]);


    $response = $this->getJson("/api/leaderboard/rank/$user->id",);
    $response->assertStatus(400)
        ->assertJson([
            'errors' => [
                'period' => ['The period field is required.'],
            ],
        ]);
});

test('Valid period top', function () {
    $period = 'month';

    $this->mock(LeaderboardService::class, function ($mock) use ($period) {
        $mock->shouldReceive('getTopTenUsersInLeaderboard')
            ->once()
            ->with($period)
            ->andReturn(
                collect([
                    (object)['user_id' => 1, 'total' => 200, 'name' => 'User 1'],
                    (object)['user_id' => 2, 'total' => 150, 'name' => 'User 2'],
                ])
            );
    });

    $response = $this->getJson("/api/leaderboard/top?period=$period");

    $response->assertStatus(200)
        ->assertJson([
            'period' => $period,
            'scores' => [
                [
                    1 => ['id' => 1, 'score' => 200, 'name' => 'User 1'],
                    2 => ['id' => 2, 'score' => 150, 'name' => 'User 2'],
                ]
            ]
        ]);
});

it('Invalid period top', function () {
    $period = 'test';

    $response = $this->getJson("/api/leaderboard/top?period=$period");
    $response->assertStatus(400)
        ->assertJson([
            'errors' => [
                'period' => ['The selected period is invalid.'],
            ],
        ]);


    $response = $this->getJson("/api/leaderboard/top?");
    $response->assertStatus(400)
        ->assertJson([
            'errors' => [
                'period' => ['The period field is required.'],
            ],
        ]);
});
