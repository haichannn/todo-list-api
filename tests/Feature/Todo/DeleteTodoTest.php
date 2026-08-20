<?php

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class)->group('todo', 'api');

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->todo = Todo::factory()->for($this->user)->create();
});

test('user can delete their own todo', function () {
    $response = $this->actingAs($this->user)->deleteJson(route('todos.destroy', $this->todo));

    $response->assertNoContent();
    $this->assertModelMissing($this->todo);
});

test('user cannot delete another user\'s todo', function () {
    $anotherUser = User::factory()->create();

    $response = $this->actingAs($anotherUser)->deleteJson(route('todos.destroy', $this->todo));

    $response->assertForbidden()
        ->assertJson(fn ($json) => $json->has('message'));
    $this->assertModelExists($this->todo);
});

test('user receives 404 when deleting a non-existent todo', function () {
    $response = $this->actingAs($this->user)->deleteJson(route('todos.destroy', 999));

    $response->assertNotFound();
});

test('unauthenticated user cannot delete a todo', function () {
    $response = $this->deleteJson(route('todos.destroy', $this->todo));

    $response->assertUnauthorized();
});
