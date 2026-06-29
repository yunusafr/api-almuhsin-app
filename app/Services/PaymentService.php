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
            // Eager load relasi student agar kita bisa langsung menambahkan saldonya
            $invoice = Invoice::with('student')->lockForUpdate()->findOrFail($data['invoice_id']);

            $currentTotalPaid = $invoice->payments()->sum('amount');
            $remainingBill = $invoice->total_amount - $currentTotalPaid;

            if ($invoice->status === 'PAID' || $remainingBill <= 0) {
                throw new Exception('Transaksi ditolak. Invoice ini sudah lunas.');
            }

            // ================================================================
            // LOGIKA BARU: PISAHKAN UANG UNTUK TAGIHAN DAN UANG KELEBIHAN (SALDO)
            // ================================================================
            $paidForInvoice = $data['amount'];
            $excessAmount = 0;

            if ($data['amount'] > $remainingBill) {
                $paidForInvoice = $remainingBill; // Yang masuk ke riwayat invoice HANYA sebesar sisa tagihan
                $excessAmount = $data['amount'] - $remainingBill; // Sisanya kita simpan sebagai saldo
            }

            // Generate nomor kuitansi otomatis (Contoh: PAY-202606-0001)
            $datePrefix = 'PAY-' . date('Ym');
            $lastPayment = \App\Models\Payment::where('payment_number', 'like', $datePrefix . '%')
                ->orderBy('payment_number', 'desc')
                ->first();

            $newNumber = $lastPayment ? (intval(substr($lastPayment->payment_number, -4)) + 1) : 1;
            $paymentNumber = $datePrefix . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            // Simpan data pembayaran ke tabel payments MENGGUNAKAN $paidForInvoice
            $payment = \App\Models\Payment::create([
                'invoice_id'       => $invoice->id,
                'recorded_by'      => \Illuminate\Support\Facades\Auth::id(),
                'payment_number'   => $paymentNumber,
                'amount'           => $paidForInvoice, // <-- Nominal yang dimasukkan hanya uang pasnya saja
                'payment_date'     => $data['payment_date'],
                'payment_method'   => $data['payment_method'],
                'reference_number' => $data['reference_number'] ?? null,
                'notes'            => $data['notes'] ?? null,
            ]);

            // Update status invoice DAN paid_amount secara bersamaan
            $newTotalPaid = $currentTotalPaid + $paidForInvoice;

            if ($newTotalPaid >= $invoice->total_amount) {
                $invoice->update(['status' => 'PAID', 'paid_amount' => $newTotalPaid]);
            } else {
                $invoice->update(['status' => 'PARTIAL', 'paid_amount' => $newTotalPaid]);
            }

            // ================================================================
            // JIKA ADA KELEBIHAN UANG, TAMBAHKAN KE SALDO (DOMPET) SISWA
            // ================================================================
            if ($excessAmount > 0) {
                // Perintah increment() akan otomatis menambah angka di database
                $invoice->student->increment('balance', $excessAmount);
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
