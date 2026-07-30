<?php

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class)->group('todo', 'api');

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('authenticated user can list their todos', function () {
    Todo::factory()->count(3)->create(['user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)->getJson('/api/todos');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                    'created_at',
                    'updated_at',
                ],
            ],
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'from', 'last_page', 'per_page', 'to', 'total'],
        ])
        ->assertJsonCount(3, 'data');
});

test('unauthenticated user cannot list todos', function () {
    $response = $this->getJson('/api/todos');

    $response->assertUnauthorized();
});

test('user cannot see other users todos', function () {
    $otherUser = User::factory()->create();
    Todo::factory()->create(['user_id' => $otherUser->id, 'title' => 'Other Title']);
    Todo::factory()->create(['user_id' => $this->user->id, 'title' => 'My Title']);

    $response = $this->actingAs($this->user)->getJson('/api/todos');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'My Title');
});

test('pagination works with custom per_page param', function () {
    Todo::factory()->count(15)->create(['user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)->getJson('/api/todos?per_page=5');

    $response->assertStatus(200)
        ->assertJsonCount(5, 'data')
        ->assertJsonPath('meta.per_page', 5)
        ->assertJsonPath('meta.total', 15);
});

test('default pagination is 10 per page', function () {
    Todo::factory()->count(15)->create(['user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)->getJson('/api/todos');

    $response->assertStatus(200)
        ->assertJsonCount(10, 'data')
        ->assertJsonPath('meta.per_page', 10);
});

test('search filters by title', function () {
    Todo::factory()->create(['user_id' => $this->user->id, 'title' => 'Learn Laravel']);
    Todo::factory()->create(['user_id' => $this->user->id, 'title' => 'Buy groceries']);

    $response = $this->actingAs($this->user)->getJson('/api/todos?search=Laravel');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Learn Laravel');
});

test('search filters by description', function () {
    Todo::factory()->create(['user_id' => $this->user->id, 'title' => 'Task A', 'description' => 'Clean the kitchen']);
    Todo::factory()->create(['user_id' => $this->user->id, 'title' => 'Task B', 'description' => 'Walk the dog']);

    $response = $this->actingAs($this->user)->getJson('/api/todos?search=kitchen');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Task A');
});

test('empty list returns empty data array', function () {
    $response = $this->actingAs($this->user)->getJson('/api/todos');

    $response->assertStatus(200)
        ->assertJsonCount(0, 'data');
});
