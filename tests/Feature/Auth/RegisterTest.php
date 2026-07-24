<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can register with valid credentials', function () {
    $payload = [
        'name' => 'John Doe',
        'email' => 'john@doe.com',
        'password' => 'password',
    ];

    $response = $this->postJson('/api/register', $payload);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ],
        ])
        ->assertJsonPath('data.name', 'John Doe')
        ->assertJsonPath('data.email', 'john@doe.com');

    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'john@doe.com',
    ]);
});

test('registration fails with validation errors', function () {
    $response = $this->postJson('/api/register', [
        'name' => '',
        'email' => 'invalid-email',
        'password' => 'short',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

test('registration fails with duplicate email', function () {
    User::factory()->create(['email' => 'john@doe.com']);

    $response = $this->postJson('/api/register', [
        'name' => 'John Doe',
        'email' => 'john@doe.com',
        'password' => 'password',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});
