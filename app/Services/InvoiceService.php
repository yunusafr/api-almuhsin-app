<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class InvoiceService
{
    public function getAll($filters = [])
    {
        $query = Invoice::with(['student', 'items']);

        // 1. Filter Berdasarkan Bulan & Tahun (Default: Bulan & Tahun Saat Ini)
        $month = $filters['month'] ?? date('m');
        $year = $filters['year'] ?? date('Y');
        $query->whereMonth('created_at', $month)->whereYear('created_at', $year);

        // 2. Filter Berdasarkan Status jika di-request (misal: UNPAID saja)
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $createdInvoices = [];
            $datePart = date('Ymd');

            // Memproses looping data array invoice yang dikirim dari frontend
            foreach ($data['invoices'] as $invoiceData) {
                $invoiceNumber = 'INV-' . $datePart . '-' . strtoupper(Str::random(4));
                $totalAmount = collect($invoiceData['items'])->sum('amount');

                // Buat Kepala Invoice
                $invoice = Invoice::create([
                    'student_id' => $invoiceData['student_id'],
                    'invoice_number' => $invoiceNumber,
                    'total_amount' => $totalAmount,
                    'paid_amount' => 0,
                    'status' => 'UNPAID',
                    'due_date' => $invoiceData['due_date'],
                ]);

                // Buat Rincian Item Tagihan
                foreach ($invoiceData['items'] as $item) {
                    $invoice->items()->create([
                        'type' => $item['type'],
                        'description' => $item['description'] ?? null,
                        'amount' => $item['amount'],
                    ]);
                }

                $createdInvoices[] = $invoice->load(['student', 'items']);
            }

            return $createdInvoices;
        });
    }

    public function findById($id)
    {
        return Invoice::with(['student', 'items'])->findOrFail($id);
    }

    public function delete($id)
    {
        $invoice = $this->findById($id);

        if ($invoice->status !== 'UNPAID') {
            throw new Exception('Gagal! Hanya tagihan yang belum dibayar (UNPAID) yang bisa dihapus.');
        }

        $invoice->delete();
        return true;
    }
}
