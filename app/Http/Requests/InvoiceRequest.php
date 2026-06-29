<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Wajib diubah jadi true agar request lolos
    }

    public function rules(): array
    {
        return [
            // Validasi Kepala Tagihan
            'student_id' => 'required|uuid|exists:students,id',
            'due_date' => 'required|date',

            // Validasi Rincian Tagihan (Berbentuk Array)
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:SPP,DAFTAR_ULANG_BARU,DAFTAR_ULANG_LAMA,SPP_PKL,INSIDENTAL,TUNGGAKAN_LAMA',
            'items.*.description' => 'nullable|string|max:255',
            'items.*.amount' => 'required|numeric|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Minimal harus ada 1 rincian tagihan yang dimasukkan.',
            'items.*.type.in' => 'Jenis tagihan tidak valid.',
        ];
    }
}
