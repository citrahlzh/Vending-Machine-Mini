<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AuditLog::query()
            ->with(['actor', 'subject'])
            ->when($request->filled('channel'), function ($query) use ($request) {
                $query->where('channel', $request->string('channel'));
            })
            ->when($request->filled('event'), function ($query) use ($request) {
                $query->where('event', 'like', '%' . $request->string('event') . '%');
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $channels = AuditLog::query()
            ->select('channel')
            ->distinct()
            ->orderBy('channel')
            ->pluck('channel');

        return view('dashboard.audit-logs.index', compact('logs', 'channels'));
    }

    public function show($id)
    {
        $log = AuditLog::with(['actor', 'subject'])->findOrFail($id);

        return view('dashboard.audit-logs.show', compact('log'));
    }
}
