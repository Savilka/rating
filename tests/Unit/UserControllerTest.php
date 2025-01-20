<?php

use App\Models\User;

test('Create User', function () {
    $data = [
        'name' => 'JohnDoe'
    ];

    $response = $this->postJson('/api/users', $data);

    $response->assertStatus(201)
        ->assertJson([
            'name' => 'JohnDoe',
        ]);

    $this->assertDatabaseHas('users', [
        'name' => 'JohnDoe',
    ]);
});

test('Invalid name', function () {
    $data = [
        'name' => 'Jo'
    ];

    $response = $this->postJson('/api/users', $data);

    $response->assertStatus(400)
        ->assertJson([
            'errors' => [
                'name' => ['The name field must be at least 3 characters.'],
            ],
        ]);

    $data = [
        'name' => 'Jo!!!@'
    ];

    $response = $this->postJson('/api/users', $data);

    $response->assertStatus(400)
        ->assertJson([
            'errors' => [
                'name' => ['The name field format is invalid.'],
            ],
        ]);
});

test('Name is already taken', function () {
    User::factory()->create(['name' => 'JohnDoe']);

    $data = [
        'name' => 'JohnDoe'
    ];

    $response = $this->postJson('/api/users', $data);

    $response->assertStatus(409)
        ->assertJson([
            'errors' => [
                'name' => ['The name has already been taken.'],
            ],
        ]);
});

test('Add score', function () {
    $user = User::factory()->create();

    $data = ['points' => 500];
    $response = $this->postJson("/api/users/$user->id/score", $data);

    $response->assertStatus(200)
        ->assertJson([
            'id'    => $user->id,
            'score' => 500,
        ]);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'score' => 500,
    ]);
});

test('Points are invalid', function () {
    $user = User::factory()->create();

    $data = [];
    $response = $this->postJson("/api/users/$user->id/score", $data);
    $response->assertStatus(400)
        ->assertJson([
            'errors' => [
                'points' => ['The points field is required.'],
            ],
        ]);

    $data = ['points' => 'test'];
    $response = $this->postJson("/api/users/{$user->id}/score", $data);
    $response->assertStatus(400)
        ->assertJson([
            'errors' => [
                'points' => ['The points field must be an integer.'],
            ],
        ]);

    $data = ['points' => 0];
    $response = $this->postJson("/api/users/{$user->id}/score", $data);
    $response->assertStatus(400)
        ->assertJson([
            'errors' => [
                'points' => ['The points field must be at least 1.'],
            ],
        ]);

    $data = ['points' => 10001];
    $response = $this->postJson("/api/users/{$user->id}/score", $data);

    $response->assertStatus(400)
        ->assertJson([
            'errors' => [
                'points' => ['The points field must not be greater than 10000.'],
            ],
        ]);
});
