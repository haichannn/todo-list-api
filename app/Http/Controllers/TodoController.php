<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Http\Resources\TodoCollection;
use App\Http\Resources\TodoResource;
use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Display a listing of the authenticated user's todos.
     */
    public function index(Request $request): TodoCollection
    {
        $perPage = min($request->integer('per_page', 10), 100);

        $todos = $request->user()->todos()
            ->when($request->string('search')->value(), fn ($query, $search) => $query->where(
                fn ($q) => $q
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%"),
            ))
            ->latest()
            ->paginate($perPage);

        return new TodoCollection($todos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTodoRequest $request): JsonResponse
    {
        $todo = $request->user()->todos()->create($request->validated());

        return (new TodoResource($todo))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTodoRequest $request, Todo $todo): TodoResource
    {
        $todo->update($request->validated());

        return new TodoResource($todo);
    }
}
