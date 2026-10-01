<?php

namespace App\Http\Repositories\Hotel;

use App\Http\Repositories\BaseRepository;
use App\Models\Hotel;

class HotelRepository extends BaseRepository
{
    protected $model;

    protected $model_name = Hotel::class;

    public function __construct()
    {
        parent::__construct();
    }
}
