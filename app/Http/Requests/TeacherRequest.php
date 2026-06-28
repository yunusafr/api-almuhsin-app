<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ubah jadi true agar request diizinkan
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ];

        // 👇 VALIDASI KHUSUS: Jika method-nya POST (Tambah Data), baru email diwajibkan
        if ($this->isMethod('post')) {
            $rules['email'] = 'required|email|unique:users,email';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'gender.in' => 'Gender harus diisi L (Laki-laki) atau P (Perempuan).',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
        ];
    }
}
