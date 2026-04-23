<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        abort_if(! auth()->user()->can('user.view'), 403, 'Anda tidak memiliki izin untuk melihat manajemen user.');
        $users = User::all()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first(),
            ];
        });

        if ($users->isEmpty()) {
            return response()->json([
                'error' => 'Data tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'data' => $users,
        ], 200);
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'error' => 'Data tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'nama' => $user->name,
            'email' => $user->email,
            'role' => $user->getRoleNames()->first(),
        ], 200);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        abort_if(! auth()->user()->can('user.create'), 403, 'Anda tidak memiliki izin untuk menambah user.');
        // Simple validation as per issue requirement
        if (! $request->name || ! $request->email || ! $request->password || ! $request->role) {
            return response()->json([
                'error' => 'Tidak boleh kosong',
            ], 400);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Assign role ke sistem Spatie
            $user->syncRoles([$request->role]);

            return response()->json([
                'data' => 'OK',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        abort_if(! auth()->user()->can('user.edit'), 403, 'Anda tidak memiliki izin untuk mengubah user.');
        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'error' => 'Data tidak ditemukan',
            ], 404);
        }

        $dataToUpdate = $request->only(['name', 'email']);

        if ($request->password) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        try {
            $user->update($dataToUpdate);

            // Sync role ke sistem Spatie jika role berubah
            if ($request->has('role')) {
                $user->syncRoles([$request->role]);
            }

            return response()->json([
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->getRoleNames()->first(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        abort_if(! auth()->user()->can('user.delete'), 403, 'Anda tidak memiliki izin untuk menghapus user.');
        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'error' => 'Data tidak ditemukan',
            ], 404);
        }

        try {
            $user->delete();

            return response()->json([
                'data' => 'OK',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
