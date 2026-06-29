<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Student extends Model
{
    use HasFactory, HasUuids; // Aktifkan fitur auto-UUID Laravel

    protected $fillable = [
        'nis',
        'name',
        'birth_place',
        'birth_date',
        'address',
        'guardian_name',
        'guardian_phone',
        'rombel',
        'tingkat',
        'status',
        'balance'
    ];
}
