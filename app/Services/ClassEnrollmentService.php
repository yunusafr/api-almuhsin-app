<?php

namespace App\Services;

use App\Models\ClassEnrollment;

class ClassEnrollmentService
{
    public function getAll()
    {
        // Ambil data sekaligus me-load relasi untuk menghindari N+1 Query Problem
        return ClassEnrollment::with(['student', 'classRoom', 'academicYear'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(array $data)
    {
        // Cek apakah santri sudah diplot di tahun ajaran yang sama agar tidak error 500 (Unique Constraint)
        $exists = ClassEnrollment::where('student_id', $data['student_id'])
            ->where('academic_year_id', $data['academic_year_id'])
            ->exists();

        if ($exists) {
            throw new \Exception('Santri ini sudah terdaftar di kelas lain pada tahun ajaran yang sama.');
        }

        return ClassEnrollment::create($data);
    }

    public function update(ClassEnrollment $enrollment, array $data)
    {
        // Jika update mengubah tahun/santri, cek lagi duplikasinya (kecuali untuk dirinya sendiri)
        $exists = ClassEnrollment::where('student_id', $data['student_id'])
            ->where('academic_year_id', $data['academic_year_id'])
            ->where('id', '!=', $enrollment->id)
            ->exists();

        if ($exists) {
            throw new \Exception('Santri ini sudah terdaftar di kelas lain pada tahun ajaran yang sama.');
        }

        $enrollment->update($data);
        return $enrollment;
    }

    public function delete(ClassEnrollment $enrollment)
    {
        return $enrollment->delete();
    }
}
