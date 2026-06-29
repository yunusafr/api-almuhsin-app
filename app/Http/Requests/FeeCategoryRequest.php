<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeeCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ubah jadi true agar request diizinkan
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'invoice_type' => 'required|in:SPP,DAFTAR_ULANG_BARU,DAFTAR_ULANG_LAMA,SPP_PKL,INSIDENTAL,TUNGGAKAN_LAMA',
            'default_amount' => 'required|numeric|min:0',
            'default_description' => 'nullable|string|max:255',
        ];
    }
}
