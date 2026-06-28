<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user.roles');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by type/aktivitas
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by periode (default: semua)
        $periode = $request->get('periode', 'semua');

        match ($periode) {
            'hari_ini'    => $query->whereDate('created_at', today()),
            'minggu_ini'  => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'bulan_ini'   => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
            default       => null, // 'semua' -> tidak ada filter tanggal
        };

        $logs  = $query->latest()->paginate(10)->withQueryString();
        $users = User::where('status', 'aktif')->orderBy('name')->get();

        return view('admin.activity-log.index', compact('logs', 'users', 'periode'));
    }
}