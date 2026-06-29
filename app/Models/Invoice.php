<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Invoice extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'student_id',
        'invoice_number',
        'total_amount',
        'paid_amount',
        'status',
        'due_date',
    ];

    // Relasi ke tabel students (Milik siapa tagihan ini?)
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Relasi ke tabel invoice_items (Apa saja rincian tagihannya?)
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
