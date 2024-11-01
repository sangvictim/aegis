<?php

namespace App\Models;

use App\Traits\CreateUpdatedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory, HasUuids, CreateUpdatedBy;

    protected $table = 'refunds';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'refund_number',
        'total_price',
        'total_items',
        'created_by',
        'updated_by',
    ];

    public function products()
    {
        return $this->hasMany(RefundDetail::class);
    }
}
