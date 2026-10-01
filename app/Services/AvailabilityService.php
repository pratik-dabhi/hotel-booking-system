<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class AvailabilityService
{
    /**
     * Get available hotels with their room types based on location and dates.
     *
     * @return Collection
     */
    public function getAvailableHotels(array $filters)
    {
        $query = Hotel::query()->with(['roomTypes' => function ($q) use ($filters) {
            if (isset($filters['min_price'])) {
                $q->where('price', '>=', $filters['min_price']);
            }
            if (isset($filters['max_price'])) {
                $q->where('price', '<=', $filters['max_price']);
            }
        }]);

        if (! empty($filters['location'])) {
            $query->where('location', 'like', '%'.$filters['location'].'%');
        }

        if (! empty($filters['name'])) {
            $query->where('name', 'like', '%'.$filters['name'].'%');
        }

        if (! empty($filters['min_rating'])) {
            $query->where('rating', '>=', $filters['min_rating']);
        }

        // Ensure hotel actually has room types that match the price range
        if (isset($filters['min_price']) || isset($filters['max_price'])) {
            $query->whereHas('roomTypes', function ($q) use ($filters) {
                if (isset($filters['min_price'])) {
                    $q->where('price', '>=', $filters['min_price']);
                }
                if (isset($filters['max_price'])) {
                    $q->where('price', '<=', $filters['max_price']);
                }
            });
        }

        $hotels = $query->get();

        $checkIn = ! empty($filters['check_in']) ? Carbon::parse($filters['check_in']) : null;
        $checkOut = ! empty($filters['check_out']) ? Carbon::parse($filters['check_out']) : null;

        if ($checkIn && $checkOut) {
            foreach ($hotels as $hotel) {
                foreach ($hotel->roomTypes as $roomType) {
                    $roomType->available_rooms_count = $this->countAvailableRooms($roomType->id, $checkIn, $checkOut);
                }

                // Optionally, we could filter out hotels where all room types have 0 available rooms
                // But for now, we just attach the available_rooms_count
            }
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

        $query = Room::where('room_type_id', $roomTypeId)
            ->where('status', 'available')
            ->whereDoesntHave('bookings', function ($q) use ($checkIn, $checkOut) {
                $q->where('status', '!=', 'cancelled')
                    ->where('check_in', '<', $checkOut)
                    ->where('check_out', '>', $checkIn);
            });

        if ($requiredRooms > 0) {
            return $query->take($requiredRooms)->get();
        }

        return $query->get();
    }

    /**
     * Count available rooms for a room type.
     */
    public function countAvailableRooms(int $roomTypeId, string|Carbon $checkIn, string|Carbon $checkOut): int
    {
        $checkIn = Carbon::parse($checkIn);
        $checkOut = Carbon::parse($checkOut);

        return Room::where('room_type_id', $roomTypeId)
            ->where('status', 'available')
            ->whereDoesntHave('bookings', function ($q) use ($checkIn, $checkOut) {
                $q->where('status', '!=', 'cancelled')
                    ->where('check_in', '<', $checkOut)
                    ->where('check_out', '>', $checkIn);
            })->count();
    }
}
