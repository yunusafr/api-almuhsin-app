<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'invoice_id'       => $this->invoice_id,
            'payment_number'   => $this->payment_number,
            'amount'           => (float) $this->amount,
            'payment_date'     => $this->payment_date,
            'payment_method'   => $this->payment_method,
            'reference_number' => $this->reference_number,
            'notes'            => $this->notes,
            // Mengambil nama kasir jika relasinya diload
            'cashier_name'     => $this->whenLoaded('cashier', fn() => $this->cashier->name),
            'created_at'       => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}
