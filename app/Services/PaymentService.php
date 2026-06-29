<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class PaymentService
{
    public function processPayment(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Kunci data invoice agar tidak terjadi race condition (double click transaksi)
            $invoice = Invoice::lockForUpdate()->findOrFail($data['invoice_id']);

            // 2. Hitung sisa tagihan yang belum dibayar
            // Di asumsikan model Invoice Anda memiliki relasi 'payments' atau kolom 'total_paid'
            $currentTotalPaid = $invoice->payments()->sum('amount');
            $remainingBill = $invoice->total_amount - $currentTotalPaid;

            // 3. Validasi jika invoice sudah lunas
            if ($invoice->status === 'PAID' || $remainingBill <= 0) {
                throw new Exception('Transaksi ditolak. Invoice ini sudah lunas.');
            }

            // 4. Validasi jika uang yang dibayarkan melebihi sisa tagihan
            if ($data['amount'] > $remainingBill) {
                throw new Exception("Nominal bayar melebihi sisa tagihan. Sisa tagihan: Rp " . number_format($remainingBill, 0, ',', '.'));
            }

            // 5. Generate nomor kuitansi otomatis (Contoh: PAY-202606-0001)
            $datePrefix = 'PAY-' . date('Ym');
            $lastPayment = Payment::where('payment_number', 'like', $datePrefix . '%')
                ->orderBy('payment_number', 'desc')
                ->first();

            if ($lastPayment) {
                $lastNumber = intval(substr($lastPayment->payment_number, -4));
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            $paymentNumber = $datePrefix . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            // 6. Simpan data pembayaran ke tabel payments
            $payment = Payment::create([
                'invoice_id'       => $invoice->id,
                'recorded_by'      => Auth::id(), // ID Admin/Kasir yang sedang login via Sanctum
                'payment_number'   => $paymentNumber,
                'amount'           => $data['amount'],
                'payment_date'     => $data['payment_date'],
                'payment_method'   => $data['payment_method'],
                'reference_number' => $data['reference_number'] ?? null,
                'notes'            => $data['notes'] ?? null,
            ]);

            // 7. Update status invoice berdasarkan total akumulasi pembayaran baru
            $newTotalPaid = $currentTotalPaid + $data['amount'];

            if ($newTotalPaid >= $invoice->total_amount) {
                $invoice->update(['status' => 'PAID']);
            } else {
                $invoice->update(['status' => 'PARTIAL']);
            }

            return $payment;
        });
    }

    public function getInvoiceHistory($invoiceId)
    {
        return Payment::where('invoice_id', $invoiceId)
            ->with('cashier:id,name')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
