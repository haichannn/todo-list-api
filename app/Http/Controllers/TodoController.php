<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Http\Resources\TodoCollection;
use App\Http\Resources\TodoResource;
use App\Models\Todo;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TodoController extends Controller
{
    use AuthorizesRequests;

    #[Endpoint(title: 'List Todos', description: 'Retrieve a paginated list of the authenticated user\'s todos.')]
    #[QueryParameter('search', description: 'Search by title or description.', type: 'string')]
    #[QueryParameter('per_page', description: 'Number of items per page (max 100).', type: 'int', default: 10)]
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

    #[Endpoint(title: 'Create Todo', description: 'Store a new todo for the authenticated user.')]
    public function store(StoreTodoRequest $request): JsonResponse
    {
        $todo = $request->user()->todos()->create($request->validated());

        return (new TodoResource($todo))
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint(title: 'Update Todo', description: 'Update an existing todo.')]
    public function update(UpdateTodoRequest $request, Todo $todo): TodoResource
    {
        $todo->update($request->validated());

        return new TodoResource($todo);
    }

    #[Endpoint(title: 'Delete Todo', description: 'Delete a todo.')]
    public function destroy(Todo $todo): Response
    {
        $this->authorize('delete', $todo);

        $todo->delete();

        return response()->noContent();
    }
}
