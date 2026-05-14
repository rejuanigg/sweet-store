<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    public function categories():BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function stocks():HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function images():HasMany
    {
        return $this->hasMany(Image::class);
    }

    public function califications():HasMany
    {
        return $this->hasMany(Calification::class);
    }

    public function orderDetails():HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    protected $fillable = [
        'name',
        'description',
        'price',
        'is_featured',
        'featured_order',
    ];
}
