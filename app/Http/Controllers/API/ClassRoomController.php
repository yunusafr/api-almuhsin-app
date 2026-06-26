<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Http\Requests\ClassRoomRequest;
use App\Http\Resources\ClassRoomResource;
use App\Services\ClassRoomService;

class ClassRoomController extends Controller
{
    protected $service;

    public function __construct(ClassRoomService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $classes = $this->service->getAll();
        return response()->json([
            'success' => true,
            'data' => ClassRoomResource::collection($classes)
        ]);
    }

    public function store(ClassRoomRequest $request)
    {
        $class = $this->service->create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Data kelas berhasil ditambahkan',
            'data' => new ClassRoomResource($class)
        ], 201);
    }

    public function show(ClassRoom $classRoom)
    {
        return response()->json([
            'success' => true,
            'data' => new ClassRoomResource($classRoom)
        ]);
    }

    public function update(ClassRoomRequest $request, ClassRoom $classRoom)
    {
        $updated = $this->service->update($classRoom, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Data kelas berhasil diperbarui',
            'data' => new ClassRoomResource($updated)
        ]);
    }

    public function destroy(ClassRoom $classRoom)
    {
        $this->service->delete($classRoom);
        return response()->json([
            'success' => true,
            'message' => 'Data kelas berhasil dihapus'
        ]);
    }
}
