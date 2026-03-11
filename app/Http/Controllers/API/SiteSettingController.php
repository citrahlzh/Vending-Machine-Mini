<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteSetting;
use App\Http\Resources\SiteSettingResource;

class SiteSettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::all()
            ->groupBy('group')
            ->map(function ($items) {
                return SiteSettingResource::collection($items);
            });

        return response()->json($settings);
    }

    public function update(Request $request)
    {
        foreach ($request->settings as $key => $value) {

            if ($request->hasFile("settings.$key")) {

                $file = $request->file("settings.$key");
                $path = $file->store('settings', 'public');

                SiteSetting::where('key', $key)->update([
                    'value' => $path
                ]);

            } else {

                SiteSetting::where('key', $key)->update([
                    'value' => $value
                ]);

            }
        }

        return response()->json([
            'message' => 'Setelan situs berhasil diubah.'
        ]);
    }
}
