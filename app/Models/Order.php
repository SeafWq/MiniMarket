<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer',
        'warehouse_id',
        'status'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
