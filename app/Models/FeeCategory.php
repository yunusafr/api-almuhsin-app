<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class FeeCategory extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'invoice_type', 'default_amount', 'default_description'];
}
