<?php

namespace App\Models;

use App\Traits\CreateUpdatedAt;
use App\Traits\CreateUpdatedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    use HasFactory, HasUuids, CreateUpdatedBy, CreateUpdatedAt;

    protected $table = 'sales_orders';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'invoice_number',
        'total_price',
        'total_items',
        'created_by',
        'updated_by',
    ];


    public function products()
    {
        return $this->hasMany(SalesOrderDetail::class);
    }
}
