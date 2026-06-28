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

    public function index(\Illuminate\Http\Request $request)
    {
        // Tangkap query parameter 'status' dari URL
        $status = $request->query('status');

        // Lempar variabel status ke dalam service
        $students = $this->service->getAll($status);

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
     * Endpoint untuk Sinkronisasi Semua Data Siswa Berdasarkan NIS
     */
    public function sync(\Illuminate\Http\Request $request)
    {
        try {
            // Ambil semua data siswa yang saat ini ada di database Laravel
            $localStudents = \App\Models\Student::all();

            if ($localStudents->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data siswa di database lokal untuk disinkronkan.'
                ], 400);
            }

            $updatedCount = 0;

            // Looping setiap siswa lokal untuk dicari data terbarunya di aplikasi sebelah
            foreach ($localStudents as $student) {
                // Tembak API eksternal menggunakan NIS siswa tersebut
                $externalData = $this->service->searchExternalStudents($student->nis);

                // Jika data ditemukan di aplikasi sebelah
                if (!empty($externalData) && isset($externalData[0])) {
                    $freshData = $externalData[0]; // Ambil baris pertama hasil pencarian

                    // Update data di database Laravel dengan data terbaru dari pusat
                    $student->update([
                        'name'           => $freshData['name'],
                        'birth_place'    => $freshData['birth_place'],
                        'birth_date'     => $freshData['birth_date'],
                        'address'        => $freshData['address'],
                        'guardian_name'  => $freshData['guardian_name'],
                        'guardian_phone' => $freshData['guardian_phone'],
                        'rombel'         => $freshData['rombel'],
                        'tingkat'           => $freshData['tingkat'],
                    ]);

                    $updatedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Proses sinkronisasi selesai. Berhasil memperbarui {$updatedCount} data siswa berdasarkan data pusat terbaru."
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
