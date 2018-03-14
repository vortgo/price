<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'product_id',
        'filename',
    ];

    public function product()
    {
        return $this->hasOne(Product::class,'id', 'product_id');
    }
}
