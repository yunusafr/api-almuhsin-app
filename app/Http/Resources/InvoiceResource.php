<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            // Cek jika relasi student di-load, maka tampilkan id dan namanya
            'student' => $this->whenLoaded('student', function () {
                return [
                    'id' => $this->student->id,
                    'name' => $this->student->name,
                ];
            }),
            'total_amount' => $this->total_amount,
            'paid_amount' => $this->paid_amount,
            'status' => $this->status,
            'due_date' => $this->due_date,
            // Tampilkan rincian tagihan
            'items' => InvoiceItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at,
        ];
    }
}
