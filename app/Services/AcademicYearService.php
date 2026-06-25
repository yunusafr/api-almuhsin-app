<?php

namespace App\Services;

use App\Models\AcademicYear;
use Illuminate\Support\Facades\DB;

class AcademicYearService
{
    public function getAll()
    {
        return AcademicYear::orderBy('created_at', 'desc')->get();
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['is_active']) && $data['is_active']) {
                $this->deactivateAll();
            }

            return AcademicYear::create($data);
        });
    }

    public function update(AcademicYear $academicYear, array $data)
    {
        return DB::transaction(function () use ($academicYear, $data) {
            if (isset($data['is_active']) && $data['is_active']) {
                $this->deactivateAll();
            }

            $academicYear->update($data);
            return $academicYear;
        });
    }

    public function delete(AcademicYear $academicYear)
    {
        if ($academicYear->is_active) {
            throw new \Exception('Tidak dapat menghapus Tahun Pelajaran yang sedang aktif.');
        }

        $academicYear->delete();
    }

    private function deactivateAll()
    {
        AcademicYear::where('is_active', true)->update(['is_active' => false]);
    }
}
