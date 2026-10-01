<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AvailabilityController extends Controller
{
    public function __construct(protected AvailabilityService $availabilityService) {}

    public function index(Request $request)
    {
        $request->validate([
            'location' => 'nullable|string',
            'name' => 'nullable|string',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|gte:min_price',
            'min_rating' => 'nullable|integer|min:1|max:5',
            'check_in' => 'nullable|date',
            'check_out' => 'nullable|date|after:check_in',
        ]);

        $filters = $request->only(['location', 'name', 'min_price', 'max_price', 'min_rating', 'check_in', 'check_out']);

        $hotels = $this->availabilityService->getAvailableHotels($filters);

        return response()->json([
            'success' => true,
            'code' => Response::HTTP_OK,
            'message' => 'Available hotels retrieved successfully.',
            'data' => $hotels,
        ], Response::HTTP_OK);
    }
}
