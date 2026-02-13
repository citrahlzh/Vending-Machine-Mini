<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ad;

class AdController extends Controller
{
    public function index()
    {
        $ads = Ad::latest()->get();

        return view('dashboard.master-data.ads.index', compact('ads'));
    }
}
