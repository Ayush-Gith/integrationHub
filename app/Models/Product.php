<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        "user",
        'integration_id',
        'external_product_id',
        'name',
        'sku',
        'price',
        'stock',
        'status',
        'platform',
        'raw_payload',
    ];

    protected $casts = [
        'raw_payload' => 'array', // automatically decode JSON
    ];

    public function integration()
    {
        return $this->belongsTo(Integration::class);
    }
}

