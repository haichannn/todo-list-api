<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

#[Group('Auth')]
class LoginController extends Controller
{
    /**
     * @unauthenticated
     */
    #[Endpoint(title: 'Login', description: 'Authenticate a user and retrieve a bearer token.')]
    public function __invoke(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->validated())) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'token' => $token,
        ]);
    }
}
