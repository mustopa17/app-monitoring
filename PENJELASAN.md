# 📚 PENJELASAN PROYEK: App Monitoring
> Dokumen ini menjelaskan logika, teknologi, dan cara kerja proyek `app-monitoring` secara menyeluruh. Cocok dibaca jika kamu masih baru atau ingin memahami lebih dalam.

---

## 🧠 Apa Fungsi Proyek Ini?

Proyek ini adalah **sistem monitoring website**. Fungsinya adalah:
- Memantau apakah website tertentu sedang **UP (aktif)** atau **DOWN (mati)**
- Mencatat riwayat pengecekan di **Log Activity**
- Mengatur **siapa yang bisa melakukan apa** (Role & Permission)
- Mengelola **user** yang bisa menggunakan sistem ini

---

## 🛠️ Teknologi yang Digunakan

| Teknologi | Versi | Fungsi |
|---|---|---|
| **PHP** | 8.x | Bahasa pemrograman utama backend |
| **Laravel** | 11.x | Framework PHP untuk backend & routing |
| **MySQL** | - | Database untuk menyimpan semua data |
| **Spatie Permission** | v7.x | Library untuk sistem Role & Permission |
| **Laravel Sanctum** | - | Sistem autentikasi berbasis token (API Token) |
| **Alpine.js** | - | JavaScript ringan untuk reaktivitas UI (seperti Vue mini) |
| **Tailwind CSS** | - | Framework CSS untuk tampilan |
| **Blade** | - | Template engine bawaan Laravel untuk HTML |

---

## 🗂️ Struktur Folder Penting

```
app-monitoring/
│
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Api/           ← Semua logika backend ada di sini
│   │           ├── AuthController.php       (Login, Logout)
│   │           ├── MonitorController.php    (CRUD Website Monitor)
│   │           ├── MonitorLogController.php (Lihat, Hapus, Export Log)
│   │           ├── UserController.php       (CRUD User)
│   │           ├── RoleController.php       (CRUD Role)
│   │           └── PermissionController.php (Daftar Permission)
│   └── Models/
│       ├── User.php      ← Model user (bisa punya Role)
│       └── Monitor.php   ← Model data website yang dimonitor
│
├── database/
│   ├── migrations/       ← Blueprint/skema tabel database
│   └── seeders/
│       └── RolePermissionSeeder.php  ← Data awal Role & Permission
│
├── routes/
│   └── api.php           ← Daftar semua endpoint/URL API
│
└── resources/
    └── views/
        └── welcome.blade.php  ← File utama tampilan frontend (SPA)
```

---

## 🔄 Alur Kerja Sistem (Dari Login sampai Aksi)

```
1. User membuka browser → masuk ke halaman login
2. User memasukkan email & password
3. Frontend (Alpine.js) mengirim request ke POST /api/login
4. Laravel Sanctum memverifikasi → membuat TOKEN unik
5. Token disimpan di localStorage browser
6. Setiap request berikutnya membawa token di header Authorization: Bearer <token>
7. Middleware auth:sanctum memeriksa token → kalau valid, lanjutkan
8. Controller memeriksa Permission user → kalau tidak punya izin, tolak (403)
9. Data dikembalikan ke frontend → Alpine.js menampilkan ke layar
```

---

## 🔐 Penjelasan Sistem Login: Laravel Sanctum

**Sanctum** adalah sistem autentikasi yang membuat **token** (semacam "kunci masuk") saat user login.

### Cara Kerjanya:
```php
// Di AuthController.php (login)
$token = $user->createToken('auth_token')->plainTextToken;
```
Ini membuat token unik, contoh: `1|abc123xyz...`

### Kenapa Pakai Token?
Karena aplikasi ini adalah **SPA (Single Page Application)**. Artinya frontend dan backend berkomunikasi lewat **API (JSON)**, bukan form HTML biasa. Token dipakai untuk membuktikan "saya sudah login" di setiap request.

```
Browser → Request + Token → Laravel (cek token) → Kirim data
```

### Di Frontend (Alpine.js):
```javascript
// Setelah login, token disimpan
localStorage.setItem('auth_token', data.token);

// Setiap fetch request, token dikirim di header
headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
```

---

## 🛡️ Penjelasan Sistem Role & Permission (Spatie)

Ini adalah bagian yang paling penting dan sering membingungkan. Mari kita pecah satu per satu.

### Apa itu Spatie Laravel Permission?

**Spatie** adalah sebuah **library (package)** buatan orang lain yang kita install ke dalam Laravel. Fungsinya adalah **mengatur siapa bisa melakukan apa** di dalam aplikasi.

Tanpa Spatie, kita harus membuat sistem ini sendiri dari nol. Dengan Spatie, semuanya sudah tersedia.

```bash
# Cara install (sudah dilakukan)
composer require spatie/laravel-permission
```

### Konsep Dasar: Role vs Permission

| Istilah | Artinya | Contoh |
|---|---|---|
| **Permission** | Satu aksi spesifik yang boleh dilakukan | `monitor.view`, `user.delete` |
| **Role** | Kumpulan beberapa Permission sekaligus | `admin` punya `monitor.view` + `monitor.create` |
| **User** | Orang yang diberi sebuah Role | Mustopa → `super_admin` |

**Analoginya:**
- Permission = Kunci lemari tertentu
- Role = Gantungan kunci yang berisi beberapa kunci
- User = Orang yang memegang gantungan kunci

### Tabel-Tabel yang Dibuat Spatie di Database

```
permissions          → Daftar semua permission yang ada
roles                → Daftar semua role yang ada
role_has_permissions → Tabel penghubung: role ini punya permission apa saja?
model_has_roles      → Tabel penghubung: user ini punya role apa?
model_has_permissions→ Permission langsung ke user (jarang dipakai)
```

### Daftar Permission di Proyek Ini

```
KATEGORI    | PERMISSION KEY    | LABEL           | KETERANGAN
------------|-------------------|-----------------|----------------------------------
Monitor     | monitor.view      | Lihat Monitor   | Melihat daftar website dimonitor
            | monitor.create    | Tambah Monitor  | Menambahkan website baru
            | monitor.edit      | Edit Monitor    | Mengubah data website
            | monitor.delete    | Hapus Monitor   | Menghapus website
------------|-------------------|-----------------|----------------------------------
Log         | log.view          | Lihat Log       | Melihat riwayat aktivitas
            | log.export        | Ekspor Log      | Download CSV
            | log.clear         | Bersihkan Log   | Hapus semua log
------------|-------------------|-----------------|----------------------------------
User        | user.view         | Lihat User      | Melihat daftar pengguna
            | user.create       | Tambah User     | Mendaftarkan pengguna baru
            | user.edit         | Edit User       | Mengubah data pengguna
            | user.delete       | Hapus User      | Menghapus pengguna
------------|-------------------|-----------------|----------------------------------
Role        | role.view         | Lihat Role      | Melihat daftar role
            | role.create       | Tambah Role     | Membuat role baru
            | role.edit         | Edit Role       | Mengubah permission role
            | role.delete       | Hapus Role      | Menghapus role
```

### Daftar Role & Akses Default

| Role | Permission yang Dimiliki |
|---|---|
| `super_admin` | **SEMUA** permission (16 permission) |
| `admin` | monitor.view/create/edit/delete + log.view/export |
| `user` | monitor.view + log.view (hanya lihat) |

---

## 🔒 Middleware: Penjaga Pintu API

**Middleware** adalah kode yang berjalan **sebelum** request sampai ke Controller. Fungsinya seperti penjaga pintu.

### Dua Lapis Keamanan di Proyek Ini

#### Lapis 1: Middleware `auth:sanctum` (Cek Login)
```php
// Di routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    // Semua route di sini butuh login
});
```
Artinya: Kalau belum login (tidak punya token), langsung ditolak. Tidak perlu sampai ke Controller.

#### Lapis 2: Middleware `role:nama_role` (Cek Role)
```php
// Di routes/api.php
Route::middleware('role:super_admin')->group(function () {
    Route::get('/users', ...);    // Hanya super_admin
    Route::get('/roles', ...);    // Hanya super_admin
});
```
Artinya: Sudah login tapi rolenya bukan `super_admin`? Tetap ditolak.

Middleware `role:` ini disediakan oleh **Spatie** secara otomatis setelah library diinstall.

#### Lapis 3: `abort_if` di dalam Controller (Cek Permission Granular)
```php
// Di MonitorController.php
public function store(Request $request)
{
    // Cek permission spesifik sebelum lanjut
    abort_if(!auth()->user()->can('monitor.create'), 403, 'Tidak punya izin');
    
    // Kalau lolos cek di atas, baru proses datanya
    Monitor::create($validated);
}
```
`abort_if` artinya: **hentikan sekarang jika** kondisinya benar.
- `!auth()->user()` → ambil user yang sedang login
- `->can('monitor.create')` → cek apakah user punya permission `monitor.create`
- Kalau TIDAK punya (`!`), kirim error 403 (Forbidden)

### Visualisasi Alur Keamanan

```
Request dari Browser
        │
        ▼
┌─────────────────────────┐
│ auth:sanctum            │ ← Sudah login? Ada token?
│ (Lapis 1)               │   TIDAK → tolak (401 Unauthorized)
└─────────┬───────────────┘
          │ Ya (sudah login)
          ▼
┌─────────────────────────┐
│ role:super_admin        │ ← Punya role yang benar?
│ (Lapis 2 - opsional)    │   TIDAK → tolak (403 Forbidden)
└─────────┬───────────────┘
          │ Ya (role benar)
          ▼
┌─────────────────────────┐
│ abort_if(!->can(...))   │ ← Punya permission spesifik?
│ (Lapis 3 - di Controller│   TIDAK → tolak (403 Forbidden)
└─────────┬───────────────┘
          │ Ya (izin ada)
          ▼
    Proses data & kirim response ✅
```

---

## 🌱 Seeder: Data Awal Permission & Role

**Seeder** adalah script PHP untuk mengisi database secara otomatis. Dijalankan dengan:
```bash
php artisan db:seed --class=RolePermissionSeeder
```

### Penjelasan Isi `RolePermissionSeeder.php`

```php
// LANGKAH 1: Bersihkan cache Spatie
// Spatie menyimpan Permission di cache agar cepat.
// Sebelum ubah data, cache harus dibersihkan dulu.
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

// LANGKAH 2: Hapus data lama (truncate)
// Truncate = hapus semua isi tabel, tapi tabel tetap ada
// Schema::disableForeignKeyConstraints() → matikan dulu pengecekan relasi
// supaya bisa hapus tabel yang saling berhubungan
Schema::disableForeignKeyConstraints();
Permission::truncate();       // Hapus semua permission
Role::truncate();             // Hapus semua role
DB::table('model_has_roles')->truncate(); // Hapus semua relasi user-role
Schema::enableForeignKeyConstraints(); // Hidupkan kembali pengecekan relasi

// LANGKAH 3: Buat permission baru
Permission::updateOrCreate(
    ['name' => 'monitor.view', 'guard_name' => 'web'], // Cari berdasarkan ini
    ['label' => 'Lihat Monitor', 'description' => '...'] // Update/isi dengan ini
);
// updateOrCreate = kalau sudah ada, update. Kalau belum ada, buat baru.

// LANGKAH 4: Buat role dan hubungkan ke permission
$superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
$superAdmin->syncPermissions(Permission::all());
// syncPermissions = selaraskan permission role ini dengan daftar yang diberikan.
// Permission::all() = semua permission yang ada
// Kalau ada permission lama yang tidak ada di list → dihapus otomatis

// LANGKAH 5: Assign role ke user yang sudah ada
$mustopa = User::where('email', 'superadmin@com')->first();
$mustopa->assignRole('super_admin');
// assignRole = berikan role ini ke user (ditambahkan ke tabel model_has_roles)
```

---

## 🖥️ Penjelasan Frontend (Alpine.js)

Alpine.js dipakai karena ringan dan tidak perlu build process seperti Vue/React.

### Cara Alpine.js Membaca Permission

Saat halaman pertama kali dimuat, Alpine.js memanggil `/api/user`:

```javascript
// Di welcome.blade.php
const res = await fetch('api/user', { headers });
const data = await res.json();
// data = { id, name, email, role, permissions: ['monitor.view', 'log.view', ...] }

this.profile = data; // Simpan ke state Alpine
```

Laravel sudah menyiapkan response ini di `routes/api.php`:
```php
Route::get('/user', function (Request $request) {
    $user = $request->user();
    return [
        'id'          => $user->id,
        'name'        => $user->name,
        'role'        => $user->getRoleNames()->first(),   // Nama role pertama
        'permissions' => $user->getAllPermissions()         // SEMUA permission
                              ->pluck('name')               // Ambil hanya nama
                              ->toArray(),                  // Ubah ke array PHP
    ];
});
```

### Helper `hasPermission()`

```javascript
hasPermission(permission) {
    // Kalau belum ada data profile, anggap tidak punya izin
    if (!this.profile || !this.profile.permissions) return false;
    
    // Cek apakah nama permission ada di dalam array permissions user
    return this.profile.permissions.includes(permission);
},
```

**Contoh:**
- `hasPermission('monitor.view')` → cek apakah `'monitor.view'` ada di array permissions
- Kalau user = `admin`, permissions = `['monitor.view', 'monitor.create', 'log.view']`
- `hasPermission('user.delete')` → `false` (tidak ada di array)

### Penggunaan di HTML

```html
<!-- Tombol hanya muncul kalau punya permission monitor.create -->
<button x-show="hasPermission('monitor.create')" @click="showAddModal = true">
    Tambah Monitor
</button>

<!-- Menu sidebar muncul kalau punya role.view -->
<button x-show="hasPermission('role.view')" @click="navigate('roles')">
    Role Management
</button>
```

`x-show` adalah direktif Alpine.js yang menyembunyikan/menampilkan elemen HTML berdasarkan kondisi Boolean (true/false).

---

## 🗄️ Penjelasan Tabel Database

### Tabel Buatan Sendiri

| Tabel | Isi |
|---|---|
| `users` | Data pengguna (nama, email, password) |
| `monitors` | Daftar website yang dimonitor (url, interval, status) |
| `monitor_logs` | Riwayat hasil pengecekan website |

### Tabel Buatan Spatie (Otomatis)

| Tabel | Isi |
|---|---|
| `permissions` | Daftar semua permission (`monitor.view`, dll) + `label` + `description` |
| `roles` | Daftar semua role (`super_admin`, `admin`, `user`) |
| `role_has_permissions` | Relasi: role X punya permission Y |
| `model_has_roles` | Relasi: user X punya role Y |
| `model_has_permissions` | Relasi: permission langsung ke user |

---

## ❓ FAQ (Pertanyaan yang Sering Muncul)

### Kenapa menu tiba-tiba hilang setelah db:seed?
Karena `truncate` pada `model_has_roles` menghapus relasi user↔role. Solusinya: pastikan Seeder selalu punya bagian "assign role ke user" di bagian akhir.

### Kenapa permission tidak berubah padahal sudah di-seed?
Spatie menggunakan cache. Jalankan:
```bash
php artisan permission:cache-reset
```

### Cara menambah fitur baru dengan permission?
1. Tambah permission baru di `RolePermissionSeeder.php`
2. Tambah ke role yang sesuai dengan `syncPermissions`
3. Jalankan `php artisan db:seed --class=RolePermissionSeeder`
4. Di Controller baru, tambahkan: `abort_if(!auth()->user()->can('fitur.aksi'), 403, '...');`
5. Di frontend, gunakan: `x-show="hasPermission('fitur.aksi')"`

### Apa bedanya `assignRole` vs `syncPermissions`?
- `assignRole('admin')` → **Tambahkan** role admin ke user ini (tidak hapus yang lain)
- `syncPermissions([...])` → **Ganti total** permission role ini dengan yang ada di list

---

## 📋 Ringkasan Alur Pengembangan

```
Tambah Fitur Baru
      │
      ▼
1. Buat Migration  →  php artisan make:migration
      │
      ▼
2. Buat Model      →  php artisan make:model
      │
      ▼
3. Buat Controller →  php artisan make:controller Api/NamaController
      │
      ▼
4. Tambah Route    →  routes/api.php (dengan middleware yang sesuai)
      │
      ▼
5. Tambah Permission → RolePermissionSeeder.php → php artisan db:seed
      │
      ▼
6. Update Frontend  →  welcome.blade.php (x-show, fetch API)
```
