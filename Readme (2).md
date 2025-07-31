# Sistem Monitoring ZI Aceh

Sistem monitoring Zona Integritas dengan fitur login dan manajemen data menggunakan PHP dan MySQL.

## 📋 Fitur

- 🔐 **Sistem Login** - Autentikasi user dengan session management
- 📊 **Dashboard** - Statistik dan visualisasi data monitoring
- ➕ **CRUD Data** - Tambah, edit, hapus data monitoring
- 👤 **User Management** - Support role admin dan user
- 📱 **Responsive Design** - Compatible dengan mobile dan desktop
- 🔒 **Security** - Password hashing dan input validation

## 🛠️ Persyaratan Sistem

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau MariaDB 10.2
- Web server (Apache/Nginx)
- PDO PHP extension

## 📥 Instalasi

### 1. Persiapan Database

1. Buat database MySQL:
```sql
CREATE DATABASE monitoring_zi_aceh;
```

2. Import file SQL:
```bash
mysql -u root -p monitoring_zi_aceh < setup_database.sql
```

### 2. Konfigurasi

1. Edit file `config.php` sesuai dengan pengaturan database Anda:
```php
$host = 'localhost';
$dbname = 'monitoring_zi_aceh';
$username = 'your_db_username';
$password = 'your_db_password';
```

### 3. Struktur Folder

Pastikan struktur folder seperti ini:
```
project/
├── login.php
├── dashboard.php
├── config.php
├── auth.php
├── css/
│   └── dashboard.css
├── js/
│   └── dashboard.js
├── api/
│   ├── get_monitoring_data.php
│   ├── add_monitoring_data.php
│   ├── update_monitoring_data.php
│   ├── delete_monitoring_data.php
│   └── get_statistics.php
├── LOGO BPS.png
└── README.md
```

### 4. Permissions

Pastikan folder dan file memiliki permission yang sesuai:
```bash
chmod 755 /path/to/project
chmod 644 /path/to/project/*.php
chmod 755 /path/to/project/api/
```

## 👤 Login Default

Setelah database di-setup, Anda bisa login dengan:

- **Username**: `admin`
- **Password**: `password`

⚠️ **Penting**: Ubah password default setelah login pertama!

## 🚀 Penggunaan

### Login
1. Akses `login.