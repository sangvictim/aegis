<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderDetail extends Model
{
    protected $table = 'sales_order_details';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'sales_order_id',
        'product_id',
        'quantity',
        'price',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
