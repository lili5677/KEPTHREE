<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Protocol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatProposalController extends Controller
{
    public function index(Request $request)
    {
        $sekretariatId = auth()->id();

        $search = $request->get('search');
        $type = $request->get('type');

        $logs = DB::table('activity_logs')
            ->leftJoin('users', 'activity_logs.user_id', '=', 'users.id')
            ->join('protocols', function ($join) {
                $join->on('activity_logs.subject_id', '=', 'protocols.id')
                    ->where('activity_logs.subject_type', '=', Protocol::class);
            })
            ->where('activity_logs.subject_type', Protocol::class)
            ->where('protocols.sekretariat_id', $sekretariatId)
            ->when($type, function ($query) use ($type) {
                $query->where('activity_logs.type', $type);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('activity_logs.action', 'like', '%' . $search . '%')
                        ->orWhere('activity_logs.type', 'like', '%' . $search . '%')
                        ->orWhere('protocols.title', 'like', '%' . $search . '%')
                        ->orWhere('protocols.nomor_registrasi', 'like', '%' . $search . '%')
                        ->orWhere('users.name', 'like', '%' . $search . '%');
                });
            })
            ->select([
                'activity_logs.id',
                'activity_logs.type',
                'activity_logs.action',
                'activity_logs.subject_id',
                'activity_logs.created_at',
                'users.name as user_name',
                'users.email as user_email',
                'protocols.title as protocol_title',
                'protocols.nomor_registrasi as nomor_registrasi',
            ])
            ->orderByDesc('activity_logs.created_at')
            ->paginate(15)
            ->withQueryString();

        $typeOptions = DB::table('activity_logs')
            ->join('protocols', function ($join) {
                $join->on('activity_logs.subject_id', '=', 'protocols.id')
                    ->where('activity_logs.subject_type', '=', Protocol::class);
            })
            ->where('activity_logs.subject_type', Protocol::class)
            ->where('protocols.sekretariat_id', $sekretariatId)
            ->whereNotNull('activity_logs.type')
            ->distinct()
            ->orderBy('activity_logs.type')
            ->pluck('activity_logs.type');

        return view('sekretariat.riwayat.index', compact(
            'logs',
            'typeOptions',
            'search',
            'type'
        ));
    }
}