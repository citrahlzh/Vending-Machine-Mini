<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Sale::with('salesLines.productDisplay.product')
            ->latest('transaction_date')
            ->get();

        return view('dashboard.transactions.index', compact('transactions'));
    }

    public function show($id)
    {
        $sale = Sale::with([
            'salesLines.productDisplay.product',
            'salesLines.productDisplay.price',
        ])->findOrFail($id);

        $orderItems = $sale->salesLines
            ->groupBy('product_display_id')
            ->map(function ($lines) {
                $firstLine = $lines->first();
                $display = $firstLine?->productDisplay;
                $qty = $lines->count();
                $unitPrice = (int) ($display?->price?->price ?? 0);

                return [
                    'product_name' => $display?->product?->product_name ?? 'Produk tidak ditemukan',
                    'qty' => $qty,
                    'price' => $unitPrice,
                    'subtotal' => $unitPrice * $qty,
                ];
            })
            ->values();

        $orderTotal = (int) ($sale->total_amount ?: $orderItems->sum('subtotal'));

        return view('dashboard.transactions.show', compact('sale', 'orderItems', 'orderTotal'));
    }
}
