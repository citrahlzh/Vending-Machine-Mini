<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Category;
use App\Models\Brand;
use App\Models\PackagingType;
use App\Models\PackagingSize;
use App\Models\Product;
use App\Models\Price;
use App\Models\ProductDisplay;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone_number',
        'whatsapp_number',
        'is_active',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function categories() {
        return $this->hasMany(Category::class);
    }

    public function brands() {
        return $this->hasMany(Brand::class);
    }

    public function packagingTypes() {
        return $this->hasMany(PackagingType::class);
    }

    public function packagingSizes() {
        return $this->hasMany(PackagingSize::class);
    }

    public function products() {
        return $this->hasMany(Product::class);
    }

    public function prices() {
        return $this->hasMany(related: Price::class);
    }

    public function productDisplays() {
        return $this->hasMany(ProductDisplay::class);
    }
}
