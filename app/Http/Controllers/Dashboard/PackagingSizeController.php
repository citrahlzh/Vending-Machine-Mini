<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PackagingSize;

class PackagingSizeController extends Controller
{
    public function index()
    {
        $packagingSizes = PackagingSize::with('user')
            ->withCount('products')
            ->latest()
            ->get();

        return view('dashboard.master-data.packaging-sizes.index', compact('packagingSizes'));
    }
}
