<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    protected $parameterValidator;

    public function __construct()
    {
        $this->parameterValidator = app('parameterValidator');
    }

    public function register(Request $request): JsonResponse
    {
        $requiredParams = ['name', 'email', 'password'];
        $missingParams = $this->parameterValidator->validateRequiredParameters($request, $requiredParams);

        if (!empty($missingParams)) {
            return response()->json([
                'message' => 'Missing parameters: ' . implode(', ', $missingParams),
            ], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if (!$user) {
            return response()->json(['message' => 'Failed to register user'], 500);
        }

        return response()->json(['message' => 'User registered successfully'], 200);
    }

    public function login(Request $request): JsonResponse
    {
        $requiredParams = ['email', 'password'];
        $missingParams = $this->parameterValidator->validateRequiredParameters($request, $requiredParams);

        if (!empty($missingParams)) {
            return response()->json([
                'message' => 'Missing parameters: ' . implode(', ', $missingParams),
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'The provided credentials are incorrect.'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
          'access_token' => $token,
          'token_type' => 'Bearer',
          'user_data' => [
              'name' => $user->name,
              'email' => $user->email,
          ],
      ], 200);
    }
}
