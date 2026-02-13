<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ad;
use App\Http\Resources\AdResource;

class AdController extends Controller
{
    public function index()
    {
        $ads = Ad::all();

        return AdResource::collection($ads);
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'title' => 'required|string|max:255',
            'image_url' => 'required|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $image_url = null;
        if ($request->hasFile('image_url')) {
            $image_url = $request->file('image_url')->store('ads', 'public');
        }

        $ad = Ad::create([
            'title' => $validator['title'],
            'image_url' => $image_url,
            'status' => $validator['status'] ?? 'inactive',
        ]);

        return response()->json([
            'data' => new AdResource($ad),
            'message' => 'Iklan berhasil ditambahkan.',
        ], 201);
    }

    public function show($id)
    {
        $ad = Ad::findOrFail($id);

        return new AdResource($ad);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'image_url' => 'sometimes|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $ad = Ad::findOrFail($id);

        if ($request->hasFile('image_url')) {
            $image_url = $request->file('image_url')->store('ads', 'public');
            $ad->image_url = $image_url;
        }

        if (isset($validator['title'])) {
            $ad->title = $validator['title'];
        }

        if (isset($validator['status'])) {
            $ad->status = $validator['status'];
        }

        $ad->save();

        return response()->json([
            'data' => new AdResource($ad),
            'message' => 'Iklan berhasil diperbarui.',
        ]);
    }

    public function destroy($id)
    {
        $ad = Ad::findOrFail($id);
        $ad->delete();

        return response()->json([
            'message' => 'Iklan berhasil dihapus.',
        ]);
    }
}

