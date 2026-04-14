# Perencanaan Fitur: Tambah Aksi Edit dan Delete di Frontend SPA

## Tujuan
Menambahkan fungsionalitas Edit dan Delete untuk setiap data *monitor* pada antarmuka *Dashboard/Frontend* (di file `resources/views/welcome.blade.php`).

## 🎯 Konteks Sistem Saat Ini
Saat ini, aplikasi web menggunakan arsitektur *Single Page Application* (SPA) dengan teknologi:
- **Backend:** Laravel 11. API untuk *Update* (`PUT`) dan *Delete* (`DELETE`) sudah tersedia di `routes/api.php` tetapi belum dipanggil dari *frontend*.
- **Frontend:** Vanilla HTML, TailwindCSS (CDN), dan Alpine.js untuk manajemen state (tiada bundler JS). Saat ini *dashboard* sudah memiliki tombol untuk menambahkan monitor (`POST /api/monitors`) dan memuat daftar monitor dari (`GET /api/monitors`).

## 📌 Endpoint API yang Digunakan
*   **Edit Monitor:** `PUT /api/monitors/{id}`
    *   *Payload:* JSON object berisi `name`, `url`, `interval`
    *   *Fungsi:* Mengubah data monitor berdasarkan *ID*.
*   **Hapus Monitor:** `DELETE /api/monitors/{id}`
    *   *Fungsi:* Menghapus rekam data monitor permanen dari *database*.

---

## 🛠 Langkah-langkah Implementasi Frontend

Berikut adalah instruksi pengerjaan fitur yang bisa dilakukan secara bertahap pada file `resources/views/welcome.blade.php`:

### Tahap 1: Persiapan State Alpine.js (Edit)
1.  Buka komponen Alpine `x-data="dashboard()"` di dalam `<script>`.
2.  Tambahkan state variabel baru untuk menyimpan status modal dan form edit:
    *   `showEditModal: false`
    *   `editForm: { id: null, name: '', url: '', interval: '1' }`
3.  Tambahkan fungsi baru bernama `openEditModal(monitor)`:
    *   Fungsi ini dipanggil ketika tombol "Edit" diklik dengan parameter *object monitor* yang sedang dirender.
    *   Tugasnya mengisi `this.editForm` dengan data *monitor* (misalnya: `this.editForm.id = monitor.id`) dan mengubah status `this.showEditModal = true`.

### Tahap 2: Membuat Modal Form Edit (UI)
1.  Temukan modal HTML untuk fitur **"Add New Monitor"** yang sudah ada.
2.  Lakukan replikasi (*copy-paste*) struktur modal tersebut.
3.  Ubah `x-show=""` yang asalnya `showModal` (untuk *Add*) menjadi `showEditModal`.
4.  Pada bagian elemen form (`<input>`, `<select>`):
    *   Ubah *binding* `x-model="form.name"` menjadi `x-model="editForm.name"`.
    *   Lakukan hal yang sama untuk input `url` dan select `interval`.
5.  Ubah tombol simpan agar tidak memanggil event submit bawaan, dan pindahkan event click ke fungsi baru dengan: `@click.prevent="submitEdit()"`. Tombol `Cancel`-nya mengembalikan nilai `showEditModal` menjadi `false`.

### Tahap 3: Implementasi Fungsi Update Data via Fetch API
1.  Dalam blok script `x-data="dashboard()"`, buat fungsi `async submitEdit()`.
2.  Cek apakah *inputs* dari *form edit* sudah terisi semua (`name`, `url`, `interval`). Lontarkan fungsi `alert()` ringan bila kosong.
3.  Lakukan **HTTP Request (fetch)** ke url endpoint ➔ `http://127.0.0.1:8001/api/monitors/${this.editForm.id}` (atau menyesuaikan nama port lokal).
4.  Gunakan *method* HTTP `PUT`. Masukkan data yang relevan ke property `body: JSON.stringify({...})`. Gunakan Headers `Content-Type: application/json` serta `Accept: application/json`.
5.  Dapatkan *response*, tutup form dengan mengubah status ke *false* lalu segarkan kembali antarmuka dari *state* baru:
    *   Tutup modal: `this.showEditModal = false`.
    *   *(Opsional namun disarankan)*: panggil kembali fungsi `loadMonitors()` untuk memperbaharui tabel data.

### Tahap 4: Menambahkan Tombol Aksi di Tabel Monitor List
1.  Temukan tabel UI di mana "Monitor List" dirender menggunakan perulangan Alpine `<template x-for="monitor in monitors" :key="monitor.id">`.
2.  Pada tag *table data* kolom aksi  `<td>`, tambahkan dua tombol:
    *   Tombol "Edit" (contoh: ikon *pencil* atau teks)
    *   Tombol "Delete" (contoh: ikon *trash* dengan warna text merah `text-red-500`)
3.  Pada tombol **Edit**, ikat event memanggil `openEditModal()` yang baru dibuat: `@click="openEditModal(monitor)"`.
4.  Pada tombol **Delete**, ikat panggilan ke fungsi hapus, misal `@click="deleteMonitor(monitor.id)"`.

### Tahap 5: Implementasi Fungsi Delete via Fetch API
1.  Buat fungsi baru pada controller Alpine, bernama `async deleteMonitor(id)`.
2.  Panggil fungsi validasi UI interaktif agar pengguna dapat membatalkan: *prompt konfirmasi bawaan browser* menggunakan `if (!confirm('Are you sure you want to delete this monitor?')) return;`.
3.  Lakukan request untuk memanggil endpoint *backend endpoint hapus*: menggunakan *method* `DELETE` terarah kepada `http://127.0.0.1:8001/api/monitors/${id}` (atau port lokal).
4.  Bila berhasil dengan *response* success, perbarui tampilan daftar halaman dengan memanggil kembali metode muatan data `loadMonitors()`.

---

### Tips dan Catatan Penting
*   **Port Target API**: Pastikan bahwa konfigurasi localhost (cth: parameter `fetch('/api/monitors')` dipantau apakah berjalan di IP address `localhost:8000` atau `8001`) sama.
*   **Reactive**: Dengan menggunakan state `showEditModal`, struktur HTML *modal UI edit* hanya akan dirender ketika dibutuhkan tanpa melakuan transisi ulang di seluruh halaman. Semua berjalan asinkron dan otomatis memutakhirkan struktur pohon dokumen DOM.
