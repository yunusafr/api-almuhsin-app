<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Http\Requests\AcademicYearRequest;
use App\Http\Resources\AcademicYearResource;
use App\Services\AcademicYearService;

class AcademicYearController extends Controller
{
    protected $service;

    public function __construct(AcademicYearService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $academicYears = $this->service->getAll();
        return response()->json([
            'success' => true,
            'data' => AcademicYearResource::collection($academicYears)
        ]);
    }

    public function store(AcademicYearRequest $request)
    {
        $academicYear = $this->service->create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Tahun pelajaran berhasil ditambahkan',
            'data' => new AcademicYearResource($academicYear)
        ], 201);
    }

    public function show(AcademicYear $academicYear)
    {
        return response()->json([
            'success' => true,
            'data' => new AcademicYearResource($academicYear)
        ]);
    }

    public function getActive()
    {
        try {
            // Asumsi: Anda memiliki kolom 'is_active' (boolean) atau 'status' = 'aktif'
            // Sesuaikan nama kolomnya dengan yang ada di database Anda
            $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();

            if (!$activeYear) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada tahun ajaran yang sedang aktif saat ini.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengambil tahun ajaran aktif',
                'data' => $activeYear
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(AcademicYearRequest $request, AcademicYear $academicYear)
    {
        $updated = $this->service->update($academicYear, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Tahun pelajaran berhasil diubah',
            'data' => new AcademicYearResource($updated)
        ]);
    }

    public function destroy(AcademicYear $academicYear)
    {
        try {
            $this->service->delete($academicYear);
            return response()->json([
                'success' => true,
                'message' => 'Tahun pelajaran berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
