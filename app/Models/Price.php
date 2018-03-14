<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = [
        'product_id',
        'shop_id',
        'price',
    ];

    public function shop()
    {
        return $this->hasOne(Shop::class, 'id','shop_id');
    }

    public function product()
    {
        return $this->hasOne(Product::class,'id', 'product_id');
    }
}
