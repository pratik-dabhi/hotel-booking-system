<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UserLoginRequest;
use App\Http\Requests\Auth\UserRegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'email_verified_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'code' => 201,
            'message' => 'User has been registered successfully.',
            'data' => $user,
        ], Response::HTTP_CREATED);
    }

    public function login(UserLoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            $user = Auth::user();
            $user['token'] = $user->createToken('AuthToken')->accessToken;

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'User has been logged successfully.',
                'data' => $user,
            ], Response::HTTP_OK);

        } else {
            return response()->json([
                'success' => true,
                'code' => 401,
                'message' => 'Unauthorized.',
                'errors' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function me(): JsonResponse
    {

        $user = auth()->user();

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Authenticated use info.',
            'data' => $user,
        ], Response::HTTP_OK);
    }

    public function logout(): JsonResponse
    {
        Auth::user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'code' => 204,
            'message' => 'Logged out successfully.',
        ], Response::HTTP_NO_CONTENT);
    }
}
