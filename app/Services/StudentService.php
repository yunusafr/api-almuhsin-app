<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StudentService
{
    public function getAll()
    {
        return Student::orderBy('name', 'asc')->get();
    }

    public function create(array $data)
    {
        return Student::create($data);
    }

    public function update(Student $student, array $data)
    {
        $student->update($data);
        return $student;
    }

    public function delete(Student $student)
    {
        $student->delete();
    }

    /**
     * Logika untuk Tombol Sinkronisasi ke Aplikasi Sebelah Berdasarkan NIS
     */
    public function syncWithExternal(Student $student)
    {
        if (!$student->nis) {
            throw new \Exception('Santri ini tidak memiliki NIS untuk disinkronkan.');
        }

        try {
            // URL API Aplikasi Data Siswa Pusat/Sebelah
            $externalUrl = 'https://api-datasiswa-sebelah.com/api/v1/get-student/' . $student->nis;

            /* // --- KODE INTEGRASI ASLI (Aktifkan jika API sebelah sudah siap digunakan) ---
            $response = Http::withHeaders([
                'Authorization' => 'Bearer TOKEN_RAHASIA_APLIKASI_SEBELAH'
            ])->get($externalUrl);

            if ($response->successful()) {
                $externalData = $response->json()['data'];
                
                $student->update([
                    'name'           => $externalData['nama_lengkap'],
                    'birth_place'    => $externalData['tempat_lahir'],
                    'birth_date'     => $externalData['tanggal_lahir'],
                    'address'        => $externalData['alamat_tinggal'],
                    'guardian_name'  => $externalData['nama_wali'],
                    'guardian_phone' => $externalData['no_hp_wali'],
                    'rombel'         => $externalData['nama_kelas_sekarang'],
                    'status'         => $externalData['is_active'] == 1 ? 'aktif' : 'keluar',
                ]);
                
                return $student;
            }
            throw new \Exception('Gagal mengambil data dari aplikasi pusat.');
            */

            // --- SIMULASI MOCK DATA (Untuk Keperluan Testing Postman Saat Ini) ---
            $student->update([
                'name' => $student->name . ' (Synced)',
                'rombel' => 'Rombel Updated by Sync',
                'status' => 'aktif'
            ]);

            return $student;
        } catch (\Exception $e) {
            Log::error('Sync Student Error: ' . $e->getMessage());
            throw new \Exception('Koneksi ke aplikasi pusat terganggu: ' . $e->getMessage());
        }
    }
}
