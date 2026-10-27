<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'name',
        'symbol',
        'exchange_rate',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:4',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function productPrices()
    {
        return $this->hasMany(ProductPrice::class);
    }
}
