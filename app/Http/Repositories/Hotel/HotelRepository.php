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

    public function getAvailableHotels(array $filters)
    {
        $query = $this->model->with(['roomTypes' => function ($q) use ($filters) {
            if (isset($filters['min_price'])) {
                $q->where('price', '>=', $filters['min_price']);
            }
            if (isset($filters['max_price'])) {
                $q->where('price', '<=', $filters['max_price']);
            }
        }]);

        if (! empty($filters['location'])) {
            $query->where('location', 'like', '%'.$filters['location'].'%');
        }

        if (! empty($filters['name'])) {
            $query->where('name', 'like', '%'.$filters['name'].'%');
        }

        if (! empty($filters['min_rating'])) {
            $query->where('rating', '>=', $filters['min_rating']);
        }

        if (isset($filters['min_price']) || isset($filters['max_price'])) {
            $query->whereHas('roomTypes', function ($q) use ($filters) {
                if (isset($filters['min_price'])) {
                    $q->where('price', '>=', $filters['min_price']);
                }
                if (isset($filters['max_price'])) {
                    $q->where('price', '<=', $filters['max_price']);
                }
            });
        }

        return $query->get();
    }
}
