<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProductDisplay;
use App\Models\Cell;
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
        $validator = $request->validate([
            'product_id' => 'required|exists:products,id',
            'price_id' => 'required|exists:prices,id',
            'cell_id' => 'required|exists:cells,id',
            'is_empty' => 'sometimes|boolean',
        ]);

        $productDisplay = ProductDisplay::create([
            // 'user_id' => auth()->id(),
            'user_id' => 1, // Temporary hardcoded user ID
            'product_id' => $validator['product_id'],
            'price_id' => $validator['price_id'],
            'cell_id' => $validator['cell_id'],
            'is_empty' => $validator['is_empty'] ?? false,
        ]);

        return response()->json([
            'data' => new ProductDisplayResource($productDisplay),
            'message' => 'Product display created successfully.',
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
        $validator = $request->validate([
            'product_id' => 'sometimes|required|exists:products,id',
            'price_id' => 'sometimes|required|exists:prices,id',
            'cell_id' => 'sometimes|required|exists:cells,id',
            'is_empty' => 'sometimes|boolean',
        ]);
    
        $productDisplay = ProductDisplay::findOrFail($id);
        $productDisplay->update($validator);

        return response()->json([
            'data' => new ProductDisplayResource($productDisplay),
            'message' => 'Product display updated successfully.',
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
                    'error' => 'Cell is already full.',
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
            'message' => 'Stock updated successfully.',
            'cell' => $result['cell'],
            'requested_qty' => $result['requested_qty'],
            'actual_added' => $result['actual_added'],
            'available_space' => $result['available_space'],
        ]);
    }

    public function destroy($id)
    {
        $productDisplay = ProductDisplay::findOrFail($id);
        $productDisplay->delete();

        return response()->json([
            'message' => 'Product display deleted successfully.',
        ]);
    }
}
