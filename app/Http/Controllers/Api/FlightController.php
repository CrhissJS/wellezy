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