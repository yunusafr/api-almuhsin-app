<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Izinkan request diproses oleh controller
    }

    public function rules(): array
    {
        // Tangkap ID santri jika sistem sedang melakukan proses UPDATE (PUT)
        $studentId = $this->route('student') ? $this->route('student')->id : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'address' => ['nullable', 'string'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_phone' => ['nullable', 'string', 'max:30'],
            'rombel' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'string', 'max:50'],

            // NIS bersifat unik, namun abaikan data milik santri itu sendiri jika sedang proses UPDATE
            'nis' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('students', 'nis')->ignore($studentId)
            ],
        ];
    }
}
