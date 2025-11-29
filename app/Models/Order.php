<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['order_number', 'subtotal', 'discount', 'total', 'status', 'notes'];

    /**
     * Get the order items for this order.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
