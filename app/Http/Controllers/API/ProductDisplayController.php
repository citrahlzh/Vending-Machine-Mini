<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\ProductDisplay;
use App\Models\Cell;
use App\Models\Price;
use App\Http\Resources\ProductDisplayResource;

class ProductDisplayController extends Controller
{
    public function index()
    {
        $productDisplays = ProductDisplay::all();

        return ProductDisplayResource::collection($productDisplays);
    }

    public function store(Request $request)
    {
        $now = now();

        $validator = $request->validate([
            'product_id' => 'required|exists:products,id',
            'price_id' => [
                'required',
                Rule::exists('prices', 'id')->where(function ($query) use ($request, $now) {
                    $query->where('product_id', $request->input('product_id'))
                        ->where('is_active', true)
                        ->where('start_date', '<=', $now)
                        ->where('end_date', '>=', $now);
                }),
            ],
            'cell_id' => 'required|exists:cells,id',
            'is_empty' => 'sometimes|boolean',
            'status' => 'sometimes|in:active,inactive,discontinued',
        ]);

        $productDisplay = ProductDisplay::create([
            'user_id' => $request->user()->id,
            'product_id' => $validator['product_id'],
            'price_id' => $validator['price_id'],
            'cell_id' => $validator['cell_id'],
            'is_empty' => $validator['is_empty'] ?? false,
            'status' => $validator['status'] ?? 'active',
        ]);

        return response()->json([
            'data' => new ProductDisplayResource($productDisplay),
            'message' => 'Data penataan produk berhasil ditambahkan.',
        ], 201);
    }

    public function show($id)
    {
        $productDisplay = ProductDisplay::findOrFail($id);

        return new ProductDisplayResource($productDisplay);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $productDisplay = ProductDisplay::findOrFail($id);
        $now = now();
        $targetProductId = (int) $request->input('product_id', $productDisplay->product_id);

        $validator = Validator::make($request->all(), [
            'product_id' => 'sometimes|required|exists:products,id',
            'price_id' => [
                'sometimes',
                'required',
                Rule::exists('prices', 'id')->where(function ($query) use ($targetProductId, $now) {
                    $query->where('product_id', $targetProductId)
                        ->where('is_active', true)
                        ->where('start_date', '<=', $now)
                        ->where('end_date', '>=', $now);
                }),
            ],
            'cell_id' => 'sometimes|required|exists:cells,id',
            'is_empty' => 'sometimes|boolean',
            'status' => 'sometimes|in:active,inactive,discontinued',
        ]);

        $validator->after(function ($validator) use ($request, $productDisplay, $targetProductId, $now) {
            if (!$request->has('product_id') || $request->has('price_id')) {
                return;
            }

            $isCurrentPriceValid = Price::query()
                ->where('id', $productDisplay->price_id)
                ->where('product_id', $targetProductId)
                ->where('is_active', true)
                ->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->exists();

            if (!$isCurrentPriceValid) {
                $validator->errors()->add('price_id', 'Pilih harga aktif yang sesuai dengan produk.');
            }
        });

        $validated = $validator->validate();
        $productDisplay->update($validated);

        return response()->json([
            'data' => new ProductDisplayResource($productDisplay),
            'message' => 'Data penataan produk berhasil diperbarui.',
        ]);
    }

    public function restock(Request $request, $id)
    {
        $validated = $request->validate([
            'qty_add' => 'required|integer|min:1',
        ]);

        $result = DB::transaction(function () use ($id, $validated) {
            $productDisplay = ProductDisplay::where('id', $id)->lockForUpdate()->firstOrFail();
            $cell = Cell::where('id', $productDisplay->cell_id)->lockForUpdate()->firstOrFail();

            $availableSpace = max(0, (int) $cell->capacity - (int) $cell->qty_current);
            if ($availableSpace <= 0) {
                return [
                    'error' => 'Sel sudah penuh.',
                    'product_display' => $productDisplay,
                    'cell' => $cell,
                    'requested_qty' => (int) $validated['qty_add'],
                    'actual_added' => 0,
                    'available_space' => 0,
                ];
            }

            $actualAdded = min((int) $validated['qty_add'], $availableSpace);
            $cell->qty_current = (int) $cell->qty_current + $actualAdded;
            $cell->save();

            if ($cell->qty_current > 0 && $productDisplay->is_empty) {
                $productDisplay->is_empty = false;
                $productDisplay->save();
            }

            return [
                'error' => null,
                'product_display' => $productDisplay->refresh(),
                'cell' => $cell->refresh(),
                'requested_qty' => (int) $validated['qty_add'],
                'actual_added' => $actualAdded,
                'available_space' => $availableSpace,
            ];
        });

        if ($result['error']) {
            return response()->json([
                'data' => new ProductDisplayResource($result['product_display']),
                'message' => $result['error'],
                'cell' => $result['cell'],
                'requested_qty' => $result['requested_qty'],
                'actual_added' => $result['actual_added'],
                'available_space' => $result['available_space'],
            ], 422);
        }

        return response()->json([
            'data' => new ProductDisplayResource($result['product_display']),
            'message' => 'Stok berhasil diperbarui.',
            'cell' => $result['cell'],
            'requested_qty' => $result['requested_qty'],
            'actual_added' => $result['actual_added'],
            'available_space' => $result['available_space'],
        ]);
    }

    public function stockOut(Request $request, $id)
    {
        $validated = $request->validate([
            'qty_out' => 'required|integer|min:1',
        ]);

        $result = DB::transaction(function () use ($id, $validated) {
            $productDisplay = ProductDisplay::where('id', $id)->lockForUpdate()->firstOrFail();
            $cell = Cell::where('id', $productDisplay->cell_id)->lockForUpdate()->firstOrFail();

            $availableStock = max(0, (int) $cell->qty_current);
            if ($availableStock <= 0) {
                return [
                    'error' => 'Stok di sel sudah habis.',
                    'product_display' => $productDisplay,
                    'cell' => $cell,
                    'requested_qty' => (int) $validated['qty_out'],
                    'actual_out' => 0,
                    'available_stock' => 0,
                ];
            }

            $actualOut = min((int) $validated['qty_out'], $availableStock);
            $cell->qty_current = (int) $cell->qty_current - $actualOut;
            $cell->save();

            if ($cell->qty_current <= 0 && !$productDisplay->is_empty) {
                $productDisplay->is_empty = true;
                $productDisplay->save();
            }

            return [
                'error' => null,
                'product_display' => $productDisplay->refresh(),
                'cell' => $cell->refresh(),
                'requested_qty' => (int) $validated['qty_out'],
                'actual_out' => $actualOut,
                'available_stock' => $availableStock,
            ];
        });

        if ($result['error']) {
            return response()->json([
                'data' => new ProductDisplayResource($result['product_display']),
                'message' => $result['error'],
                'cell' => $result['cell'],
                'requested_qty' => $result['requested_qty'],
                'actual_out' => $result['actual_out'],
                'available_stock' => $result['available_stock'],
            ], 422);
        }

        return response()->json([
            'data' => new ProductDisplayResource($result['product_display']),
            'message' => 'Stock out berhasil diproses.',
            'cell' => $result['cell'],
            'requested_qty' => $result['requested_qty'],
            'actual_out' => $result['actual_out'],
            'available_stock' => $result['available_stock'],
        ]);
    }

    public function destroy($id)
    {
        $productDisplay = ProductDisplay::findOrFail($id);
        $productDisplay->delete();

        return response()->json([
            'message' => 'Data penataan produk berhasil dihapus.',
        ]);
    }
}

