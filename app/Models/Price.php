<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductDisplay;

class Price extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'product_id',
        'start_date',
        'end_date',
        'price',
        'is_active'
    ];

    protected $casts = [
        'start_date'=> 'datetime',
        'end_date' => 'datetime'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function productDisplays() {
        return $this->hasMany(ProductDisplay::class);
    }
}
