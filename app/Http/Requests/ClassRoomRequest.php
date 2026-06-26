<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClassRoomRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Pastikan ini true
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'level' => 'nullable|string|max:50'
        ];
    }
}
