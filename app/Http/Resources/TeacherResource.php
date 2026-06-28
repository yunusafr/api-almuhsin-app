<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, // Ini akan menampilkan string UUID ustadz
            'user_id' => $this->user_id, // Menampilkan ID akun pasangannya
            'name' => $this->name,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'address' => $this->address,
            'created_at' => $this->created_at,
        ];
    }
}
