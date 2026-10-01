<?php

namespace App\Http\Repositories\Room;

use App\Http\Repositories\BaseRepository;
use App\Models\Room;

class RoomRepository extends BaseRepository
{
    protected $model;

    protected $model_name = Room::class;

    public function __construct()
    {
        parent::__construct();
    }

    public function getAvailableRooms(int $roomTypeId, $checkIn, $checkOut, int $requiredRooms = 0)
    {
        $query = $this->model->where('room_type_id', $roomTypeId)
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

    public function countAvailableRooms(int $roomTypeId, $checkIn, $checkOut): int
    {
        return $this->model->where('room_type_id', $roomTypeId)
            ->where('status', 'available')
            ->whereDoesntHave('bookings', function ($q) use ($checkIn, $checkOut) {
                $q->where('status', '!=', 'cancelled')
                    ->where('check_in', '<', $checkOut)
                    ->where('check_out', '>', $checkIn);
            })->count();
    }
}
