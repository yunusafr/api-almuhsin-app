<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ClassEnrollment;
use App\Http\Requests\ClassEnrollmentRequest;
use App\Http\Resources\ClassEnrollmentResource;
use App\Services\ClassEnrollmentService;

class ClassEnrollmentController extends Controller
{
    protected $service;

    public function __construct(ClassEnrollmentService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $enrollments = $this->service->getAll();
        return response()->json([
            'success' => true,
            'data' => ClassEnrollmentResource::collection($enrollments)
        ]);
    }

    public function store(ClassEnrollmentRequest $request)
    {
        try {
            $enrollment = $this->service->create($request->validated());

            // Load relasi agar response-nya lengkap ada nama siswa dll
            $enrollment->load(['student', 'classRoom', 'academicYear']);

            return response()->json([
                'success' => true,
                'message' => 'Santri berhasil diploting ke kelas',
                'data' => new ClassEnrollmentResource($enrollment)
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function show(ClassEnrollment $enrollment)
    {
        $enrollment->load(['student', 'classRoom', 'academicYear']);
        return response()->json([
            'success' => true,
            'data' => new ClassEnrollmentResource($enrollment)
        ]);
    }

    public function update(ClassEnrollmentRequest $request, ClassEnrollment $enrollment)
    {
        try {
            $updated = $this->service->update($enrollment, $request->validated());
            $updated->load(['student', 'classRoom', 'academicYear']);

            return response()->json([
                'success' => true,
                'message' => 'Data ploting berhasil diperbarui',
                'data' => new ClassEnrollmentResource($updated)
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function destroy(ClassEnrollment $enrollment)
    {
        $this->service->delete($enrollment);
        return response()->json([
            'success' => true,
            'message' => 'Data ploting santri berhasil dihapus'
        ]);
    }
}
