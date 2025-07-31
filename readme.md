# 🗂️ Website Monitoring Kegiatan Kantor

Website ini dibuat untuk membantu memonitor dan mengelola kegiatan pegawai di lingkungan kantor dengan sistem login berbasis role. Terdapat 5 peran utama dengan hak akses yang berbeda.

---

## 🔐 Alur Akses & Login

1. Ketika pengguna mengakses website:

   -  Sistem mengecek apakah user sudah login dan sesi masih aktif (berlaku 24 jam).
   -  Jika **belum login atau sesi sudah kadaluarsa**, user akan diarahkan ke halaman login.
   -  Jika sesi masih aktif, user akan langsung diarahkan ke halaman sesuai perannya.

2. Pada halaman login:
   -  User memasukkan **username dan password**.
   -  Jika valid, sistem menyimpan sesi login selama 24 jam.
   -  Berdasarkan role, user akan diarahkan ke dashboard yang sesuai.

---

## 👤 Role & Hak Akses

### 🔹 Pegawai

-  Melihat **data kegiatan pribadi**
-  Melihat **status data** (disetujui/ditolak)
-  Melihat **komentar/revisi**
-  ❌ Tidak dapat edit atau hapus data

### 🔹 Pemeriksa

-  Melihat **semua data**
-  **Menyetujui/menolak** data
-  Memberi **catatan revisi**
-  ❌ Tidak dapat menghapus data

### 🔹 Admin

-  **Akses penuh** ke seluruh sistem
-  CRUD **semua data kegiatan**
-  **Kelola user**:
   -  Tambah, ubah, hapus user
   -  Ubah role, password, dan username
-  Halaman admin hanya muncul jika role = `admin`

### 🔹 Kepala BPS

-  Melihat **semua data kegiatan**
-  **Menyetujui** data
-  Melihat seluruh **status dan revisi**

### 🔹 Operator

-  Melihat **semua data**
-  Memberi **revisi**
-  **Memantau perkembangan** status data
-  ❌ Tidak dapat menyetujui/menolak data

---

## 🔒 Sesi & Keamanan

-  Sesi login berlaku selama **24 jam**
-  Setelah lewat 24 jam, user akan **otomatis logout**
-  Semua akses halaman menggunakan **middleware role-based**
-  User tanpa akses yang sesuai akan ditolak

---

## 🚪 Logout

-  User dapat logout kapan saja
-  Setelah logout, sesi dihapus dan harus login ulang untuk akses kembali

---

## ✨ Fitur Tambahan

### ✅ Audit Log / Riwayat Aktivitas

-  Setiap tindakan penting dicatat:
   -  Siapa menyetujui, menolak, merevisi, menghapus
-  Disimpan di tabel `log_aktivitas`:
   -  id, user_id, aksi, target_id, waktu, keterangan

### ✅ Notifikasi Sistem

-  Pegawai diberi notifikasi saat data disetujui/ditolak
-  Pemeriksa diberi notifikasi saat ada data baru
-  Operator diberi notifikasi saat ada data masuk untuk revisi

### ✅ Fitur Reset Password

-  User bisa klik "Lupa Password?" untuk reset otomatis
-  Admin bisa reset password ke default, tanpa perlu melihat password lama

---

## 📌 Teknologi yang Disarankan (Opsional)

-  Backend: Laravel / Express / Django
-  Frontend: React / Vue / Blade (jika Laravel)
-  Database: MySQL / PostgreSQL
-  Auth: Session + Middleware Role-Based Access

---

## 🛠️ Status

> Proyek ini dalam tahap pengembangan awal dan akan terus disempurnakan sesuai kebutuhan kantor.


## Gambaran Alur

START
  │
  ├──► [User Akses Website]
  │        │
  │        └──► [Cek Status Login (Sesi 24 Jam)]
  │                 │
  │                 ├──► [Belum Login]
  │                 │        └──► Redirect ke [Halaman Login]
  │                 │                     │
  │                 │                     └──► [Input Username & Password]
  │                 │                              │
  │                 │                              ├──► Jika BENAR:
  │                 │                              │        ├── Simpan sesi (24 jam)
  │                 │                              │        └── Periksa role → Redirect ke dashboard sesuai role
  │                 │                              └──► Jika SALAH:
  │                 │                                       └── Tampilkan error
  │                 │
  │                 └──► [Sudah Login]
  │                          └──► Periksa role → Redirect ke dashboard sesuai role
  │
  └──► [Dashboard Berdasarkan Role]
            │
            ├──► Jika **Pegawai**:
            │        ├── Melihat data pribadi
            │        ├── Melihat status persetujuan
            │        └── Melihat komentar atau catatan revisi
            │
            ├──► Jika **Pemeriksa**:
            │        ├── Melihat semua data
            │        ├── Memberi catatan revisi
            │        └── Menyetujui atau menolak data
            │
            ├──► Jika **Admin**:
            │        ├── Akses semua halaman
            │        ├── CRUD semua data
            │        └── Kelola user (tambah, hapus, ubah, reset password, ubah role)
            │
            ├──► Jika **Kepala BPS**:
            │        ├── Melihat seluruh data
            │        ├── Menyetujui data
            │        └── Melihat semua riwayat revisi dan status
            │
            └──► Jika **Operator**:
                     ├── Melihat semua data
                     ├── Memberikan revisi
                     └── Memantau status perkembangan data

  ↓
[Logout]
  └──► Hapus sesi → Redirect ke Halaman Login

END

## 👤 Login Default

Setelah database di-setup, Anda bisa login dengan:

- Pegawai:
   - **Username**: `andi`
   - **Password**: `123456`
- Admin
   - **Username**: `admin`
   - **Password**: `admin123`
- Operator
   - **Username**: `siti`
   - **Password**: `operator01`
- Kepala BPS
   - **Username**: `budi`
   - **Password**: `bps2024`
- Pemeriksa
   - **Username**: `dina`
   - **Password**: `checkdata`