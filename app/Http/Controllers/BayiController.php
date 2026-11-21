<?php

namespace App\Http\Controllers;

use App\Models\Bayi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class BayiController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-bayi')->only(['index']);
        $this->middleware('permission:create-bayi')->only(['create', 'store']);
        $this->middleware('permission:edit-bayi')->only(['edit', 'update']);
        $this->middleware('permission:delete-bayi')->only(['destroy']);
    }

    public function index(): Response
    {
        $perPage = request('per_page', 10);
        $validPerPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 10;

        $query = Bayi::withTrashed();

        if (request()->has('search') && request('search') != '') {
            $search = request('search');
            $query->where('nama', 'like', "%{$search}%")
                ->orWhere('nik', 'like', "%{$search}%")
                ->orWhere('nama_ortu', 'like', "%{$search}%");
        }

        $bayis = $query->paginate($validPerPage)->withQueryString();

        return Inertia::render('Bayi/Index', [
            'bayis' => $bayis,
            'filters' => [
                'search' => request('search', ''),
                'per_page' => $validPerPage,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Bayi/Create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|unique:tb_bayi,nik',
            'tgl_lahir' => 'required|date',
            'jk' => 'required|in:L,P',
            'nama_ortu' => 'required|string|max:255',
            'bb' => 'nullable|numeric',
            'tb' => 'nullable|numeric',
            'll' => 'nullable|numeric',
            'lk' => 'nullable|numeric',
            'ket' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Bayi::create([
            'id' => \Str::uuid(),
            'nama' => $request->nama,
            'nik' => $request->nik,
            'tgl_lahir' => $request->tgl_lahir,
            'jk' => $request->jk,
            'nama_ortu' => $request->nama_ortu,
            'bb' => $request->bb,
            'tb' => $request->tb,
            'll' => $request->ll,
            'lk' => $request->lk,
            'ket' => $request->ket,
        ]);

        return redirect()->route('bayi.index')
            ->with('success', 'Data bayi berhasil ditambahkan.');
    }

    public function edit(Bayi $bayi): Response
    {
        return Inertia::render('Bayi/Edit', [
            'bayi' => $bayi,
        ]);
    }

    public function update(Request $request, Bayi $bayi)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|unique:tb_bayi,nik,'.$bayi->id,
            'tgl_lahir' => 'required|date',
            'jk' => 'required|in:L,P',
            'nama_ortu' => 'required|string|max:255',
            'bb' => 'nullable|numeric',
            'tb' => 'nullable|numeric',
            'll' => 'nullable|numeric',
            'lk' => 'nullable|numeric',
            'ket' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $bayi->update($request->only([
            'nama',
            'nik',
            'tgl_lahir',
            'jk',
            'nama_ortu',
            'bb',
            'tb',
            'll',
            'lk',
            'ket',
        ]));

        return redirect()->route('bayi.index')
            ->with('success', 'Data bayi berhasil diperbarui.');
    }

    public function destroy(Bayi $bayi)
    {
        $bayi->delete();

        return redirect()->route('bayi.index')
            ->with('success', 'Data bayi berhasil dihapus (soft delete).');
    }

    public function restore($id)
    {
        $bayi = Bayi::withTrashed()->findOrFail($id);
        $bayi->restore();

        return redirect()->route('bayi.index')
            ->with('success', 'Data bayi berhasil dipulihkan.');
    }
}
