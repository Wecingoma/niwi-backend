<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = Auth::getProvider()->retrieveByCredentials([
            'email' => $validated['email'],
        ]);

        if (
            ! $user
            || ! Hash::check($validated['password'], $user->password)
            || $user->role === User::ROLE_ADMIN
        ) {
            return response()->json(['message' => 'Mot de passe incorrect.'], 422);
        }

        if (! $user->role) {
            $user->update([
                'role' => User::ROLE_USER,
            ]);
        }

        $token = $user->createToken('user')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json(null, 204);
    }
}
