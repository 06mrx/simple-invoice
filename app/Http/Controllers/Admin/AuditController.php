<?php

namespace App\Http\Controllers\Admin;

use App\Models\Audit;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AuditController extends Controller
{
    public function __construct()
    {
        // Hanya user dengan permission "view-audits" yang bisa akses index
        $this->middleware('permission:view-audit')->only(['index', 'show']);

    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $query = Audit::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('event', 'like', "%$search%")
                    ->orWhere('auditable_type', 'like', "%$search%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    });
            });
        }

        if ($request->filled('event')) {
            $query->where('event', $request->input('event'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        $audits = $query->paginate($perPage);

        return Inertia::render('Admin/Audit/Index', [
            'audits' => $audits,
            'filters' => [
                'search' => $request->input('search', ''),
                'event' => $request->input('event', ''),
                'user_id' => $request->input('user_id', ''),
                'per_page' => $perPage,
            ],
        ]);
    }

    public function show(Audit $audit)
    {
        return Inertia::render('Admin/Audit/Show', [
            'audit' => $audit,
        ]);
    }
}
