<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PackagingType;

class PackagingTypeController extends Controller
{
    public function index()
    {
        $packagingTypes = PackagingType::with('user')->latest()->get();

        return view('dashboard.master-data.packaging-types.index', compact('packagingTypes'));
    }
}
