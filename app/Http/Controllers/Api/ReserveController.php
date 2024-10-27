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