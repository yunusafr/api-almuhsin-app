<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class InvoiceItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'invoice_id',
        'type',
        'description',
        'amount',
    ];

    // Relasi balik ke tabel invoices (Item ini masuk di nota tagihan yang mana?)
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
