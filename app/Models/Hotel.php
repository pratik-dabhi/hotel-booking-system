<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'location', 'description', 'rating', 'facilities'])]
class Hotel extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'facilities' => 'array',
        ];
    }

    public function roomTypes()
    {
        return $this->hasMany(RoomType::class, 'hotel_id', 'id');
    }
}
