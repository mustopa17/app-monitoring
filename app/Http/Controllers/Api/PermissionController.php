<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * GET /api/permissions
     * Ambil semua permission yang tersedia
     */
    public function index()
    {
        abort_if(! auth()->user()->can('role.view'), 403, 'Anda tidak memiliki izin untuk melihat daftar permission.');
        $permissions = Permission::select('id', 'name', 'label', 'description')->get()->map(function ($p) {
            return [
                'key' => $p->name,
                'label' => $p->label ?? ucwords(str_replace('.', ' ', $p->name)),
                'description' => $p->description ?? '',
            ];
        });

        return response()->json(['data' => $permissions]);
    }
}
