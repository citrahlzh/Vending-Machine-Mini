<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\SiteSetting;

class SiteSettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::all()->groupBy('group');
        $machine = Machine::query()->latest('id')->first();

        return view('dashboard.site-settings.index', compact('settings', 'machine'));
    }
}
