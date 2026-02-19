<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::with('user')
            ->withCount('products')
            ->latest()
            ->get();

        return view('dashboard.master-data.brands.index', compact('brands'));
    }
}
