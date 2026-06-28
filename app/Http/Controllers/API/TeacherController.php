<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeacherRequest;
use App\Http\Resources\TeacherResource;
use App\Services\TeacherService;

class TeacherController extends Controller
{
    protected $service;

    public function __construct(TeacherService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $teachers = $this->service->getAll();
        return response()->json([
            'success' => true,
            'data' => TeacherResource::collection($teachers)
        ]);
    }

    public function store(TeacherRequest $request)
    {
        $teacher = $this->service->create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Data ustadz dan akun login berhasil dibuat!',
            'data' => new TeacherResource($teacher)
        ], 201);
    }

    public function show($id)
    {
        $teacher = $this->service->findById($id);
        return response()->json([
            'success' => true,
            'data' => new TeacherResource($teacher)
        ]);
    }

    public function update(TeacherRequest $request, $id)
    {
        $teacher = $this->service->update($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Data ustadz berhasil diperbarui',
            'data' => new TeacherResource($teacher)
        ]);
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Data ustadz berhasil dihapus'
        ]);
    }
}
