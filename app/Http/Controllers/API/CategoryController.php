<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        return CategoryResource::collection($categories);
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'category_name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $category = Category::create([
            'user_id' => $request->user()->id,
            'category_name' => $validator['category_name'],
            'is_active' => $validator['is_active'],
        ]);

        return response()->json([
            'data' => new CategoryResource($category),
            'message' => 'Kategori berhasil ditambahkan.',
        ], 201);
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);

        return new CategoryResource($category);
    }

    public function edit($id)
    {
        // Logic to edit a specific category
    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'category_name' => 'sometimes|required|string|max:255',
            'is_active' => 'sometimes|required|boolean',
        ]);

        $category = Category::findOrFail($id);
        $category->update($validator);

        return response()->json([
            'data' => new CategoryResource($category),
            'message' => 'Kategori berhasil diperbarui.',
        ], 200);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        $category->delete();

        return response()->json([
            'message' => 'Kategori berhasil dihapus.'
        ]);
    }
}

