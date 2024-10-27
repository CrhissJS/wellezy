<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reserve;
use App\Models\Itinerary;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReserveController extends Controller
{
    public function __construct() {
        $this->parameterValidator = app('parameterValidator');
    }

    /**
     * @OA\Post(
     *     path="/api/reserves",
     *     summary="Create a new reservation",
     *     tags={"Reservations"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "passenger_count", "adult_count", "child_count", "baby_count", "total_amount", "currency"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="passenger_count", type="integer"),
     *             @OA\Property(property="adult_count", type="integer"),
     *             @OA\Property(property="child_count", type="integer"),
     *             @OA\Property(property="baby_count", type="integer"),
     *             @OA\Property(property="total_amount", type="number", format="float"),
     *             @OA\Property(property="currency", type="string"),
     *             @OA\Property(property="itineraries", type="array", @OA\Items(type="object",
     *                 @OA\Property(property="departure_city", type="string"),
     *                 @OA\Property(property="arrival_city", type="string"),
     *                 @OA\Property(property="departure_date", type="string", format="date"),
     *                 @OA\Property(property="arrival_date", type="string", format="date"),
     *                 @OA\Property(property="departure_time", type="string"),
     *                 @OA\Property(property="arrival_time", type="string"),
     *                 @OA\Property(property="flight_number", type="string"),
     *                 @OA\Property(property="marketing_carrier", type="string")
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Reservation created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Missing parameters"),
     *     @OA\Response(response=401, description="Authentication needed"),
     *     @OA\Response(response=500, description="Error creating reserve")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $requiredParams = ['name', 'email', 'passenger_count', 'adult_count', 'child_count', 'baby_count', 'total_amount', 'currency'];
        $missingParams = $this->parameterValidator->validateRequiredParameters($request, $requiredParams);

        if (!empty($missingParams)) {
            return response()->json([
                'message' => 'Missing parameters: ' . implode(', ', $missingParams),
            ], 400);
        }

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'passenger_count' => 'required|integer',
            'adult_count' => 'required|integer',
            'child_count' => 'required|integer',
            'baby_count' => 'required|integer',
            'total_amount' => 'required|numeric',
            'currency' => 'required|string',
            'itineraries' => 'required|array',
            'itineraries.*.departure_city' => 'required|string',
            'itineraries.*.arrival_city' => 'required|string',
            'itineraries.*.departure_date' => 'required|date',
            'itineraries.*.arrival_date' => 'required|date',
            'itineraries.*.departure_time' => 'required',
            'itineraries.*.arrival_time' => 'required',
            'itineraries.*.flight_number' => 'required|string',
            'itineraries.*.marketing_carrier' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $reserve = Reserve::create([
                'name' => $request->name,
                'email' => $request->email,
                'passenger_count' => $request->passenger_count,
                'adult_count' => $request->adult_count,
                'child_count' => $request->child_count,
                'baby_count' => $request->baby_count,
                'total_amount' => $request->total_amount,
                'currency' => $request->currency
            ]);

            foreach ($request->itineraries as $itineraryData) {
                $reserve->itineraries()->create($itineraryData);
            }

            DB::commit();
            return response()->json(['message' => 'Reserve created successfully', 'data' => $reserve->load('itineraries')], 201);
        } catch (AuthenticationException $e) {
            return response()->json(['message' => 'Authentication is needed to reserve flights'], 401);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error creating reserve'], 500);
        }
    }
}