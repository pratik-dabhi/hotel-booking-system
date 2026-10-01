<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BookingController extends Controller
{
    public function __construct(protected BookingService $bookingService) {}

    public function index(Request $request)
    {
        $bookings = $this->bookingService->getUserBookings($request->user()->id);

        return response()->json([
            'success' => true,
            'code' => Response::HTTP_OK,
            'message' => 'My bookings retrieved successfully.',
            'data' => $bookings,
        ], Response::HTTP_OK);
    }

    public function store(StoreBookingRequest $request)
    {
        try {
            $booking = $this->bookingService->createBooking(
                $request->user()->id,
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'code' => Response::HTTP_CREATED,
                'message' => 'Booking created successfully.',
                'data' => $booking,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function cancel(Request $request, int $id)
    {
        try {
            $booking = $this->bookingService->cancelBooking($request->user()->id, $id);

            return response()->json([
                'success' => true,
                'code' => Response::HTTP_OK,
                'message' => 'Booking cancelled successfully.',
                'data' => $booking,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
