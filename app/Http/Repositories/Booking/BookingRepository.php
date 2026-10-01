<?php

namespace App\Http\Repositories\Booking;

use App\Http\Repositories\BaseRepository;
use App\Models\Booking;

class BookingRepository extends BaseRepository
{
    protected $model;

    protected $model_name = Booking::class;

    public function __construct()
    {
        parent::__construct();
    }
}
