<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class InvoiceService
{
    public function getAll()
    {
        return Invoice::with(['student', 'items'])->orderBy('created_at', 'desc')->get();
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Generate Nomor Invoice (Contoh: INV-20260629-ABCD)
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            // 2. Hitung total amount dari array items
            $totalAmount = collect($data['items'])->sum('amount');

            // 3. Buat Kepala Invoice
            $invoice = Invoice::create([
                'student_id' => $data['student_id'],
                'invoice_number' => $invoiceNumber,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'status' => 'UNPAID',
                'due_date' => $data['due_date'],
            ]);

            // 4. Masukkan Rincian Items
            foreach ($data['items'] as $item) {
                $invoice->items()->create([
                    'type' => $item['type'],
                    'description' => $item['description'] ?? null,
                    'amount' => $item['amount'],
                ]);
            }

            return $invoice->load(['student', 'items']);
        });
    }

    public function findById($id)
    {
        return Invoice::with(['student', 'items'])->findOrFail($id);
    }

    public function delete($id)
    {
        $invoice = $this->findById($id);

        // Validasi: Hanya tagihan UNPAID yang boleh dihapus
        if ($invoice->status !== 'UNPAID') {
            throw new Exception('Gagal! Hanya tagihan yang belum dibayar sama sekali (UNPAID) yang bisa dihapus.');
        }

        $invoice->delete();
        return true;
    }
}
