<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['hotel_id', 'name', 'description', 'price', 'capacity', 'total_rooms'])]
class RoomType extends Model
{
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'room_type_id', 'id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'room_type_id', 'id');
    }
}
