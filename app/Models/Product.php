<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'code',
        'name',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'products_categories');
    }

    public function images()
    {
        return $this->hasMany(Image::class,'product_id', 'id');
    }

    public function prices()
    {
        return $this->hasMany(Price::class, 'product_id', 'id');
    }
}
