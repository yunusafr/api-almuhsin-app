<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StudentService
{
    // Silakan sesuaikan URL domain aplikasi PHP Native Anda di sini
    protected $externalBaseUrl = 'https://induk.ingintau.my.id';
    protected $apiKey = 'TUsmekisa1968';

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
     * 1. FITUR TARIK DATA: Cari data santri ke aplikasi PHP Native Sebelah (REAL API)
     */
    public function searchExternalStudents($keyword)
    {
        try {
            // Tembak API Native dengan Header X-API-KEY
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey
            ])->get($this->externalBaseUrl . '/apiSearchSiswa', [
                'q' => $keyword
            ]);

            // --- TAMBAHAN KODE UNTUK DEBUGGING ---
            // Jika response gagal di-parse menjadi JSON, kita lempar isi aslinya!
            if (is_null($response->json())) {
                throw new \Exception("Response sebelah bukan JSON! Isinya: " . $response->body());
            }
            // -------------------------------------

            if ($response->successful() && $response->json()['status'] === 'success') {
                return $response->json()['data'];
            }

            return [];
        } catch (\Exception $e) {
            throw new \Exception('Gagal terhubung ke aplikasi pusat: ' . $e->getMessage());
        }
    }

    /**
     * 2. FITUR TARIK DATA: Eksekusi simpan rombongan data yang dicentang frontend
     */
    public function pullSelectedStudents(array $checkedStudents)
    {
        $insertedCount = 0;
        $updatedCount = 0;

        foreach ($checkedStudents as $data) {
            $student = Student::updateOrCreate(
                ['nis' => $data['nis']],
                [
                    'name'           => $data['name'],
                    'birth_place'    => $data['birth_place'] ?? null,
                    'birth_date'     => $data['birth_date'] ?? null,
                    'address'        => $data['address'] ?? null,
                    'guardian_name'  => $data['guardian_name'] ?? null,
                    'guardian_phone' => $data['guardian_phone'] ?? null,
                    'rombel'         => $data['rombel'] ?? null,
                    'status'         => 'aktif', // Default langsung aktif saat ditarik
                ]
            );

            if ($student->wasRecentlyCreated) {
                $insertedCount++;
            } else {
                $updatedCount++;
            }
        }

        return [
            'inserted' => $insertedCount,
            'updated' => $updatedCount
        ];
    }

    /**
     * 3. FITUR SYNC SATUAN: Memperbarui data siswa yang sudah ada di database Laravel
     */
    public function syncWithExternal(Student $student)
    {
        if (!$student->nis) {
            throw new \Exception('Santri ini tidak memiliki NIS untuk disinkronkan.');
        }

        try {
            // Tembak API Detail milik PHP Native
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey
            ])->get($this->externalBaseUrl . '/apiDetailSiswa', [
                'nis' => $student->nis
            ]);

            if ($response->successful() && $response->json()['status'] === 'success') {
                $externalData = $response->json()['data'];

                if ($externalData) {
                    $student->update([
                        'name'           => $externalData['name'],
                        'birth_place'    => $externalData['birth_place'],
                        'birth_date'     => $externalData['birth_date'],
                        'address'        => $externalData['address'],
                        'guardian_name'  => $externalData['guardian_name'],
                        'guardian_phone' => $externalData['guardian_phone'],
                        'rombel'         => $externalData['rombel'],
                        // status tetap dipertahankan sesuai kondisi lokal database laravel Anda
                    ]);

                    return $student;
                }
            }

            throw new \Exception('Data siswa tidak ditemukan di aplikasi pusat.');
        } catch (\Exception $e) {
            Log::error('Sync Student Error: ' . $e->getMessage());
            throw new \Exception('Koneksi ke aplikasi pusat terganggu: ' . $e->getMessage());
        }
    }
}
