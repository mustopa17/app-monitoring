<div align="center">

# 🖥️ MonitorHub
### Sistem Monitoring Website Otomatis

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-00758F?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpinedotjs&logoColor=black)](https://alpinejs.dev)

> **MonitorHub** adalah aplikasi web yang memantau status website secara otomatis.  
> Tahu lebih cepat kalau website kamu lagi **mati** sebelum pengguna yang lapor duluan.

</div>

---

## 📖 Daftar Isi

1. [Apa Itu Aplikasi Ini?](#-apa-itu-aplikasi-ini)
2. [Teknologi yang Digunakan](#%EF%B8%8F-teknologi-yang-digunakan)
3. [Cara Install & Menjalankan](#-cara-install--menjalankan)
4. [Cara Menggunakan Aplikasi](#-cara-menggunakan-aplikasi)
5. [Penjelasan Sistem Role & Permission](#-penjelasan-sistem-role--permission)
6. [Penjelasan Teknis untuk Developer](#-penjelasan-teknis-untuk-developer)
7. [Struktur Folder](#%EF%B8%8F-struktur-folder)
8. [Alur Kerja Sistem](#-alur-kerja-sistem)
9. [API Endpoint](#-api-endpoint)
10. [FAQ](#-faq)

---

## 🎯 Apa Itu Aplikasi Ini?

Bayangkan kamu punya 10 website. Kamu tidak mungkin buka satu-satu setiap jam untuk memastikan semuanya aktif. Di sinilah **MonitorHub** berperan.

**MonitorHub** akan:
- ✅ Memantau website-website kamu **secara otomatis** setiap beberapa menit
- 🔴 Mendeteksi kalau ada website yang **DOWN (mati)**
- 📋 Mencatat **riwayat lengkap** kapan website naik/turun
- 📊 Menampilkan **waktu respons** (seberapa cepat website merespons)
- 👥 Mendukung **banyak pengguna** dengan hak akses yang berbeda-beda
- 📥 Bisa **ekspor laporan** ke format CSV

### Siapa yang Cocok Menggunakan Ini?
- Tim IT yang mengelola banyak website
- Developer yang ingin memantau server-nya
- Perusahaan yang ingin tahu uptime website mereka

---

## 🛠️ Teknologi yang Digunakan

Ini adalah daftar "bahan-bahan" yang dipakai untuk membangun aplikasi ini. Kalau kamu orang awam, anggap saja ini seperti daftar peralatan dapur.

| Teknologi | Fungsi Sederhananya |
|---|---|
| **PHP 8.x** | Bahasa utama yang dipakai untuk logika di server |
| **Laravel 11** | "Kerangka kerja" PHP — seperti template yang sudah jadi, tinggal pakai |
| **MySQL** | Tempat menyimpan semua data (website, user, log, dll) |
| **Laravel Sanctum** | Sistem keamanan untuk login dan autentikasi |
| **Spatie Permission** | Library untuk mengatur siapa boleh melakukan apa |
| **Alpine.js** | JavaScript ringan untuk membuat tampilan web menjadi interaktif |
| **Tailwind CSS** | Library untuk mempercantik tampilan web |
| **Blade** | "Bahasa template" untuk membuat halaman HTML |
| **XAMPP** | Paket server lokal (PHP + MySQL + Apache) untuk development |

---

## 🚀 Cara Install & Menjalankan

### Prasyarat
Pastikan sudah terinstall:
- [XAMPP](https://www.apachefriends.org/) (sudah termasuk PHP dan MySQL)
- [Composer](https://getcomposer.org/) (package manager untuk PHP)
- [Git](https://git-scm.com/)

### Langkah 1: Clone Repository
```bash
git clone https://github.com/mustopa17/app-monitoring.git
cd app-monitoring
```

### Langkah 2: Install Dependensi
```bash
composer install
```

### Langkah 3: Konfigurasi Environment
```bash
# Salin file konfigurasi
cp .env.example .env

# Buat kunci enkripsi
php artisan key:generate
```

Buka file `.env` dan sesuaikan konfigurasi database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=app_monitoring   # Nama database kamu
DB_USERNAME=root             # Username MySQL
DB_PASSWORD=                 # Password MySQL (kosong jika default XAMPP)
```

### Langkah 4: Buat Database & Isi Data Awal
```bash
# Jalankan migrasi (buat semua tabel)
php artisan migrate

# Isi data awal (role, permission, dan user default)
php artisan db:seed --class=RolePermissionSeeder
```

### Langkah 5: Jalankan Server
```bash
php artisan serve
```

Buka browser dan akses: **http://localhost:8000**

### Akun Default Setelah Seeding

| Email | Password | Role |
|---|---|---|
| `superadmin@com` | *(lihat di seeder)* | Super Admin |
| `admin1@gmail.com` | *(lihat di seeder)* | Admin |
| `user1@gmail.com` | *(lihat di seeder)* | User |

---

## 📱 Cara Menggunakan Aplikasi

### 1. Login
Masukkan email dan password. Sistem akan mengarahkan kamu ke Dashboard sesuai role.

### 2. Dashboard
Tampilan utama berisi ringkasan:
- **Total** website yang dipantau
- **Healthy** — website yang aktif
- **Down** — website yang mati
- **Avg Response** — rata-rata waktu respons

### 3. Menambah Website untuk Dipantau
Klik menu **Websites** → **Add New Target** → isi:
- **Friendly Name**: nama mudah diingat (contoh: "Website Toko Saya")
- **Target URL**: alamat website (contoh: `google.com`)
- **Check Interval**: seberapa sering dicek, dalam menit (contoh: `5`)

### 4. Melihat Log Aktivitas
Klik menu **Activity Logs** untuk melihat riwayat lengkap semua pengecekan. Bisa juga:
- **Export CSV**: download laporan
- **Clear History**: hapus semua riwayat

### 5. User Management *(hanya Super Admin)*
Klik menu **User Management** untuk mengelola akun pengguna.

### 6. Role Management *(hanya Super Admin)*
Klik menu **Role Management** untuk mengatur hak akses tiap role.

---

## 🔐 Penjelasan Sistem Role & Permission

Ini adalah fitur yang mengatur **siapa boleh melakukan apa** di dalam aplikasi.

### Analogi Sederhana

Bayangkan sebuah kantor:
- 🏢 **Super Admin** = Direktur Utama → bisa akses semua ruangan dan melakukan semua hal
- 👔 **Admin** = Manager → bisa masuk sebagian ruangan, tapi tidak bisa ubah struktur organisasi
- 👤 **User** = Karyawan Biasa → hanya bisa masuk ruang kerja sendiri, tidak bisa ke ruang direksi

### Tiga Level Role

```
┌─────────────────────────────────────────────────────────┐
│  SUPER ADMIN                                            │
│  ✅ Semua fitur (16 permission)                         │
│  ✅ Kelola User, Role, Monitor, Log                     │
├─────────────────────────────────────────────────────────┤
│  ADMIN                                                  │
│  ✅ Kelola Monitor (tambah/edit/hapus website)          │
│  ✅ Lihat & Export Log                                  │
│  ❌ Tidak bisa kelola User & Role                       │
├─────────────────────────────────────────────────────────┤
│  USER                                                   │
│  ✅ Hanya lihat Dashboard & Monitor                     │
│  ✅ Hanya lihat Log                                     │
│  ❌ Tidak bisa tambah/edit/hapus apapun                 │
└─────────────────────────────────────────────────────────┘
```

### Daftar Lengkap 16 Permission

Permission adalah **satu aksi spesifik** yang bisa diizinkan atau dilarang.

| Kategori | Permission | Label | Keterangan |
|---|---|---|---|
| 🖥️ Monitor | `monitor.view` | Lihat Monitor | Melihat daftar website yang dimonitor |
| 🖥️ Monitor | `monitor.create` | Tambah Monitor | Menambahkan website baru |
| 🖥️ Monitor | `monitor.edit` | Edit Monitor | Mengubah data website |
| 🖥️ Monitor | `monitor.delete` | Hapus Monitor | Menghapus website dari daftar |
| 📋 Log | `log.view` | Lihat Log | Melihat riwayat aktivitas |
| 📋 Log | `log.export` | Ekspor Log | Download data ke CSV |
| 📋 Log | `log.clear` | Bersihkan Log | Menghapus semua riwayat |
| 👥 User | `user.view` | Lihat User | Melihat daftar pengguna |
| 👥 User | `user.create` | Tambah User | Mendaftarkan pengguna baru |
| 👥 User | `user.edit` | Edit User | Mengubah data pengguna |
| 👥 User | `user.delete` | Hapus User | Menghapus akun pengguna |
| 🎭 Role | `role.view` | Lihat Role | Melihat daftar role |
| 🎭 Role | `role.create` | Tambah Role | Membuat role baru |
| 🎭 Role | `role.edit` | Edit Role | Mengubah permission suatu role |
| 🎭 Role | `role.delete` | Hapus Role | Menghapus role |

### Cara Membuat Role Kustom

Kamu bisa membuat role sendiri! Misalnya role **"Viewer Logs Only"** yang hanya bisa lihat log saja.

1. Buka menu **Role Management**
2. Klik **Tambah Role**
3. Isi nama role dan deskripsi
4. Centang hanya permission `log.view`
5. Simpan

Setelah itu, assign role tersebut ke user yang kamu inginkan melalui menu **User Management**.

---

## 🔧 Penjelasan Teknis untuk Developer

*Bagian ini untuk programmer yang ingin memahami cara kerja di balik layar.*

### Sistem Autentikasi: Laravel Sanctum

Aplikasi ini menggunakan **API Token** untuk autentikasi, bukan session biasa. Ini dipilih karena arsitekturnya berbasis **SPA (Single Page Application)** — frontend dan backend berkomunikasi via JSON.

**Alur Login:**
```
1. User kirim POST /api/login dengan email & password
2. Laravel verifikasi kredensial
3. Kalau benar → buat token unik, kirim ke frontend
4. Frontend simpan token di localStorage
5. Setiap request berikutnya → kirim token di Header
   Authorization: Bearer <token>
6. Laravel cek token → kalau valid → proses request
```

**Di kode:**
```php
// AuthController.php — saat login berhasil
$token = $user->createToken('auth_token')->plainTextToken;
return response()->json(['token' => $token, 'user' => $user]);
```

```javascript
// Di frontend (Alpine.js) — setelah login
localStorage.setItem('auth_token', data.token);

// Setiap fetch request
headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
```

---

### Sistem Role & Permission: Spatie Laravel Permission

**Spatie** adalah package PHP yang membuat sistem Role & Permission menjadi mudah.

**Cara install (sudah dilakukan):**
```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

**Spatie membuat 5 tabel di database secara otomatis:**

```
permissions          → Daftar semua permission
roles                → Daftar semua role
role_has_permissions → Hubungan: role ini punya permission apa?
model_has_roles      → Hubungan: user ini punya role apa?
model_has_permissions→ Permission langsung ke user (opsional)
```

**Method-method penting Spatie:**

```php
// Membuat permission
Permission::create(['name' => 'monitor.view']);

// Membuat role
$role = Role::create(['name' => 'admin']);

// Assign permission ke role
$role->syncPermissions(['monitor.view', 'monitor.create']);

// Assign role ke user
$user->assignRole('admin');

// Cek apakah user punya permission
$user->can('monitor.view'); // true / false

// Ambil semua permission user (termasuk dari role)
$user->getAllPermissions()->pluck('name');
```

---

### Sistem Middleware (3 Lapis Keamanan)

Setiap request yang masuk melewati tiga lapis pemeriksaan:

```
REQUEST MASUK
     │
     ▼
┌────────────────────────────┐
│  LAPIS 1: auth:sanctum     │ ── Sudah login? Punya token?
│                            │    TIDAK → 401 Unauthorized
└────────────┬───────────────┘
             │ ✅ Ya
             ▼
┌────────────────────────────┐
│  LAPIS 2: role:super_admin │ ── Punya role yang diperlukan?
│  (middleware di route)     │    TIDAK → 403 Forbidden
└────────────┬───────────────┘
             │ ✅ Ya
             ▼
┌────────────────────────────┐
│  LAPIS 3: abort_if         │ ── Punya permission spesifik?
│  (di dalam controller)     │    TIDAK → 403 Forbidden
└────────────┬───────────────┘
             │ ✅ Ya
             ▼
       PROSES & KIRIM RESPONSE ✅
```

**Contoh di kode:**

```php
// routes/api.php — Lapis 1 & 2
Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
    });
});

// UserController.php — Lapis 3
public function index()
{
    abort_if(!auth()->user()->can('user.view'), 403, 'Tidak punya izin');
    // ... proses selanjutnya
}
```

---

### Frontend: Alpine.js

Alpine.js dipakai untuk membuat tampilan menjadi **reaktif** (berubah otomatis) tanpa reload halaman, seperti React/Vue tapi jauh lebih ringan.

**Cara Alpine.js mengetahui permission user:**

```javascript
// 1. Saat pertama load, ambil data user dari API
const res = await fetch('api/user', { headers });
const data = await res.json();
// data.permissions = ['monitor.view', 'log.view', ...]

this.profile = data; // simpan ke state Alpine

// 2. Helper function untuk cek permission
hasPermission(permission) {
    if (!this.profile?.permissions) return false;
    return this.profile.permissions.includes(permission);
},

// 3. Pakai di HTML untuk tampil/sembunyi elemen
// x-show = tampilkan elemen HANYA kalau kondisinya true
```

```html
<!-- Tombol ini HANYA muncul jika user punya permission monitor.create -->
<button x-show="hasPermission('monitor.create')">
    Tambah Monitor
</button>

<!-- Menu ini HANYA muncul jika user punya permission role.view -->
<button x-show="hasPermission('role.view')">
    Role Management
</button>
```

---

### Seeder: Mengisi Data Awal

Seeder adalah script untuk **mengisi database secara otomatis**. Berguna untuk setup awal atau reset data.

```bash
# Jalankan seeder
php artisan db:seed --class=RolePermissionSeeder

# Reset cache permission jika ada masalah
php artisan permission:cache-reset
```

**Urutan yang dilakukan seeder:**
1. Bersihkan cache Spatie
2. Hapus (truncate) semua data role & permission lama
3. Buat 16 permission baru dengan label & deskripsi
4. Buat role `super_admin` → assign semua permission
5. Buat role `admin` → assign permission monitor & log
6. Buat role `user` → assign hanya permission view
7. Assign role ke user yang sudah ada di database

---

## 🗂️ Struktur Folder

```
app-monitoring/
│
├── 📁 app/
│   ├── 📁 Http/Controllers/Api/
│   │   ├── AuthController.php        ← Login & Logout
│   │   ├── MonitorController.php     ← CRUD website monitor
│   │   ├── MonitorLogController.php  ← Lihat, hapus, ekspor log
│   │   ├── UserController.php        ← CRUD pengguna
│   │   ├── RoleController.php        ← CRUD role
│   │   └── PermissionController.php  ← Ambil daftar permission
│   └── 📁 Models/
│       ├── User.php       ← Model pengguna
│       └── Monitor.php    ← Model website yang dipantau
│
├── 📁 database/
│   ├── 📁 migrations/    ← Blueprint/skema tabel database
│   └── 📁 seeders/
│       └── RolePermissionSeeder.php  ← Data awal role & permission
│
├── 📁 routes/
│   └── api.php           ← Semua endpoint/alamat API
│
├── 📁 resources/views/
│   └── welcome.blade.php ← File utama tampilan (SPA - satu halaman)
│
├── 📄 PENJELASAN.md      ← Dokumentasi teknis mendalam
├── 📄 README.md          ← File ini
└── 📄 .env               ← Konfigurasi (database, dll) — jangan di-commit!
```

---

## 🔄 Alur Kerja Sistem

### Alur Login

```
User buka browser
        ↓
Masukkan email & password
        ↓
Alpine.js kirim POST ke /api/login
        ↓
Laravel cek kredensial di database
        ↓
    ┌───┴───┐
 Salah    Benar
    ↓        ↓
Tampilkan  Buat token unik
error      Kirim token ke browser
           Token disimpan di localStorage
                ↓
           Masuk ke Dashboard
```

### Alur Monitoring Otomatis

```
Scheduler Laravel berjalan setiap menit
        ↓
Ambil semua website dari database
        ↓
Kirim HTTP request ke setiap website
        ↓
Ukur waktu respons
        ↓
Catat hasil ke tabel monitor_logs
(status: UP/DOWN, response_time, dll)
        ↓
Update status di tabel monitors
        ↓
Dashboard menampilkan data terbaru
```

---

## 🌐 API Endpoint

Semua URL yang tersedia untuk komunikasi antara frontend dan backend:

### Publik (Tidak Perlu Login)
| Method | Endpoint | Fungsi |
|---|---|---|
| `POST` | `/api/login` | Login dan dapatkan token |

### Perlu Login (Semua Role)
| Method | Endpoint | Fungsi |
|---|---|---|
| `POST` | `/api/logout` | Logout dan hapus token |
| `GET` | `/api/user` | Ambil data user + permissions |
| `GET` | `/api/monitors` | Lihat semua website |
| `GET` | `/api/logs` | Lihat semua log |
| `GET` | `/api/status` | Status ringkasan dashboard |

### Hanya Admin & Super Admin
| Method | Endpoint | Fungsi |
|---|---|---|
| `POST` | `/api/monitors` | Tambah website baru |
| `PUT` | `/api/monitors/{id}` | Edit website |
| `DELETE` | `/api/monitors/{id}` | Hapus website |
| `DELETE` | `/api/logs/{id}` | Hapus satu log |
| `DELETE` | `/api/logs/clear` | Hapus semua log |
| `GET` | `/api/logs/export` | Download log CSV |

### Hanya Super Admin
| Method | Endpoint | Fungsi |
|---|---|---|
| `GET` | `/api/users` | Lihat semua user |
| `POST` | `/api/users` | Tambah user baru |
| `PUT` | `/api/users/{id}` | Edit user |
| `DELETE` | `/api/users/{id}` | Hapus user |
| `GET` | `/api/roles` | Lihat semua role |
| `POST` | `/api/roles` | Buat role baru |
| `PUT` | `/api/roles/{id}` | Edit role |
| `DELETE` | `/api/roles/{id}` | Hapus role |
| `GET` | `/api/permissions` | Lihat semua permission |

---

## ❓ FAQ

**Q: Kenapa menu di sidebar tidak muncul setelah saya login?**  
A: Kemungkinan akun Anda tidak memiliki role. Jalankan seeder ulang:
```bash
php artisan db:seed --class=RolePermissionSeeder
```

**Q: Kenapa permission tidak berubah padahal sudah di-seed?**  
A: Spatie menyimpan permission di cache. Reset dengan:
```bash
php artisan permission:cache-reset
```

**Q: Bagaimana cara menambah fitur baru dengan permission?**  
1. Tambah permission baru di `RolePermissionSeeder.php`
2. Tambah ke role yang sesuai
3. Jalankan `php artisan db:seed --class=RolePermissionSeeder`
4. Di Controller: `abort_if(!auth()->user()->can('fitur.aksi'), 403, '...');`
5. Di frontend: `x-show="hasPermission('fitur.aksi')"`

**Q: Bagaimana kalau website yang dipantau butuh login?**  
A: Saat ini sistem hanya melakukan pengecekan HTTP sederhana (cek apakah website bisa diakses). Website yang butuh login tetap bisa dipantau, karena yang dicek hanya apakah server-nya merespons.

**Q: Apa bedanya `assignRole` dan `syncPermissions`?**  
- `assignRole('admin')` → **Tambahkan** role ke user (tidak hapus role lain)  
- `syncPermissions([...])` → **Ganti total** semua permission role dengan yang baru

**Q: Apakah bisa dipakai di production (server online)?**  
A: Bisa. Tapi perlu konfigurasi tambahan:
- Set `APP_ENV=production` dan `APP_DEBUG=false` di `.env`
- Gunakan web server seperti Nginx atau Apache
- Atur cron job untuk menjalankan `php artisan schedule:run` tiap menit

---

## 📝 Catatan untuk Developer Selanjutnya

Jika kamu mengambil alih proyek ini, berikut hal yang perlu diketahui:

> ⚠️ **PENTING**: Jangan pernah commit file `.env` ke GitHub karena berisi password database!

> 💡 **TIP**: Setiap kali menambah permission baru, selalu jalankan `php artisan permission:cache-reset` setelahnya.

> 📌 **KONVENSI**: Nama permission menggunakan format `kategori.aksi` (contoh: `monitor.view`, `user.create`). Patuhi konvensi ini agar kode tetap rapi.

> 🔒 **KEAMANAN**: Jangan pernah hapus pengecekan `abort_if` di controller. Itu adalah lapis keamanan terakhir.

---

## 👨‍💻 Kontribusi

1. Fork repository ini
2. Buat branch baru: `git checkout -b fitur/nama-fitur`
3. Commit perubahan: `git commit -m "Tambah fitur: nama-fitur"`
4. Push ke branch: `git push origin fitur/nama-fitur`
5. Buat Pull Request

---

<div align="center">

Dibuat dengan ❤️ menggunakan Laravel & Alpine.js

</div>
