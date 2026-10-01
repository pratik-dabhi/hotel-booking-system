<?php

namespace App\Services;

use App\Http\Repositories\Booking\BookingRepository;
use App\Http\Repositories\RoomType\RoomTypeRepository;
use App\Models\Booking;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    public function __construct(
        protected AvailabilityService $availabilityService,
        protected BookingRepository $bookingRepository,
        protected RoomTypeRepository $roomTypeRepository
    ) {}

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

            $roomType = $this->roomTypeRepository->getById($roomTypeId);
            if (! $roomType) {
                throw new Exception('Room Type not found.');
            }

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

            $booking = $this->bookingRepository->create([
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
        return $this->bookingRepository->getUserBookings($userId);
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
        return $this->bookingRepository->cancelBooking($userId, $bookingId);
    }
}
