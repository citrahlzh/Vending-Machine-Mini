<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use App\Models\Price;
use App\Http\Resources\PriceResource;

class PriceController extends Controller
{
    public function index()
    {
        Price::deactivateOutsideRange();

        $prices = Price::all();

        return PriceResource::collection($prices);
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'product_id' => 'required|exists:products,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'price' => 'required|numeric',
            'is_active' => 'sometimes|boolean',
        ]);

        $now = now();
        $startDate = Carbon::parse($validator['start_date']);
        $endDate = Carbon::parse($validator['end_date']);
        $isInRange = $startDate->lessThanOrEqualTo($now) && $endDate->greaterThanOrEqualTo($now);
        $isActive = (bool) ($validator['is_active'] ?? true) && $isInRange;

        if ($isActive) {
            $hasActivePrice = Price::query()
                ->where('product_id', $validator['product_id'])
                ->where('is_active', true)
                ->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->exists();

            if ($hasActivePrice) {
                throw ValidationException::withMessages([
                    'product_id' => 'Produk ini sudah memiliki harga aktif.',
                ]);
            }
        }

        $price = Price::create([
            'user_id' => $request->user()->id,
            'product_id' => $validator['product_id'],
            'start_date' => $validator['start_date'],
            'end_date' => $validator['end_date'],
            'price' => $validator['price'],
            'is_active' => $isActive,
        ]);

        return response()->json([
            'data' => new PriceResource($price),
            'message' => 'Harga berhasil ditambahkan.',
        ], 201);
    }

    public function show($id)
    {
        $price = Price::findOrFail($id);

        return new PriceResource($price);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'product_id' => 'sometimes|required|exists:products,id',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'price' => 'sometimes|required|numeric',
            'is_active' => 'sometimes|required|boolean'
        ]);

        $price = Price::findOrFail($id);
        $payload = $validator;
        $now = now();
        $startDate = array_key_exists('start_date', $payload) ? Carbon::parse($payload['start_date']) : $price->start_date;
        $endDate = array_key_exists('end_date', $payload) ? Carbon::parse($payload['end_date']) : $price->end_date;
        $isInRange = $startDate->lessThanOrEqualTo($now) && $endDate->greaterThanOrEqualTo($now);

        if (array_key_exists('is_active', $payload)) {
            $payload['is_active'] = (bool) $payload['is_active'] && $isInRange;
        } elseif (array_key_exists('start_date', $payload) || array_key_exists('end_date', $payload)) {
            if (!$isInRange) {
                $payload['is_active'] = false;
            }
        }

        $price->update($payload);

        return response()->json([
            'data' => new PriceResource($price),
            'message' => 'Harga berhasil diperbarui.',
        ]);
    }

    public function destroy($id)
    {
        $price = Price::findOrFail($id);
        $price->delete();

        return response()->json([
            'message' => 'Harga berhasil dihapus.',
        ]);
    }
}

