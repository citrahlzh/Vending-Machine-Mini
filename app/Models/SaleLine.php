<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Sale;
use App\Models\ProductDisplay;

class SaleLine extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sales_lines';

    protected $fillable = [
        'sale_id',
        'product_display_id',
        'status'
    ];

    public function sale(){
        return $this->belongsTo(Sale::class);
    }

    public function productDisplay() {
        return $this->belongsTo(ProductDisplay::class);
    }
}
