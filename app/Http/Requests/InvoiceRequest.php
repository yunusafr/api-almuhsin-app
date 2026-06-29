<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Menerima pembungkus utama bernama 'invoices' (array)
            'invoices' => 'required|array|min:1',
            'invoices.*.student_id' => 'required|uuid|exists:students,id',
            'invoices.*.due_date' => 'required|date',

            // Item tagihan kustom per siswa
            'invoices.*.items' => 'required|array|min:1',
            'invoices.*.items.*.type' => 'required|in:SPP,DAFTAR_ULANG_BARU,DAFTAR_ULANG_LAMA,SPP_PKL,INSIDENTAL,TUNGGAKAN_LAMA',
            'invoices.*.items.*.description' => 'nullable|string|max:255',
            'invoices.*.items.*.amount' => 'required|numeric|min:0', // min:0 untuk mengakomodasi "SPP nominal 0"
        ];
    }
}
