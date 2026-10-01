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
}
