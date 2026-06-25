<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Http\Requests\StudentRequest;
use App\Http\Resources\StudentResource;
use App\Services\StudentService;

class StudentController extends Controller
{
    protected $service;

    public function __construct(StudentService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $students = $this->service->getAll();
        return response()->json([
            'success' => true,
            'data' => StudentResource::collection($students)
        ]);
    }

    public function store(StudentRequest $request)
    {
        $student = $this->service->create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Data santri berhasil ditambahkan secara manual',
            'data' => new StudentResource($student)
        ], 201);
    }

    public function show(Student $student)
    {
        return response()->json([
            'success' => true,
            'data' => new StudentResource($student)
        ]);
    }

    public function update(StudentRequest $request, Student $student)
    {
        $updated = $this->service->update($student, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Data santri berhasil diperbarui secara manual',
            'data' => new StudentResource($updated)
        ]);
    }

    public function destroy(Student $student)
    {
        $this->service->delete($student);
        return response()->json([
            'success' => true,
            'message' => 'Data santri berhasil dihapus dari sistem'
        ]);
    }

    /**
     * Endpoint khusus pemicu tombol Sync di Frontend
     */
    public function sync(Student $student)
    {
        try {
            $syncedStudent = $this->service->syncWithExternal($student);
            return response()->json([
                'success' => true,
                'message' => 'Data santri berhasil disinkronkan dengan aplikasi pusat',
                'data' => new StudentResource($syncedStudent)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
