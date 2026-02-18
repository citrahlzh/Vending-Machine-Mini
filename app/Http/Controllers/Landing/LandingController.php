<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\ProductDisplay;

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
                ];
            })
            ->values();

        return view('landing.index', compact('ads', 'products', 'callCenterPhone', 'callCenterWhatsapp'));
    }
}
