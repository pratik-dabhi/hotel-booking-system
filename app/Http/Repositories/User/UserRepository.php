<?php

namespace App\Http\Repositories\User;

use App\Http\Repositories\BaseRepository;
use App\Models\User;

class UserRepository extends BaseRepository
{
    protected $model;

    protected $model_name = User::class;

    public function __construct()
    {
        parent::__construct();
    }
}
