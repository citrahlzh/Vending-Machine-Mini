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
            'qty_current' => 'required|integer|min:0',
            'capacity' => 'required|integer|min:1',
        ]);

        $cell = Cell::create($validator);

        return response()->json([
            'data' => new CellResource($cell),
            'message' => 'Cell created successfully.',
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
            'message' => 'Cell updated successfully.',
        ], 200);
    }

    public function destroy($id)
    {
        $cell = Cell::findOrFail($id);
        $cell->delete();

        return response()->json([
            'message' => 'Cell deleted successfully.',
        ], 200);
    }
}
