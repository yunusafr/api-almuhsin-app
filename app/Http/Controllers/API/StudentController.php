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
     * Endpoint untuk mencari data santri di aplikasi sebelah
     */
    public function searchExternal(\Illuminate\Http\Request $request)
    {
        $request->validate(['q' => 'required|string']);

        try {
            $results = $this->service->searchExternalStudents($request->q);
            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Endpoint untuk mengeksekusi rombongan santri yang dicentang
     */
    public function pullExternal(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'students' => 'required|array',
            'students.*.nis' => 'required|string',
            'students.*.name' => 'required|string'
        ]);

        try {
            $resultCount = $this->service->pullSelectedStudents($request->students);
            return response()->json([
                'success' => true,
                'message' => "Proses tarik data selesai. {$resultCount['inserted']} santri baru ditambahkan, {$resultCount['updated']} santri diperbarui."
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Endpoint untuk tombol Sync per-satu siswa
     */
    public function sync(\App\Models\Student $student)
    {
        try {
            $updatedStudent = $this->service->syncWithExternal($student);
            return response()->json([
                'success' => true,
                'message' => 'Data santri berhasil disinkronkan dengan aplikasi pusat',
                'data' => $updatedStudent
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
