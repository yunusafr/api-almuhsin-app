<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // <-- WAJIB DIPANGGIL

class Teacher extends Model
{
    use HasFactory, HasUuids; // <-- Gunakan HasUuids di sini

    protected $fillable = [
        'user_id',
        'name',
        'gender',
        'phone',
        'address',
    ];
}
