<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class)->group('todo', 'api');

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('authenticated user can create a todo', function () {
    $payload = [
        'title' => 'Buy groceries',
        'description' => 'Milk, eggs, bread',
    ];

    $response = $this->actingAs($this->user)->postJson('/api/todos', $payload);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'description',
                'created_at',
                'updated_at',
            ],
        ])
        ->assertJsonPath('data.title', 'Buy groceries')
        ->assertJsonPath('data.description', 'Milk, eggs, bread');

    $this->assertDatabaseHas('todos', [
        'title' => 'Buy groceries',
        'description' => 'Milk, eggs, bread',
        'user_id' => $this->user->id,
    ]);
});

test('unauthenticated user cannot create a todo', function () {
    $response = $this->postJson('/api/todos', [
        'title' => 'Buy groceries',
    ]);

    $response->assertUnauthorized();
});

test('creation fails when title is missing', function () {
    $response = $this->actingAs($this->user)->postJson('/api/todos', [
        'description' => 'Some description',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title']);
});

test('creation fails when title exceeds 255 characters', function () {
    $response = $this->actingAs($this->user)->postJson('/api/todos', [
        'title' => str_repeat('a', 256),
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title']);
});

test('creation fails when description exceeds 2000 characters', function () {
    $response = $this->actingAs($this->user)->postJson('/api/todos', [
        'title' => 'Valid title',
        'description' => str_repeat('a', 2001),
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['description']);
});

test('creation is rate limited to 60 requests per minute', function () {
    $payload = ['title' => 'Rate limit test'];

    for ($i = 0; $i < 60; $i++) {
        $this->actingAs($this->user)->postJson('/api/todos', $payload)->assertStatus(201);
    }

    $this->actingAs($this->user)->postJson('/api/todos', $payload)->assertStatus(429);
});

test('creation succeeds when description is null', function () {
    $response = $this->actingAs($this->user)->postJson('/api/todos', [
        'title' => 'No description todo',
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.title', 'No description todo')
        ->assertJsonPath('data.description', null);
});
