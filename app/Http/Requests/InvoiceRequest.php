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
            // Mendukung penerbitan masal di awal bulan (Poin 3)
            'invoices' => 'required|array|min:1',
            'invoices.*.student_id' => 'required|uuid|exists:students,id',
            'invoices.*.due_date' => 'required|date',

            // Validasi tipe item tagihan yang disesuaikan
            'invoices.*.items' => 'required|array|min:1',
            'invoices.*.items.*.type' => 'required|in:SPP_NORMAL,SPP_PKL,SPP_BEASISWA,DAFTAR_ULANG_BARU,DAFTAR_ULANG_LAMA,INSIDENTAL,LAINNYA',
            'invoices.*.items.*.description' => 'nullable|string|max:255',
            'invoices.*.items.*.amount' => 'required|numeric|min:0',
        ];
    }
}
