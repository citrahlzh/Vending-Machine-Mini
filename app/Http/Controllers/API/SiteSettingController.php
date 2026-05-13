<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SiteSettingResource;
use App\Models\Machine;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SiteSettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::all()
            ->reject(fn (SiteSetting $setting) => in_array($setting->key, [
                'machine_name',
                'machine_code',
                'machine_serial_number',
                'machine_location',
                'machine_operator_name',
            ], true))
            ->groupBy('group')
            ->map(function ($items) {
                return SiteSettingResource::collection($items);
            });

        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $machine = Machine::query()->latest('id')->first() ?? new Machine();
        $inputSettings = $request->input('settings', []);
        $fileSettings = $request->file('settings', []);
        $keys = array_unique([
            ...array_keys($inputSettings),
            ...array_keys($fileSettings),
        ]);

        foreach ($keys as $key) {
            $value = $inputSettings[$key] ?? null;

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

        $validatedMachine = $request->validate([
            'machine.name' => ['required', 'string', 'max:255'],
            'machine.code' => [
                'required',
                'string',
                'size:3',
                'regex:/^[A-Z]{3}$/',
                Rule::unique('machines', 'code')->ignore($machine->id),
            ],
            'machine.serial_number' => ['nullable', 'string', 'max:255'],
            'machine.location' => ['nullable', 'string', 'max:255'],
            'machine.operator_name' => ['nullable', 'string', 'max:255'],
            'machine.category' => ['nullable', 'string', 'max:255'],
            'machine.size' => ['nullable', 'string', 'max:255'],
            'machine.is_android' => ['required', 'boolean'],
            'machine.status' => ['required', Rule::in(['active', 'inactive'])],
            'machine.condition_status' => ['required', Rule::in(['good', 'maintenance', 'damaged'])],
            'machine.photo_url' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:4096'],
        ], [
            'machine.code.regex' => 'Kode mesin harus terdiri dari 3 huruf kapital.',
            'machine.code.size' => 'Kode mesin harus tepat 3 huruf.',
        ]);

        $machinePayload = $validatedMachine['machine'] ?? [];
        $machinePayload['code'] = strtoupper($machinePayload['code']);

        if ($request->hasFile('machine.photo_url')) {
            $machinePayload['photo_url'] = $request->file('machine.photo_url')->store('machines', 'public');
        } elseif (!$machine->exists) {
            $machinePayload['photo_url'] = null;
        }

        $machine->fill($machinePayload);
        $machine->save();

        cache()->forget('site_settings');
        cache()->forget('current_machine');

        return response()->json([
            'message' => 'Setelan situs dan identitas mesin berhasil diubah.'
        ]);
    }
}
