<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_id'       => ['required', 'uuid', 'exists:invoices,id'],
            'amount'           => ['required', 'numeric', 'min:1'],
            'payment_date'     => ['required', 'date'],
            'payment_method'   => ['required', 'string', 'max:50'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes'            => ['nullable', 'string'],
        ];
    }
}
