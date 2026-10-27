<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    protected $fillable = [
        'product_id',
        'currency_id',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
