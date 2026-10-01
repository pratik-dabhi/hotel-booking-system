<?php

namespace App\Services;

use App\Http\Repositories\Hotel\HotelRepository;
use App\Http\Repositories\Room\RoomRepository;
use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class HotelService
{
    public function __construct(
        protected HotelRepository $hotelRepository, protected RoomRepository $roomRepository
    ) {}

    /**
     * Create a new hotel with its room types and auto-generated rooms.
     *
     * @return Hotel
     */
    public function createHotel(array $data)
    {
        return DB::transaction(function () use ($data) {
            $roomTypesData = $data['room_types'] ?? [];
            unset($data['room_types']);

            if (isset($data['facilities']) && is_string($data['facilities'])) {
                $data['facilities'] = json_decode($data['facilities'], true);
            }

            $hotel = $this->hotelRepository->create($data);

            foreach ($roomTypesData as $typeIndex => $roomTypeData) {
                $roomType = $hotel->roomTypes()->create([
                    'name' => $roomTypeData['name'],
                    'description' => $roomTypeData['description'],
                    'price' => $roomTypeData['price'],
                    'capacity' => $roomTypeData['capacity'],
                    'total_rooms' => $roomTypeData['total_rooms'],
                ]);

                $roomsToInsert = [];
                for ($i = 1; $i <= $roomTypeData['total_rooms']; $i++) {
                    $floor = $typeIndex + 1;
                    $roomNumber = sprintf('%d%02d', $floor, $i);

                    $roomsToInsert[] = [
                        'room_type_id' => $roomType->id,
                        'room_number' => $hotel->id.'-'.$roomType->id.'-'.$roomNumber,
                        'status' => 'available',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                $this->roomRepository->insert($roomsToInsert);
            }

            return $hotel->load('roomTypes.rooms');
        });
    }
}
