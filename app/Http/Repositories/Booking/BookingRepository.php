<?php

namespace App\Http\Repositories\Booking;

use App\Http\Repositories\BaseRepository;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class BookingRepository extends BaseRepository
{
    protected $model;

    protected $model_name = Booking::class;

    public function __construct()
    {
        parent::__construct();
    }

    public function getUserBookings(int $userId)
    {
        return $this->model->where('user_id', $userId)
            ->with(['roomType.hotel', 'rooms'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function cancelBooking(int $userId, int $bookingId)
    {
        return DB::transaction(function () use ($userId, $bookingId) {
            $booking = $this->model->where('user_id', $userId)->lockForUpdate()->findOrFail($bookingId);

            if ($booking->status === 'cancelled') {
                throw new \Exception('Booking is already cancelled.');
            }

            $booking->update(['status' => 'cancelled']);

            return $booking;
        });
    }
}
