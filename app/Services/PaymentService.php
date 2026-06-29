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
            // Ambil data invoice beserta data siswanya
            $invoice = Invoice::with('student')->lockForUpdate()->findOrFail($data['invoice_id']);

            $currentTotalPaid = $invoice->payments()->sum('amount');
            $remainingBill = $invoice->total_amount - $currentTotalPaid;

            // ================================================================
            // LOGIKA BARU: ATUR ALOKASI DANA SECARA OTOMATIS
            // ================================================================
            $paidForInvoice = $data['amount'];
            $excessAmount = 0;

            if ($remainingBill <= 0 || $invoice->status === 'PAID') {
                // KASUS 1: Jika invoice SUDAH LUNAS, maka tidak ada uang yang masuk ke invoice.
                // 100% Uang yang dibayarkan dialihkan menjadi SALDO SISWA.
                $paidForInvoice = 0;
                $excessAmount = $data['amount'];
            } else if ($data['amount'] > $remainingBill) {
                // KASUS 2: Jika invoice BELUM LUNAS tapi bayarnya LEBIH,
                // Ambil uang pas untuk melunasi invoice, sisanya jadi saldo.
                $paidForInvoice = $remainingBill;
                $excessAmount = $data['amount'] - $remainingBill;
            }

            // Generate nomor kuitansi otomatis (Contoh: PAY-202606-0001)
            $datePrefix = 'PAY-' . date('Ym');
            $lastPayment = \App\Models\Payment::where('payment_number', 'like', $datePrefix . '%')
                ->orderBy('payment_number', 'desc')
                ->first();

            $newNumber = $lastPayment ? (intval(substr($lastPayment->payment_number, -4)) + 1) : 1;
            $paymentNumber = $datePrefix . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            // Tambahkan catatan otomatis di kuitansi agar kasir/orang tua tidak bingung
            $notes = $data['notes'] ?? null;
            if ($paidForInvoice === 0) {
                $notes = trim(($notes ? $notes . ' | ' : '') . 'Uang dialihkan ke saldo karena tagihan ini sudah lunas sebelumnya.');
            } else if ($excessAmount > 0) {
                $notes = trim(($notes ? $notes . ' | ' : '') . 'Kelebihan bayar Rp ' . number_format($excessAmount, 0, ',', '.') . ' masuk ke saldo.');
            }

            // Simpan data pembayaran ke tabel payments
            $payment = \App\Models\Payment::create([
                'invoice_id'       => $invoice->id,
                'recorded_by'      => \Illuminate\Support\Facades\Auth::id(),
                'payment_number'   => $paymentNumber,
                'amount'           => $paidForInvoice, // Nominal masuk invoice (bisa 0 jika sudah lunas)
                'payment_date'     => $data['payment_date'],
                'payment_method'   => $data['payment_method'],
                'reference_number' => $data['reference_number'] ?? null,
                'notes'            => $notes,
            ]);

            // Update status & nominal terbayar di tabel invoices (HANYA jika invoice belum lunas)
            if ($remainingBill > 0 && $invoice->status !== 'PAID') {
                $newTotalPaid = $currentTotalPaid + $paidForInvoice;
                $invoice->update([
                    'status'      => $newTotalPaid >= $invoice->total_amount ? 'PAID' : 'PARTIAL',
                    'paid_amount' => $newTotalPaid
                ]);
            }

            // ================================================================
            // EKSEKUSI PENAMBAHAN SALDO SISWA (JIKA ADA KELEBIHAN)
            // ================================================================
            if ($excessAmount > 0) {
                $invoice->student->increment('balance', $excessAmount);
            }

            return $payment;
        });
    }

    public function processMultiPayment(array $data)
    {
        return DB::transaction(function () use ($data) {
            // $data['invoice_ids'] berupa array contoh: ["uuid-1", "uuid-2"]
            $invoiceIds = $data['invoice_ids'];
            $totalCashReceived = $data['amount']; // Total uang fisik yang diterima kasir

            // Ambil semua invoice yang di-request, urutkan berdasarkan tanggal dibuat (tertua dulu)
            $invoices = Invoice::with('student')
                ->whereIn('id', $invoiceIds)
                ->orderBy('created_at', 'asc')
                ->lockForUpdate()
                ->get();

            if ($invoices->isEmpty()) {
                throw new Exception("Tidak ada data tagihan yang valid.");
            }

            // Ambil data siswa dari invoice pertama (semua invoice pasti miliki siswa yang sama)
            $student = $invoices->first()->student;
            $remainingMoney = $totalCashReceived;
            $paymentsCreated = [];

            foreach ($invoices as $invoice) {
                // Jika uang kasir sudah habis, stop looping invoice selanjutnya
                if ($remainingMoney <= 0) {
                    break;
                }

                $currentTotalPaid = $invoice->payments()->sum('amount');
                $remainingBill = $invoice->total_amount - $currentTotalPaid;

                // Jika invoice ini ternyata sudah lunas, lewati ke invoice berikutnya
                if ($remainingBill <= 0 || $invoice->status === 'PAID') {
                    continue;
                }

                // Tentukan berapa nominal yang akan dialokasikan untuk invoice ini
                $paidForInvoice = 0;
                if ($remainingMoney >= $remainingBill) {
                    $paidForInvoice = $remainingBill; // Uang cukup/lebih untuk melunasi invoice ini
                    $remainingMoney -= $remainingBill; // Kurangi sisa uang kasir
                } else {
                    $paidForInvoice = $remainingMoney; // Uang sisa kasir hanya cukup untuk mencicil
                    $remainingMoney = 0; // Uang kasir habis
                }

                // Generate nomor kuitansi unik untuk tiap invoice
                $datePrefix = 'PAY-' . date('Ym');
                $lastPayment = \App\Models\Payment::where('payment_number', 'like', $datePrefix . '%')
                    ->orderBy('payment_number', 'desc')
                    ->first();
                $newNumber = $lastPayment ? (intval(substr($lastPayment->payment_number, -4)) + 1) : 1;
                $paymentNumber = $datePrefix . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

                // Buat data pembayaran kuitansi
                $payment = \App\Models\Payment::create([
                    'invoice_id'       => $invoice->id,
                    'recorded_by'      => \Illuminate\Support\Facades\Auth::id(),
                    'payment_number'   => $paymentNumber,
                    'amount'           => $paidForInvoice,
                    'payment_date'     => $data['payment_date'],
                    'payment_method'   => $data['payment_method'],
                    'reference_number' => $data['reference_number'] ?? null,
                    'notes'            => ($data['notes'] ?? '') . " (Pembayaran Multi-Tagihan)",
                ]);

                // Update Invoice
                $newTotalPaid = $currentTotalPaid + $paidForInvoice;
                $invoice->update([
                    'status'      => $newTotalPaid >= $invoice->total_amount ? 'PAID' : 'PARTIAL',
                    'paid_amount' => $newTotalPaid
                ]);

                $paymentsCreated[] = $payment;
            }

            // ================================================================
            // JIKA SEMUA INVOICE SUDAH DI-LOOP DAN MASIH ADA SISA UANG
            // MASUKKAN SISA AKHIRNYA KE SALDO DEPOSIT SISWA
            // ================================================================
            if ($remainingMoney > 0) {
                $student->increment('balance', $remainingMoney);

                // Opsional: Buat kuitansi dummy/catatan khusus saldo jika diperlukan
            }

            return [
                'payments' => $paymentsCreated,
                'excess_to_balance' => $remainingMoney
            ];
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
