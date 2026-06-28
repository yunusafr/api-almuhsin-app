<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherService
{
    public function getAll()
    {
        return Teacher::orderBy('name', 'asc')->get();
    }

    public function create(array $data)
    {
        // DB Transaction memastikan jika salah satu proses gagal, semua dibatalkan otomatis
        DB::beginTransaction();

        try {
            // 1. Daftarkan akun ustadz otomatis ke tabel users untuk modal login
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password123'), // Password default pertama kali login
            ]);

            // 2. Selipkan ID user yang baru terbuat ke array $data
            $data['user_id'] = $user->id;

            // 3. Simpan biodata ke tabel teachers (id UUID akan otomatis digenerate Laravel)
            $teacher = Teacher::create($data);

            DB::commit(); // Simpan permanen ke database

            return $teacher;
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua jika ada error
            throw $e;
        }
    }

    public function findById($id)
    {
        return Teacher::findOrFail($id);
    }

    public function update($id, array $data)
    {
        $teacher = $this->findById($id);
        $teacher->update($data);

        return $teacher;
    }

    public function delete($id)
    {
        $teacher = $this->findById($id);
        $teacher->delete();

        return true;
    }
}
