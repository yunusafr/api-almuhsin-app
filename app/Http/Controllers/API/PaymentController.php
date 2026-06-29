<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Http\Resources\PaymentResource; // <-- JANGAN LUPA IMPORT INI
use App\Services\PaymentService;
use Exception;

class PaymentController extends Controller
{
    protected $service;

    public function __construct(PaymentService $service)
    {
        $this->service = $service;
    }

    public function store(PaymentRequest $request)
    {
        try {
            $payment = $this->service->processPayment($request->validated());

            // Load relasi kasir agar namanya ikut tampil di Resource
            $payment->load('cashier');

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil diproses. Kuitansi diterbitkan.',
                'invoice_status' => $payment->invoice->status, // Lempar status terbaru untuk update UI frontend
                'data'    => new PaymentResource($payment) // <-- MENGGUNAKAN RESOURCE
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function show($invoiceId)
    {
        $history = $this->service->getInvoiceHistory($invoiceId);

        return response()->json([
            'success' => true,
            'data'    => PaymentResource::collection($history) // <-- MENGGUNAKAN RESOURCE COLLECTION
        ]);
    }
}
