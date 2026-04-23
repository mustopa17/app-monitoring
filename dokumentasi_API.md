# 🚀 Dokumentasi API: MonitorHub
> Dokumentasi ini berisi panduan lengkap penggunaan API untuk aplikasi MonitorHub, termasuk autentikasi, manajemen monitor, log, user, dan role.

---

## 🔑 Autentikasi
MonitorHub menggunakan **Laravel Sanctum** (Bearer Token). Sebagian besar endpoint memerlukan token yang valid di dalam header.

**Base URL:** `http://localhost:8000/api`  
**Header Wajib (setelah login):**
```http
Authorization: Bearer <your_token>
Accept: application/json
Content-Type: application/json
```

---

## 🔐 1. Authentication Endpoints

### Login
Mendapatkan token akses.
- **URL:** `/login`
- **Method:** `POST`
- **Body:**
  ```json
  {
    "email": "admin@example.com",
    "password": "password"
  }
  ```
- **Response (200):**
  ```json
  {
    "token": "1|abc123xyz...",
    "user": { "id": 1, "name": "Admin", "email": "admin@example.com" }
  }
  ```

### Logout
Menghapus token akses saat ini.
- **URL:** `/logout`
- **Method:** `POST`

### Get Current User Profile
Melihat profil dan hak akses (permissions) user yang sedang login.
- **URL:** `/user`
- **Method:** `GET`
- **Response (200):**
  ```json
  {
    "id": 1,
    "name": "Super Admin",
    "email": "superadmin@com",
    "role": "super_admin",
    "permissions": ["monitor.view", "monitor.create", ...]
  }
  ```

---

## 🖥️ 2. Monitor Management
Dikelola oleh role: `admin`, `super_admin`.

### Get All Monitors
- **URL:** `/monitors`
- **Method:** `GET`
- **Permission:** `monitor.view`

### Create New Monitor
- **URL:** `/monitors`
- **Method:** `POST`
- **Permission:** `monitor.create`
- **Body:**
  ```json
  {
    "name": "Google",
    "url": "https://google.com",
    "interval": 5
  }
  ```

### Update Monitor
- **URL:** `/monitors/{id}`
- **Method:** `PUT`
- **Permission:** `monitor.edit`
- **Body:** Sama dengan Create (semua field bersifat opsional).

### Delete Monitor
- **URL:** `/monitors/{id}`
- **Method:** `DELETE`
- **Permission:** `monitor.delete`

---

## 📋 3. Activity Logs
Dikelola oleh role: `admin`, `super_admin`.

### Get All Logs
- **URL:** `/logs`
- **Method:** `GET`
- **Permission:** `log.view`

### Delete Single Log
- **URL:** `/logs/{id}`
- **Method:** `DELETE`
- **Permission:** `log.view` (Admin level)

### Clear All Logs
- **URL:** `/logs/clear`
- **Method:** `DELETE`
- **Permission:** `log.clear`

### Export Logs to CSV
- **URL:** `/logs/export`
- **Method:** `GET`
- **Permission:** `log.export`

---

## 👥 4. User Management
Hanya untuk role: `super_admin`.

### Get All Users
- **URL:** `/users`
- **Method:** `GET`
- **Permission:** `user.view`

### Create User
- **URL:** `/users`
- **Method:** `POST`
- **Permission:** `user.create`
- **Body:**
  ```json
  {
    "name": "Budi",
    "email": "budi@gmail.com",
    "password": "password123",
    "role": "admin"
  }
  ```

### Update User
- **URL:** `/users/{id}`
- **Method:** `PUT`
- **Permission:** `user.edit`
- **Body:** `name`, `email`, `password` (opsional), `role` (opsional).

### Delete User
- **URL:** `/users/{id}`
- **Method:** `DELETE`
- **Permission:** `user.delete`

---

## 🎭 5. Role & Permission Management
Hanya untuk role: `super_admin`.

### Get All Roles
Melihat daftar role beserta permissions yang dimilikinya.
- **URL:** `/roles`
- **Method:** `GET`
- **Permission:** `role.view`

### Create Role
- **URL:** `/roles`
- **Method:** `POST`
- **Permission:** `role.create`
- **Body:**
  ```json
  {
    "name": "Editor",
    "description": "Bisa edit monitor saja",
    "permissions": ["monitor.view", "monitor.edit"]
  }
  ```

### Update Role
- **URL:** `/roles/{id}`
- **Method:** `PUT`
- **Permission:** `role.edit`
- **Body:** `name`, `description`, `permissions` (array of strings).

### Delete Role
- **URL:** `/roles/{id}`
- **Method:** `DELETE`
- **Permission:** `role.delete`

### Get All Available Permissions
Melihat daftar mentah semua permission yang ada di sistem.
- **URL:** `/permissions`
- **Method:** `GET`
- **Permission:** `role.view`

---

## 📊 6. Dashboard Status
Mendapatkan ringkasan statistik untuk dashboard.
- **URL:** `/status`
- **Method:** `GET`
- **Response (200):**
  ```json
  {
    "total_monitors": 10,
    "healthy": 8,
    "down": 2,
    "avg_response_time": "250ms"
  }
  ```

---

## ⚠️ Error Responses

| Status Code | Makna |
|---|---|
| `401` | **Unauthorized**: Token salah atau belum login. |
| `403` | **Forbidden**: User login tapi tidak punya permission (hak akses) untuk endpoint tersebut. |
| `404` | **Not Found**: Data (ID) tidak ditemukan. |
| `422` | **Unprocessable Entity**: Validasi gagal (misal email sudah ada). |
| `500` | **Server Error**: Terjadi kesalahan pada server. |

---

Dibuat untuk mempermudah integrasi Frontend dan Mobile App. 🚀
