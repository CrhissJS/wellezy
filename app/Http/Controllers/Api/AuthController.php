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

    

    /**
     *  * @OA\Info(
     *     version="1.0.0",
     *     title="API de Reservas de Vuelos",
     *     description="API documentation for the flight reservation project."
     * )
     *
     * @OA\Server(
     *     url="http://localhost:8000/api",
     *     description="Local server"
     * )
     * 
     * @OA\Server(
     *     url="https://web-production-8f66.up.railway.app/api",
     *     description="Production server"  
     * )  
     *
     * @OA\SecurityScheme(
     *     securityScheme="Authorization",
     *     type="http",
     *     scheme="bearer",
     *     bearerFormat="JWT",
     *     description="Autenticación mediante Laravel Sanctum (Authorization Bearer token)"
     * )
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User registered successfully"
     *     ),
     *     @OA\Response(response=400, description="Missing parameters"),
     *     @OA\Response(response=500, description="Failed to register user")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="User login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string"),
     *             @OA\Property(property="user_data", type="object",
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Missing parameters"),
     *     @OA\Response(response=401, description="Incorrect credentials")
     * )
     */
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
