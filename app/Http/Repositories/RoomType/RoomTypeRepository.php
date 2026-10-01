<?php

namespace App\Http\Repositories\RoomType;

use App\Http\Repositories\BaseRepository;
use App\Models\RoomType;

class RoomTypeRepository extends BaseRepository
{
    protected $model;

    protected $model_name = RoomType::class;

    public function __construct()
    {
        parent::__construct();
    }
}
