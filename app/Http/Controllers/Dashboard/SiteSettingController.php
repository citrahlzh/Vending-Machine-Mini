<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteSetting;

class SiteSettingController extends Controller
{
    public function index() {
        $settings = SiteSetting::all()->groupBy('group');

        return view('dashboard.site-settings.index', compact('settings'));
    }
}
