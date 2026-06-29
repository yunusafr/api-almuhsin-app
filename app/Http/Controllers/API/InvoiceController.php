<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Exception;

class InvoiceController extends Controller
{
    protected $service;

    public function __construct(InvoiceService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        // Mengambil filter dari parameter URL, contoh: ?month=06&status=UNPAID
        $filters = $request->only(['month', 'year', 'status']);

        $invoices = $this->service->getAll($filters);

        return response()->json([
            'success' => true,
            'data' => InvoiceResource::collection($invoices)
        ]);
    }

    public function store(InvoiceRequest $request)
    {
        $invoices = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => count($invoices) . ' Tagihan (Invoice) berhasil diterbitkan!',
            'data' => InvoiceResource::collection(collect($invoices))
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
            ], 400);
        }
    }
}
