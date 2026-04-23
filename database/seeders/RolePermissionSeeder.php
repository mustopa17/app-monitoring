<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan cache spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Hapus data lama agar tidak "acak"
        Schema::disableForeignKeyConstraints();
        Permission::truncate();
        Role::truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        Schema::enableForeignKeyConstraints();

        // 1. Definisikan semua permission dengan Label dan Deskripsi
        $permissions = [
            // Monitor
            ['name' => 'monitor.view',   'label' => 'Lihat Monitor',    'description' => 'Melihat daftar website yang dimonitor'],
            ['name' => 'monitor.create', 'label' => 'Tambah Monitor',  'description' => 'Menambahkan website baru untuk dimonitor'],
            ['name' => 'monitor.edit',   'label' => 'Edit Monitor',    'description' => 'Mengubah data website (URL, Nama, Interval)'],
            ['name' => 'monitor.delete', 'label' => 'Hapus Monitor',   'description' => 'Menghapus website dari daftar monitoring'],

            // Logs
            ['name' => 'log.view',   'label' => 'Lihat Log',    'description' => 'Melihat riwayat aktivitas dan insiden status'],
            ['name' => 'log.export', 'label' => 'Ekspor Log',   'description' => 'Mengunduh data riwayat log ke format CSV'],
            ['name' => 'log.clear',  'label' => 'Bersihkan Log', 'description' => 'Menghapus data riwayat aktivitas/log'],

            // User
            ['name' => 'user.view',   'label' => 'Lihat User',    'description' => 'Melihat daftar pengguna sistem'],
            ['name' => 'user.create', 'label' => 'Tambah User',   'description' => 'Mendaftarkan pengguna baru'],
            ['name' => 'user.edit',   'label' => 'Edit User',     'description' => 'Mengubah profil atau role pengguna lain'],
            ['name' => 'user.delete', 'label' => 'Hapus User',    'description' => 'Menghapus akun pengguna dari sistem'],

            // Role
            ['name' => 'role.view',   'label' => 'Lihat Role',    'description' => 'Melihat daftar role dan hak aksesnya'],
            ['name' => 'role.create', 'label' => 'Tambah Role',   'description' => 'Membuat role baru dengan set izin tertentu'],
            ['name' => 'role.edit',   'label' => 'Edit Role',     'description' => 'Mengatur ulang permission pada suatu role'],
            ['name' => 'role.delete', 'label' => 'Hapus Role',    'description' => 'Menghapus role yang tidak digunakan'],
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                ['label' => $perm['label'], 'description' => $perm['description']]
            );
        }

        // 2. Buat role super_admin → dapat semua permission
        $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ], ['description' => 'Akses penuh ke semua fitur sistem']);
        $superAdmin->syncPermissions(Permission::all());

        // 3. Buat role admin → akses monitor & logs
        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ], ['description' => 'Akses monitor dan activity logs']);
        $admin->syncPermissions([
            'monitor.view',
            'monitor.create',
            'monitor.edit',
            'monitor.delete',
            'log.view',
            'log.export',
        ]);

        // 4. Buat role user → hanya lihat dashboard
        $user = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web',
        ], ['description' => 'Akses hanya melihat dashboard']);
        $user->syncPermissions([
            'monitor.view',
            'log.view',
        ]);

        // 5. Berikan Role ke User yang sudah ada agar menu muncul kembali
        $mustopa = User::where('email', 'superadmin@com')->first();
        if ($mustopa) {
            $mustopa->assignRole('super_admin');
        }

        $admin1User = User::where('email', 'admin1@gmail.com')->first();
        if ($admin1User) {
            $admin1User->assignRole('admin');
        }

        $user1 = User::where('email', 'user1@gmail.com')->first();
        if ($user1) {
            $user1->assignRole('user');
        }

        echo "Roles dan Permissions berhasil di-seed dan ditugaskan ke user!\n";
    }
}
