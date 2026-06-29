<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Payment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'invoice_id',
        'recorded_by',
        'payment_number',
        'amount',
        'payment_date',
        'payment_method',
        'reference_number',
        'notes'
    ];

    // Relasi balik ke Invoice
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Relasi ke User (Kasir/Admin)
    public function cashier()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
