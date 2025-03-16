<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_type_id',
        'serial_number',
        'is_sold',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_sold' => 'boolean',
    ];

    /**
     * Get the product type that owns the item.
     */
    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    /**
     * Update the product type's stock count when an item is created or updated.
     */
    protected static function booted()
    {
        static::saved(function ($item) {
            $item->productType->updateStocksCount();
        });

        static::deleted(function ($item) {
            $item->productType->updateStocksCount();
        });
    }
}
