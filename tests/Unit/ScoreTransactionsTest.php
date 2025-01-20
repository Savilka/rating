<?php

use App\Models\ScoreTransaction;
use App\Models\User;
use App\Repositories;
use App\Services;

beforeEach(function () {
    $this->userService = new Services\UserService(
        new Repositories\UserRepository(),
        new Services\ScoreTransactionService(new Repositories\ScoreTransactionRepository())
    );

    $this->scoreTransactionService = new Services\ScoreTransactionService(new Repositories\ScoreTransactionRepository());
});

test('Add new transaction', function () {
    $user = $this->userService->createNewUserFromData(['name' => 'test']);

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('test');

    $transaction = $this->scoreTransactionService->addNewTransaction($user, 100);

    expect($transaction)->toBeInstanceOf(ScoreTransaction::class)
        ->and($transaction->user_id)->toBe($user->id)
        ->and($transaction->score)->toBe(100);
});
