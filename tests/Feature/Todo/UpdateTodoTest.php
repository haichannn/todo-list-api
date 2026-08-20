<?php

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class)->group('todo', 'api');

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->todo = Todo::factory()->for($this->user)->create();
});

test('an owner can update their todo', function () {
    $payload = [
        'title' => 'Updated Title',
        'description' => 'Updated Description',
    ];

    $response = $this->actingAs($this->user)->patchJson("/api/todos/{$this->todo->id}", $payload);

    $response->assertOk()
        ->assertJsonPath('data.title', 'Updated Title')
        ->assertJsonPath('data.description', 'Updated Description');
});

test('a non-owner cannot update a todo', function () {
    $anotherUser = User::factory()->create();
    $payload = ['title' => 'Malicious Update'];

    $response = $this->actingAs($anotherUser)->patchJson("/api/todos/{$this->todo->id}", $payload);

    $response->assertForbidden()
        ->assertJson(fn ($json) => $json->has('message'));
});

test('unauthenticated user cannot update a todo', function () {
    $response = $this->patchJson("/api/todos/{$this->todo->id}", ['title' => 'Guest Update']);

    $response->assertUnauthorized();
});

test('update fails if todo does not exist', function () {
    $response = $this->actingAs($this->user)->patchJson('/api/todos/999', ['title' => 'Wont work']);

    $response->assertNotFound();
});

test('update fails on validation error', function (array $payload, array $errors) {
    $response = $this->actingAs($this->user)->patchJson("/api/todos/{$this->todo->id}", $payload);

    $response->assertStatus(422)->assertJsonValidationErrors($errors);
})->with([
    'title missing' => fn () => [['title' => ''], ['title']],
    'title too long' => fn () => [['title' => str_repeat('a', 256)], ['title']],
    'description too long' => fn () => [['title' => 'Valid Title', 'description' => str_repeat('a', 2001)], ['description']],
]);

test('todo description can be set to null', function () {
    $payload = ['description' => null];
    $response = $this->actingAs($this->user)->patchJson("/api/todos/{$this->todo->id}", $payload);

    $response->assertOk()->assertJsonPath('data.description', null);
    $this->assertNull($this->todo->fresh()->description);
});
