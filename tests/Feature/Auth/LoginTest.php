<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('user can login with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'john@doe.com',
        'password' => bcrypt('password'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'john@doe.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['token'])
        ->assertJsonPath('token', fn ($token) => str_contains($token, '|'));

    $this->assertDatabaseHas('personal_access_tokens', [
        'tokenable_id' => $user->id,
    ]);
});

test('login fails with invalid credentials', function () {
    User::factory()->create([
        'email' => 'john@doe.com',
        'password' => bcrypt('password'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'john@doe.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(401)
        ->assertJsonPath('message', 'The provided credentials are incorrect.');
});

test('login fails with validation errors', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'invalid-email',
        'password' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
});

test('authenticated user can access protected routes with token', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/user');

    $response->assertStatus(200)
        ->assertJsonPath('id', $user->id)
        ->assertJsonPath('email', $user->email);
});
