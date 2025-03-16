<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Services\S3Service;

class ProductType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'current_stocks',
        'image_path',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['image_url'];

    /**
     * Get the image URL attribute.
     *
     * @return string|null
     */
    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            $s3Service = app()->make(S3Service::class);
            return $s3Service->getUrl($this->image_path);
        }
        
        return null;
    }

    /**
     * Get the user that owns the product type.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the product type.
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Get the available (unsold) items count for this product type.
     */
    public function availableItemsCount()
    {
        return $this->items()->where('is_sold', false)->count();
    }

    /**
     * Update the current stocks count based on available items.
     */
    public function updateStocksCount()
    {
        $this->current_stocks = $this->availableItemsCount();
        $this->save();
    }
}
