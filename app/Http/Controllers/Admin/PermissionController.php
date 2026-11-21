<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        // Hanya user dengan permission "view-permissions" yang bisa akses index
        $this->middleware('permission:view-permission')->only(['index']);

        // Hanya user dengan permission "create-permission" yang bisa tambah user
        $this->middleware('permission:create-permission')->only(['create', 'store']);

        // Hanya user dengan permission "edit-permission" yang bisa edit user
        $this->middleware('permission:edit-permission')->only(['edit', 'update']);

        // Hanya user dengan permission "delete-permission" yang bisa hapus user
        $this->middleware('permission:delete-permission')->only(['destroy']);
    }

    public function index()
    {
        $perPage = request('per_page', 10);

        // Validasi nilai input (opsional tapi aman)
        $validPerPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 10;

        $query = Permission::query();

        if (request()->has('search') && request('search') != '') {
            $search = request('search');
            $query->where('name', 'like', "%{$search}%");
        }
        // dd($validPerPage);

        $permissions = $query->paginate($validPerPage)->withQueryString();

        return Inertia::render('Admin/Permissions/Index', [
            'permissions' => $permissions,
            'filters' => ['search' => request('search', ''), 'per_page' => $validPerPage],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Permissions/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);

        Permission::create(['name' => $request->name]);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission berhasil ditambahkan.');
    }

    public function edit(Permission $permission)
    {
        return Inertia::render('Admin/Permissions/Edit', [
            'permission' => $permission,
        ]);
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,'.$permission->id,
        ]);

        $permission->update(['name' => $request->name]);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission berhasil diperbarui.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission berhasil dihapus.');
    }
}
