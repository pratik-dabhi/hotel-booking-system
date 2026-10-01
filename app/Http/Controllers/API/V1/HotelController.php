<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Hotel\HotelRepository;
use App\Http\Requests\Hotel\StoreHotelRequest;
use App\Services\HotelService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HotelController extends Controller
{
    public function __construct(
        protected HotelRepository $hotelRepository,
        protected HotelService $hotelService
    ) {}

    public function index(Request $request)
    {
        $hotels = $this->hotelRepository->all();

        return response()->json([
            'success' => true,
            'code' => Response::HTTP_OK,
            'message' => 'Hotel has been retrieved successfully.',
            'data' => $hotels,
        ], Response::HTTP_OK);
    }

    public function store(StoreHotelRequest $request)
    {
        $data = $request->validated();
        $hotel = $this->hotelService->createHotel($data);

        return response()->json([
            'success' => true,
            'code' => Response::HTTP_OK,
            'message' => 'Hotel has been created successfully.',
            'data' => $hotel,
        ], Response::HTTP_OK);
    }

    public function getById(Request $request, int $id)
    {
        $hotel = $this->hotelRepository->getById($id);

        if (empty($hotel)) {
            return response()->json([
                'success' => false,
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => 'No data found',
                'data' => $hotel,
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'success' => true,
            'code' => Response::HTTP_OK,
            'message' => 'Hotel has been retrieved successfully.',
            'data' => $hotel,
        ], Response::HTTP_OK);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $hotel = $this->hotelRepository->update($id, $data);

        return response()->json([
            'success' => true,
            'code' => Response::HTTP_OK,
            'message' => 'Hotel has been updated successfully.',
            'data' => $hotel,
        ], Response::HTTP_OK);
    }

    public function destroy(Request $request, $id)
    {
        $hotel = $this->hotelRepository->delete($id);

        return response()->json([
            'success' => true,
            'code' => Response::HTTP_OK,
            'message' => 'Hotel has been destroyed successfully.',
            'data' => $hotel,
        ], Response::HTTP_OK);
    }
}
