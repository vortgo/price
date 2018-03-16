<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Price
 *
 * @property int $id
 * @property int $product_id
 * @property int $shop_id
 * @property float $price
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\Shop $shop
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Price whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Price whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Price wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Price whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Price whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Price whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
