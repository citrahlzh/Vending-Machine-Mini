<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->latest()->get();

        return response()->json([
            'data' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:roles,slug',
            'description' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $slug = $validator['slug'] ?? '';
        if (!$slug) {
            $slug = Str::slug($validator['name']);
        }

        $baseSlug = $slug;
        $counter = 1;
        while (Role::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $role = Role::create([
            'name' => $validator['name'],
            'slug' => $slug,
            'description' => $validator['description'] ?? null,
            'is_active' => $validator['is_active'],
        ]);

        return response()->json([
            'data' => $role,
            'message' => 'Role berhasil ditambahkan.',
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validator = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
                Rule::unique('roles', 'slug')->ignore($role->id),
            ],
            'description' => 'sometimes|nullable|string|max:255',
            'is_active' => 'sometimes|required|boolean',
        ]);

        if (array_key_exists('slug', $validator) && $validator['slug']) {
            $validator['slug'] = Str::slug($validator['slug']);
        }

        $role->update($validator);

        return response()->json([
            'data' => $role,
            'message' => 'Role berhasil diperbarui.',
        ]);
    }

    public function destroy($id)
    {
        $role = Role::withCount('users')->findOrFail($id);

        if ($role->users_count > 0) {
            return response()->json([
                'message' => 'Role masih digunakan oleh pengguna.',
            ], 422);
        }

        $role->delete();

        return response()->json([
            'message' => 'Role berhasil dihapus.',
        ]);
    }
}
