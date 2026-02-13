<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cell;
use App\Http\Resources\CellResource;

class CellController extends Controller
{
    public function index()
    {
        $cells = Cell::all();

        return CellResource::collection($cells);
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'row' => 'required|string|max:10',
            'column' => 'required|string|max:10',
            'code' => 'required|string|max:50|unique:cells,code',
            'qty_current' => 'sometimes|integer|min:0',
            'capacity' => 'required|integer|min:1',
        ]);

        $cell = Cell::create([
            ...$validator,
            'qty_current' => $validator['qty_current'] ?? 0,
        ]);

        return response()->json([
            'data' => new CellResource($cell),
            'message' => 'Sel berhasil ditambahkan.',
        ], 201);
    }

    public function show($id)
    {
        $cell = Cell::findOrFail($id);

        return new CellResource($cell);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'row' => 'sometimes|required|string|max:10',
            'column' => 'sometimes|required|string|max:10',
            'code' => 'sometimes|required|string|max:50|unique:cells,code,' . $id,
            'qty_current' => 'sometimes|required|integer|min:0',
            'capacity' => 'sometimes|required|integer|min:1',
        ]);

        $cell = Cell::findOrFail($id);
        $cell->update($validator);

        return response()->json([
            'data' => new CellResource($cell),
            'message' => 'Sel berhasil diperbarui.',
        ], 200);
    }

    public function destroy($id)
    {
        $cell = Cell::findOrFail($id);
        $cell->delete();

        return response()->json([
            'message' => 'Sel berhasil dihapus.',
        ], 200);
    }
}

