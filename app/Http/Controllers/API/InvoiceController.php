<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Services\InvoiceService;
use Exception;

class InvoiceController extends Controller
{
    protected $service;

    public function __construct(InvoiceService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $invoices = $this->service->getAll();
        return response()->json([
            'success' => true,
            'data' => InvoiceResource::collection($invoices)
        ]);
    }

    public function store(InvoiceRequest $request)
    {
        $invoice = $this->service->create($request->validated());

        // (Tempat trigger Queue Job Notifikasi WA nanti ditaruh di sini)

        return response()->json([
            'success' => true,
            'message' => 'Tagihan berhasil dibuat!',
            'data' => new InvoiceResource($invoice)
        ], 201);
    }

    public function show($id)
    {
        $invoice = $this->service->findById($id);
        return response()->json([
            'success' => true,
            'data' => new InvoiceResource($invoice)
        ]);
    }

    public function destroy($id)
    {
        try {
            $this->service->delete($id);
            return response()->json([
                'success' => true,
                'message' => 'Tagihan berhasil dihapus.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400); // Bad Request
        }
    }
}
