<?php

namespace App\Http\Requests\Hotel;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreHotelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'facilities' => 'required|json',
            'room_types' => 'required|array|min:1',
            'room_types.*.name' => 'required|string|max:255',
            'room_types.*.description' => 'required|string',
            'room_types.*.price' => 'required|integer|min:0',
            'room_types.*.capacity' => 'required|integer|min:1',
            'room_types.*.total_rooms' => 'required|integer|min:1',
        ];
    }
}
