<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Exception;

class PaymentService
{
    public function processPayment(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Ambil data invoice beserta data siswanya dengan lock agar aman dari race condition
            $invoice = Invoice::with('student')->lockForUpdate()->findOrFail($data['invoice_id']);
            $student = $invoice->student;

            // Jika tagihan sudah lunas, tolak pembayaran
            if ($invoice->status === 'PAID') {
                throw new Exception('Gagal! Tagihan ini sudah berstatus LUNAS (PAID).');
            }

            // 2. Hitung sisa tagihan aktual saat ini
            $currentTotalPaid = $invoice->payments()->sum('amount');
            $remainingBill = $invoice->total_amount - $currentTotalPaid;

            $paymentMethod = strtoupper($data['payment_method']);
            $inputAmount = $data['amount'];

            $paidForInvoice = 0;
            $excessAmount = 0;

            // ================================================================
            // ALUR A: JIKA BAYAR MENGGUNAKAN SALDO / DEPOSIT SISWA (Sesuai Poin 5)
            // ================================================================
            if ($paymentMethod === 'SALDO') {
                // Pastikan saldo siswa mencukupi nominal yang ingin dibayarkan
                if ($student->balance < $inputAmount) {
                    throw new Exception('Gagal! Saldo/Deposit siswa tidak mencukupi. Saldo saat ini: Rp ' . number_format($student->balance, 0, ',', '.'));
                }

                // Jika input bayar melebihi sisa tagihan, kita batasi senilai sisa tagihan saja
                $paidForInvoice = min($inputAmount, $remainingBill);

                // Potong saldo siswa sesuai yang benar-benar digunakan untuk bayar invoice
                $student->decrement('balance', $paidForInvoice);
                $excessAmount = 0;
            }
            // ================================================================
            // ALUR B: JIKA BAYAR TUNAI / TRANSFER / METODE EKSTERNAL LAINNYA
            // ================================================================
            else {
                if ($inputAmount > $remainingBill) {
                    // Jika bayar lebih, invoice dilunasi sesuai sisa tagihan, sisa uang masuk ke saldo
                    $paidForInvoice = $remainingBill;
                    $excessAmount = $inputAmount - $remainingBill;

                    // Masukkan ke saldo deposit siswa (Sesuai Poin 5)
                    $student->increment('balance', $excessAmount);
                } else {
                    // Pembayaran pas atau kurang / cicilan (Sesuai Poin 5)
                    $paidForInvoice = $inputAmount;
                    $excessAmount = 0;
                }
            }

            // 3. Buat nomor kuitansi otomatis
            $paymentNumber = 'PAY-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            // Tambahkan keterangan otomatis di kuitansi
            $notes = $data['notes'] ?? '';
            if ($excessAmount > 0) {
                $notes .= " (Kelebihan Rp " . number_format($excessAmount, 0, ',', '.') . " otomatis masuk ke saldo siswa)";
            }
            if ($paymentMethod === 'SALDO') {
                $notes .= " (Dibayar menggunakan Saldo/Deposit siswa)";
            }

            // 4. Simpan riwayat pembayaran ke database
            $payment = Payment::create([
                'invoice_id'       => $invoice->id,
                'recorded_by'      => Auth::id(), // ID Bendahara yang sedang login
                'payment_number'   => $paymentNumber,
                'amount'           => $paidForInvoice,
                'payment_date'     => $data['payment_date'],
                'payment_method'   => $paymentMethod,
                'reference_number' => $data['reference_number'] ?? null,
                'notes'            => trim($notes),
            ]);

            // 5. Update status dan paid_amount pada kepala invoice
            $newTotalPaid = $currentTotalPaid + $paidForInvoice;
            $invoice->update([
                'paid_amount' => $newTotalPaid,
                'status'      => $newTotalPaid >= $invoice->total_amount ? 'PAID' : 'PARTIAL',
            ]);

            // Mengembalikan object payment utuh agar tidak crash di Controller
            return $payment->load('invoice');
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
