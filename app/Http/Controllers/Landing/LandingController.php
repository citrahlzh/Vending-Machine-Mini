<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\ProductDisplay;
use Illuminate\Support\Str;

class LandingController extends Controller
{
    public function index()
    {
        $callCenterPhone = (string) config('app.call_center_phone', '0812-0000-0000');
        $callCenterWhatsapp = (string) config('app.call_center_whatsapp', $callCenterPhone);

        $ads = Ad::query()
            ->where('status', 'active')
            ->orderBy('id')
            ->get();
        $products = ProductDisplay::with([
            'product:id,product_name,image_url',
            'price:id,price,is_active',
            'cell:id,qty_current',
        ])
            ->whereHas('product')
            ->whereHas('price', function ($query) {
                $query->where('is_active', true);
            })
            ->whereHas('cell')
            ->where('status', 'active')
            ->orderBy('id')
            ->get()
            ->map(function (ProductDisplay $display) {
                $stock = $display->is_empty ? 0 : max(0, (int) optional($display->cell)->qty_current);
                $imagePath = $display->product->image_url ?? null;

                return [
                    'id' => (string) $display->id,
                    'name' => $display->product->product_name ?? 'Produk',
                    'price' => (int) ($display->price->price ?? 0),
                    'stock' => $stock,
                    'image' => $imagePath ? asset('/image/' . ltrim($imagePath, '/')) : '',
                    'detail_url' => route('landing.product', ['productDisplay' => $display->id]),
                ];
            })
            ->values();

        return view('landing.index', compact('ads', 'products', 'callCenterPhone', 'callCenterWhatsapp'));
    }

    public function product(string $productDisplay)
    {
        $display = ProductDisplay::with([
            'product:id,product_name,image_url,brand_id,category_id,packaging_type_id,packaging_size_id',
            'product.brand:id,brand_name',
            'product.category:id,category_name',
            'product.packagingType:id,packaging_type',
            'product.packagingSize:id,size',
            'price:id,price,is_active',
            'cell:id,qty_current',
        ])
            ->whereHas('product')
            ->whereHas('price', function ($query) {
                $query->where('is_active', true);
            })
            ->whereHas('cell')
            ->where('status', 'active')
            ->findOrFail($productDisplay);

        $stock = $display->is_empty ? 0 : max(0, (int) optional($display->cell)->qty_current);
        $imagePath = $display->product->image_url ?? null;
        $name = $display->product->product_name ?? 'Produk';

        $descriptionParts = array_filter([
            optional($display->product->brand)->brand_name,
            optional($display->product->category)->category_name,
            optional($display->product->packagingType)->packaging_type,
            optional($display->product->packagingSize)->size,
        ]);

        $product = [
            'id' => (string) $display->id,
            'name' => $name,
            'price' => (int) ($display->price->price ?? 0),
            'stock' => $stock,
            'image' => $imagePath ? asset('/image/' . ltrim($imagePath, '/')) : '',
            'description' => count($descriptionParts) > 0
                ? implode(' | ', $descriptionParts)
                : 'Deskripsi produk belum tersedia.',
            'short_name' => Str::limit($name, 40),
        ];

        return view('landing.product', compact('product'));
    }

    public function payment(string $saleId)
    {
        return view('landing.payment', [
            'saleId' => $saleId,
        ]);
    }
}
