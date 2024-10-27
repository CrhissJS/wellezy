<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class FlightController extends Controller
{
    protected $baseUrl;

    public function __construct() {
        $this->baseUrl = env('API_BASE_URL');
        $this->parameterValidator = app('parameterValidator');
    }

    /**
     * @OA\Post(
     *     path="/api/airports",
     *     summary="Get airport suggestions based on IATA code",
     *     tags={"Flights"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code"},
     *             @OA\Property(property="code", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Airport suggestions",
     *         @OA\JsonContent(
     *             @OA\Property(property="airports", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="cities", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(response=400, description="Missing parameters"),
     *     @OA\Response(response=500, description="Error fetching airports")
     * )
     */
    public function airports(Request $request): JsonResponse
    {
        $requiredParams = ['code'];
        $missingParams = $this->parameterValidator->validateRequiredParameters($request, $requiredParams);

        if (!empty($missingParams)) {
            return response()->json([
                'message' => 'Missing parameters: ' . implode(', ', $missingParams),
            ], 400);
        }

        try {
            $response = Http::post("{$this->baseUrl}/airports/v2", [
                'code' => $request->code
            ]);

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching airports'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/flights",
     *     summary="Search for flights",
     *     tags={"Flights"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"direct", "currency", "searchs", "class", "qtyPassengers", "adult", "child", "baby"},
     *             @OA\Property(property="direct", type="boolean"),
     *             @OA\Property(property="currency", type="string"),
     *             @OA\Property(property="searchs", type="integer"),
     *             @OA\Property(property="class", type="boolean"),
     *             @OA\Property(property="qtyPassengers", type="integer"),
     *             @OA\Property(property="adult", type="integer"),
     *             @OA\Property(property="child", type="integer"),
     *             @OA\Property(property="baby", type="integer"),
     *             @OA\Property(property="seat", type="integer"),
     *             @OA\Property(property="itinerary", type="array", @OA\Items(type="object",
     *                 @OA\Property(property="departureCity", type="string"),
     *                 @OA\Property(property="arrivalCity", type="string"),
     *                 @OA\Property(property="hour", type="string", format="date-time")
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Flight search results",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Missing parameters"),
     *     @OA\Response(response=500, description="Error fetching flights")
     * )
     */
    public function flights(Request $request): JsonResponse
    {
        $requiredParams = ['direct', 'currency', 'searchs', 'class', 'qtyPassengers', 'adult', 'child', 'baby', 'seat'];
        $missingParams = $this->parameterValidator->validateRequiredParameters($request, $requiredParams);

        if (!empty($missingParams)) {
            return response()->json([
                'message' => 'Missing parameters: ' . implode(', ', $missingParams),
            ], 400);
        }

        try {
            $response = Http::post("{$this->baseUrl}/flights/v2", [
                'direct' => $request->direct,
                'currency' => $request->currency,
                'searchs' => $request->searchs,
                'class' => $request->class,
                'qtyPassengers' => $request->qtyPassengers,
                'adult' => $request->adult,
                'child' => $request->child,
                'baby' => $request->baby,
                'itinerary' => $request->itinerary
            ]);
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching flights'], 500);
        }
    }
}