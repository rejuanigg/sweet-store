<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    public function product():BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function order():BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    protected $fillable = [
        'product_id',
        'order_id',
        'quantity',
        'price'
    ];
}
