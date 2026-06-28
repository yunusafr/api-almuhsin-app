<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClassEnrollmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'student_id' => 'required|uuid|exists:students,id',
            'class_id' => 'required|uuid|exists:classes,id',
            'academic_year_id' => 'required|uuid|exists:academic_years,id',
            'status' => 'nullable|string|in:aktif,mutasi,lulus,keluar'
        ];
    }
}
