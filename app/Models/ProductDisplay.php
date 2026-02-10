<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Product;
use App\Models\Price;
use App\Models\Cell;
use App\Models\SaleLine;

class ProductDisplay extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'product_id',
        'price_id',
        'cell_id',
        'is_empty',
        'status'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function price() {
        return $this->belongsTo(Price::class);
    }

    public function cell() {
        return $this->belongsTo(Cell::class);
    }

    public function salesLines() {
        return $this->hasMany(SaleLine::class);
    }
}
