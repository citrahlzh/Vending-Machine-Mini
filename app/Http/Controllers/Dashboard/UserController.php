<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->latest()->get();

        return view('dashboard.master-data.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::where('is_active', true)->orderBy('name')->get();

        return view('dashboard.master-data.users.create', compact('roles'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        $roles = Role::where('is_active', true)->orderBy('name')->get();

        return view('dashboard.master-data.users.edit', compact('user', 'roles'));
    }

    public function show($id)
    {
        $user = User::with('role')->findOrFail($id);

        return view('dashboard.master-data.users.show', compact('user'));
    }
}
