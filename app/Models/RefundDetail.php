<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundDetail extends Model
{
    protected $table = 'refund_details';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'refund_id',
        'product_id',
        'quantity',
        'price'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
