<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();

        return view('dashboard.master-data.users.index', compact('users'));
    }

    public function create()
    {
        return view('dashboard.master-data.users.create');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('dashboard.master-data.users.edit', compact('user'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        return view('dashboard.master-data.users.show', compact('user'));
    }
}
