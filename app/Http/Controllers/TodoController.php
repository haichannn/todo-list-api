<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTodoRequest;
use App\Http\Resources\TodoResource;
use Illuminate\Http\JsonResponse;

class TodoController extends Controller
{
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
}
