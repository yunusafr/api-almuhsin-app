<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeeCategoryRequest;
use App\Http\Resources\FeeCategoryResource;
use App\Models\FeeCategory;
use Illuminate\Http\Request;

class FeeCategoryController extends Controller
{
    // 1. READ ALL (Menampilkan Semua Kategori untuk Dropdown Frontend)
    public function index()
    {
        $categories = FeeCategory::orderBy('name', 'asc')->get();
        return response()->json([
            'success' => true,
            'data' => FeeCategoryResource::collection($categories)
        ]);
    }

    // 2. CREATE (Menyimpan Kategori Baru)
    public function store(FeeCategoryRequest $request)
    {
        $category = FeeCategory::create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Kategori pembayaran berhasil ditambahkan!',
            'data' => new FeeCategoryResource($category)
        ], 201);
    }

    // 3. READ SINGLE (Melihat Detail Satu Kategori)
    public function show($id)
    {
        $category = FeeCategory::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => new FeeCategoryResource($category)
        ]);
    }

    // 4. UPDATE (Mengubah Nominal Default atau Nama Kategori)
    public function update(FeeCategoryRequest $request, $id)
    {
        $category = FeeCategory::findOrFail($id);
        $category->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kategori pembayaran berhasil diperbarui.',
            'data' => new FeeCategoryResource($category)
        ]);
    }

    // 5. DELETE (Menghapus Kategori Master)
    public function destroy($id)
    {
        $category = FeeCategory::findOrFail($id);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori pembayaran berhasil dihapus.'
        ]);
    }
}
