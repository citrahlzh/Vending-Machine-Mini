<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cell;

class CellController extends Controller
{
    public function index()
    {
        $cells = Cell::latest()->get();

        return view('dashboard.master-data.cells.index', compact('cells'));
    }
}
