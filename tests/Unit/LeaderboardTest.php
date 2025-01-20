<?php

use App\Models\User;
use App\Repositories;
use App\Services;

beforeEach(function () {
    $this->userService = new Services\UserService(
        new Repositories\UserRepository(),
        new Services\ScoreTransactionService(new Repositories\ScoreTransactionRepository())
    );

    $this->leaderboardService = new Services\LeaderboardService(
        new Repositories\UserRepository(),
        new Repositories\ScoreTransactionRepository()
    );
});

test('Top ten test', function () {
    $scores = [300, 200, 100];

    $userOne   = $this->userService->createNewUserFromData(['name' => 'test1']);
    $userOne   = $this->userService->addPointsToUser($userOne, $scores[2]);
    $userTwo   = $this->userService->createNewUserFromData(['name' => 'test2']);
    $userTwo   = $this->userService->addPointsToUser($userTwo, $scores[1]);
    $userThree = $this->userService->createNewUserFromData(['name' => 'test3']);
    $userThree = $this->userService->addPointsToUser($userThree, $scores[0]);

    expect($userOne)->toBeInstanceOf(User::class)
        ->and($userOne->name)->toBe('test1')
        ->and($userOne->score)->toBe($scores[2])
        ->and($userTwo)->toBeInstanceOf(User::class)
        ->and($userTwo->name)->toBe('test2')
        ->and($userTwo->score)->toBe($scores[1])
        ->and($userThree)->toBeInstanceOf(User::class)
        ->and($userThree->name)->toBe('test3')
        ->and($userThree->score)->toBe($scores[0]);

    $topTenDataDay   = $this->leaderboardService->getTopTenUsersInLeaderboard('day');
    $topTenDataWeek  = $this->leaderboardService->getTopTenUsersInLeaderboard('week');
    $topTenDataMonth = $this->leaderboardService->getTopTenUsersInLeaderboard('month');

    for ($i = 0; $i < 2; $i++) {
        expect($topTenDataDay[$i]->total)->toEqual($scores[$i])
            ->and($topTenDataWeek[$i]->total)->toEqual($scores[$i])
            ->and($topTenDataMonth[$i]->total)->toEqual($scores[$i]);
    }
});

test('User rank test', function () {
    $scores = [300, 200, 100];

    $userOne   = $this->userService->createNewUserFromData(['name' => 'test1']);
    $userOne   = $this->userService->addPointsToUser($userOne, $scores[2]);
    $userTwo   = $this->userService->createNewUserFromData(['name' => 'test2']);
    $userTwo   = $this->userService->addPointsToUser($userTwo, $scores[1]);
    $userThree = $this->userService->createNewUserFromData(['name' => 'test3']);
    $userThree = $this->userService->addPointsToUser($userThree, $scores[0]);

    expect($userOne)->toBeInstanceOf(User::class)
        ->and($userOne->name)->toBe('test1')
        ->and($userOne->score)->toBe($scores[2])
        ->and($userTwo)->toBeInstanceOf(User::class)
        ->and($userTwo->name)->toBe('test2')
        ->and($userTwo->score)->toBe($scores[1])
        ->and($userThree)->toBeInstanceOf(User::class)
        ->and($userThree->name)->toBe('test3')
        ->and($userThree->score)->toBe($scores[0]);

    [$rank, $score] = $this->leaderboardService->getUserRankAndScoreByPeriod($userTwo, 'day');
    expect($rank)->toEqual(2)->and($score)->toEqual($scores[1]);

    [$rank, $score] = $this->leaderboardService->getUserRankAndScoreByPeriod($userTwo, 'week');
    expect($rank)->toEqual(2)->and($score)->toEqual($scores[1]);

    [$rank, $score] = $this->leaderboardService->getUserRankAndScoreByPeriod($userTwo, 'month');
    expect($rank)->toEqual(2)->and($score)->toEqual($scores[1]);
});

test('User rank test with bad user data', function () {
    $user = new User();

    [$rank, $score] = $this->leaderboardService->getUserRankAndScoreByPeriod($user, 'day');

    expect($rank)->toEqual(0)->and($score)->toEqual(0);
});
