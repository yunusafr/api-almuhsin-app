<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // 1. Hitung total yang sudah dibayar dari tabel pembayaran (payments)
        // Kita gunakan ternary operator (? :) untuk jaga-jaga jika relasi payments belum ter-load
        $totalPaid = $this->payments ? $this->payments->sum('amount') : 0;

        // 2. Hitung sisa tagihan
        $remainingAmount = $this->total_amount - $totalPaid;

        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,

            // Format student dikembalikan
            'student' => $this->whenLoaded('student', function () {
                return [
                    'id' => $this->student->id,
                    'name' => $this->student->name,
                ];
            }),

            'total_amount' => (float) $this->total_amount,

            // PERBAIKAN DI SINI: Gunakan variabel hasil hitung $totalPaid, BUKAN $this->paid_amount
            'paid_amount' => (float) $totalPaid,

            // PERBAIKAN DI SINI: Tambahkan sisa tagihan
            'remaining_amount' => (float) $remainingAmount,

            'status' => $this->status,
            'due_date' => $this->due_date,

            // Rincian items bawaan Anda tetap aman
            'items' => InvoiceItemResource::collection($this->whenLoaded('items')),

            'created_at' => $this->created_at,
        ];
    }
}
