<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use App\Models\PackagingType;
use App\Models\PackagingSize;
use App\Models\Price;
use App\Models\ProductDisplay;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'brand_id',
        'packaging_type_id',
        'packaging_size_id',
        'product_name',
        'image_url'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function brand() {
        return $this->belongsTo(Brand::class);
    }

    public function packagingType() {
        return $this->belongsTo(PackagingType::class);
    }

    public function packagingSize() {
        return $this->belongsTo(PackagingSize::class);
    }

    public function prices() {
        return $this->hasMany(Price::class);
    }

    public function productDisplays() {
        return $this->hasMany(ProductDisplay::class);
    }
}
