<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return UserResource::collection($users);
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'is_active' => 'required|boolean',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string',
        ]);

        $user = User::create([
            'name' => $validator['name'],
            'phone_number' => $validator['phone_number'],
            'whatsapp_number' => $validator['whatsapp_number'] ?? null,
            'is_active' => $validator['is_active'],
            'username' => $validator['username'],
            'password' => bcrypt($validator['password']),
        ]);

        return response()->json([
            'data' => new UserResource($user),
            'message' => 'Pengguna berhasil ditambahkan.',
        ], 201);
    }
    
    public function show($id)
    {
        $user = User::findOrFail($id);

        return new UserResource($user);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone_number' => 'sometimes|required|string|max:20',
            'whatsapp_number' => 'sometimes|nullable|string|max:20',
            'is_active' => 'sometimes|required|boolean',
            'username' => 'sometimes|required|string|max:50|unique:users,username,' . $id,
            'password' => 'sometimes|required|string',
        ]);

        $user = User::findOrFail($id);
        if (isset($validator['password'])) {
            $validator['password'] = bcrypt($validator['password']);
        }
        $user->update($validator);

        return response()->json([
            'data' => new UserResource($user),
            'message' => 'Pengguna berhasil diperbarui.',
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'Pengguna berhasil dihapus.'
        ]);
    }
}

