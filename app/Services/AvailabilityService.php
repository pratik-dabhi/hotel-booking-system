<?php

namespace App\Services;

use App\Http\Repositories\Hotel\HotelRepository;
use App\Http\Repositories\Room\RoomRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class AvailabilityService
{
    public function __construct(
        protected HotelRepository $hotelRepository,
        protected RoomRepository $roomRepository
    ) {}

    /**
     * Get available hotels with their room types based on location and dates.
     *
     * @return Collection
     */
    public function getAvailableHotels(array $filters)
    {
        $hotels = $this->hotelRepository->getAvailableHotels($filters);

        $checkIn = ! empty($filters['check_in']) ? Carbon::parse($filters['check_in']) : null;
        $checkOut = ! empty($filters['check_out']) ? Carbon::parse($filters['check_out']) : null;

        if ($checkIn && $checkOut) {
            foreach ($hotels as $key => $hotel) {
                $availableRoomTypes = collect();

                foreach ($hotel->roomTypes as $roomType) {
                    $availableCount = $this->countAvailableRooms($roomType->id, $checkIn, $checkOut);

                    if ($availableCount > 0) {
                        $roomType->available_rooms_count = $availableCount;
                        $availableRoomTypes->push($roomType);
                    }
                }

                if ($availableRoomTypes->isEmpty()) {
                    $hotels->forget($key);
                } else {
                    $hotel->setRelation('roomTypes', $availableRoomTypes);
                }
            }

            // Re-index the collection
            $hotels = $hotels->values();
        }

        return $hotels;
    }

    /**
     * Get specific available rooms for a room type.
     *
     * @return Collection
     */
    public function getAvailableRooms(int $roomTypeId, string|Carbon $checkIn, string|Carbon $checkOut, int $requiredRooms = 0)
    {
        $checkIn = Carbon::parse($checkIn);
        $checkOut = Carbon::parse($checkOut);

        return $this->roomRepository->getAvailableRooms($roomTypeId, $checkIn, $checkOut, $requiredRooms);
    }

    /**
     * Count available rooms for a room type.
     */
    public function countAvailableRooms(int $roomTypeId, string|Carbon $checkIn, string|Carbon $checkOut): int
    {
        $checkIn = Carbon::parse($checkIn);
        $checkOut = Carbon::parse($checkOut);

        return $this->roomRepository->countAvailableRooms($roomTypeId, $checkIn, $checkOut);
    }
}
