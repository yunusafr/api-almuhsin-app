<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'classes'; // Mengunci nama tabel agar tetap 'classes'
    protected $fillable = ['name', 'level'];

    public function enrollments()
    {
        return $this->hasMany(ClassEnrollment::class, 'class_id');
    }
}
