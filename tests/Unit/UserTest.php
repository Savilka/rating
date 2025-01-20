<?php

use App\Models\User;
use App\Repositories;
use App\Services;

beforeEach(function () {
    $this->userService = new Services\UserService(
        new Repositories\UserRepository(),
        new Services\ScoreTransactionService(new Repositories\ScoreTransactionRepository())
    );
});

test('Create User', function () {
    $user = $this->userService->createNewUserFromData(['name' => 'test']);

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('test');
});

test('Add User score', function () {
    $user = $this->userService->createNewUserFromData(['name' => 'test']);
    $user = $this->userService->addPointsToUser($user, 100);

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('test')
        ->and($user->score)->toBe(100);
});
