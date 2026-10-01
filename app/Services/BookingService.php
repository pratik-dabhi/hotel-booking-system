<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\RoomType;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    public function __construct(protected AvailabilityService $availabilityService) {}

    /**
     * Create a new booking and auto-assign available rooms.
     *
     * @return Booking
     *
     * @throws Exception
     */
    public function createBooking(int $userId, array $data)
    {
        return DB::transaction(function () use ($userId, $data) {
            $roomTypeId = $data['room_type_id'];
            $checkIn = Carbon::parse($data['check_in']);
            $checkOut = Carbon::parse($data['check_out']);
            $guests = $data['guests'];
            $roomsCount = $data['rooms_count'];

            $roomType = RoomType::findOrFail($roomTypeId);
            $availableRooms = $this->availabilityService->getAvailableRooms(
                $roomTypeId,
                $checkIn,
                $checkOut,
                $roomsCount
            );

            if ($availableRooms->count() < $roomsCount) {
                throw new Exception('Not enough rooms available for the selected dates.');
            }

            $nights = $checkIn->diffInDays($checkOut);
            if ($nights <= 0) {
                throw new Exception('Check-out date must be after check-in date.');
            }

            $pricePerNight = $roomType->price;
            $totalAmount = $roomsCount * $pricePerNight * $nights;

            $booking = Booking::create([
                'booking_reference' => strtoupper(Str::random(10)),
                'user_id' => $userId,
                'room_type_id' => $roomTypeId,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'guests' => $guests,
                'price_per_night' => $pricePerNight,
                'total_amount' => $totalAmount,
                'status' => 'confirmed',
            ]);

            // Attach assigned rooms to the booking
            $booking->rooms()->attach($availableRooms->pluck('id')->toArray());

            return $booking->load('rooms', 'roomType.hotel');
        });
    }

    /**
     * Get all bookings for a specific user.
     *
     * @return Collection
     */
    public function getUserBookings(int $userId)
    {
        return Booking::where('user_id', $userId)
            ->with(['roomType.hotel', 'rooms'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Cancel a specific booking for a user.
     *
     * @return Booking
     *
     * @throws Exception
     */
    public function cancelBooking(int $userId, int $bookingId)
    {
        return DB::transaction(function () use ($userId, $bookingId) {
            $booking = Booking::where('user_id', $userId)->lockForUpdate()->findOrFail($bookingId);

            if ($booking->status === 'cancelled') {
                throw new Exception('Booking is already cancelled.');
            }

            $booking->update(['status' => 'cancelled']);

            return $booking;
        });
    }
}
