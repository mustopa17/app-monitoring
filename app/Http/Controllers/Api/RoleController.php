<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * GET /api/roles
     * Ambil semua role beserta permissions dan jumlah user
     */
    public function index()
    {
        abort_if(! auth()->user()->can('role.view'), 403, 'Anda tidak memiliki izin untuk melihat manajemen role.');
        $roles = Role::with('permissions')->get()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description ?? '',
                'user_count' => User::role($role->name)->count(),
                'permissions' => $role->permissions->pluck('name')->toArray(),
            ];
        });

        return response()->json(['data' => $roles]);
    }

    /**
     * POST /api/roles
     * Buat role baru
     */
    public function store(Request $request)
    {
        abort_if(! auth()->user()->can('role.create'), 403, 'Anda tidak memiliki izin untuk menambah role.');
        $request->validate([
            'name' => 'required|string|unique:roles,name|max:50',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create([
            'name' => strtolower(str_replace(' ', '_', $request->name)),
            'guard_name' => 'web',
            'description' => $request->description,
        ]);

        if ($request->has('permissions') && count($request->permissions) > 0) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json([
            'message' => 'Role berhasil dibuat',
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description,
                'user_count' => 0,
                'permissions' => $role->permissions->pluck('name')->toArray(),
            ],
        ], 201);
    }

    /**
     * PUT /api/roles/{id}
     * Update role: deskripsi dan permissions
     */
    public function update(Request $request, $id)
    {
        abort_if(! auth()->user()->can('role.edit'), 403, 'Anda tidak memiliki izin untuk mengubah role.');
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:50|unique:roles,name,'.$id,
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        // Proteksi: nama super_admin tidak boleh diubah
        if ($role->name !== 'super_admin' && $request->has('name')) {
            $role->name = strtolower(str_replace(' ', '_', $request->name));
        }

        if ($request->has('description')) {
            $role->description = $request->description;
        }

        $role->save();

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json([
            'message' => 'Role berhasil diperbarui',
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description,
                'user_count' => User::role($role->name)->count(),
                'permissions' => $role->fresh()->permissions->pluck('name')->toArray(),
            ],
        ]);
    }

    /**
     * DELETE /api/roles/{id}
     * Hapus role dengan validasi
     */
    public function destroy($id)
    {
        abort_if(! auth()->user()->can('role.delete'), 403, 'Anda tidak memiliki izin untuk menghapus role.');
        $role = Role::findOrFail($id);

        // Validasi 1: super_admin tidak boleh dihapus
        if ($role->name === 'super_admin') {
            return response()->json([
                'error' => 'Role super_admin tidak bisa dihapus karena bersifat sistem.',
            ], 403);
        }

        // Validasi 2: Role yang masih dipakai user tidak boleh dihapus
        $userCount = User::role($role->name)->count();
        if ($userCount > 0) {
            return response()->json([
                'error' => "Role \"{$role->name}\" tidak bisa dihapus karena masih digunakan oleh {$userCount} user.",
            ], 422);
        }

        $role->delete();

        return response()->json(['message' => 'Role berhasil dihapus']);
    }
}
