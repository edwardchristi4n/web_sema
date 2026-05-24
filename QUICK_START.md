# 🚀 Web SEMA FTI UAJY - Quick Start Guide

## Prerequisites

- **XAMPP** (PHP 8.2+, MySQL/MariaDB) atau server PHP lainnya
- **Node.js** v16+ dan npm
- **Git** (opsional, untuk version control)

---

## ⚙️ Setup Awal

### 1️⃣ Database Setup

**Menggunakan phpMyAdmin:**

1. Buka http://localhost/phpmyadmin
2. Buat database baru: `senat_mahasiswa`
3. Pilih database → Import → Upload file `senat_mahasiswa.sql`
4. Klik Import

**Menggunakan Command Line:**

```bash
# Windows Command Prompt
mysql -u root -p senat_mahasiswa < senat_mahasiswa.sql

# Jika tidak ada password
mysql -u root senat_mahasiswa < senat_mahasiswa.sql
```

### 2️⃣ Frontend Dependencies

```bash
cd frontend
npm install
```

---

## 🏃 Cara Menjalankan Website

### Option 1: Menggunakan XAMPP Apache (Recommended)

1. **Start XAMPP Apache**
   - Buka XAMPP Control Panel
   - Klik "Start" pada Apache

2. **Akses Website**
   - Homepage: http://localhost/Web_SEMA/
   - Admin Panel: http://localhost/Web_SEMA/admin/login.php

3. **Frontend Development (Optional)**
   - Jika ingin development dengan hot reload:
   ```bash
   cd frontend
   npm run dev
   ```

   - Akses: http://localhost:5173

### Option 2: Menggunakan PHP Built-in Server

**Backend:**

```bash
cd backend
php -S localhost:8000
```

**Frontend:**

```bash
cd frontend
npm run dev
```

**Update .env untuk point ke backend lokal:**

```env
VITE_API_BASE_URL=http://localhost:8000
VITE_ASSET_BASE_URL=http://localhost:5173
```

---

## 🔐 Default Admin Login

| Field        | Value                       |
| ------------ | --------------------------- |
| **Username** | `edwardchristi4n`           |
| **Password** | `AdminSenatMahasiswa2025_!` |

**Admin Panel Features:**

- ✅ Kelola Divisi (Division)
- ✅ Kelola Event/Berita
- ✅ Kelola Member
- ✅ Kelola Program Kerja (Proker)

---

## 🛠️ Troubleshooting

### ❌ Database Connection Failed

**Error:** "Database connection failed"

**Solution:**

1. Pastikan MySQL/MariaDB running
2. Cek konfigurasi di `config/database.php`:
   ```php
   $host = "127.0.0.1";  // atau localhost
   $user = "root";
   $pass = "";           // sesuaikan password Anda
   $db   = "senat_mahasiswa";
   ```
3. Restart MySQL service

### ❌ API Not Found (404)

**Error:** "Cannot POST /backend/api/..."

**Solution:**

1. Pastikan Apache/PHP Server running
2. Cek `VITE_API_BASE_URL` di `.env` sesuai dengan setup Anda
3. URL harus mengarah ke folder `backend/api/`

### ❌ Frontend Not Loading

**Error:** Blank page atau error di console

**Solution:**

1. Cek browser console (F12 → Console)
2. Pastikan `npm install` sudah dijalankan
3. Restart dev server: `npm run dev`
4. Clear browser cache: Ctrl+Shift+Delete

### ❌ Upload Files Not Working

**Error:** "Failed to upload image"

**Solution:**

1. Folder `asset/uploads/` sudah dibuat ✅
2. Cek folder permissions (harus writable)
3. Max upload size: 2MB per file

---

## 📁 Important Directories

```
Web_SEMA/
├── config/database.php           ← Database connection
├── backend/api/                  ← REST API endpoints
├── frontend/                     ← React + Vite app
├── admin/                        ← Legacy PHP admin panel
├── asset/uploads/                ← User uploaded files
└── senat_mahasiswa.sql           ← Database schema
```

---

## 📚 Project Structure

| Folder           | Purpose                  |
| ---------------- | ------------------------ |
| `backend/api/`   | REST API (PHP)           |
| `frontend/`      | React SPA (Vite)         |
| `admin/`         | Legacy Admin Panel (PHP) |
| `config/`        | Configuration files      |
| `asset/uploads/` | Uploaded media files     |

---

## 🔄 API Endpoints

| Method | Endpoint                  | Purpose        |
| ------ | ------------------------- | -------------- |
| GET    | `/backend/api/divisi.php` | List divisions |
| GET    | `/backend/api/event.php`  | List events    |
| GET    | `/backend/api/member.php` | List members   |
| GET    | `/backend/api/proker.php` | List programs  |
| POST   | `/backend/api/auth.php`   | Login          |

---

## ✅ Health Check

Jalankan checklist ini untuk memastikan setup benar:

- [ ] Database `senat_mahasiswa` exist
- [ ] XAMPP Apache/PHP Server running
- [ ] Homepage accessible at http://localhost/Web_SEMA/
- [ ] Admin login works: http://localhost/Web_SEMA/admin/login.php
- [ ] `npm install` completed in `frontend/`
- [ ] Upload directories created (`asset/uploads/`)
- [ ] `.env` file exists in `frontend/`

---

## 📞 Support

Untuk error, silakan cek:

1. Browser console (F12)
2. Apache error log di XAMPP
3. Check file `AGENTS.md` untuk dokumentasi lengkap

---

**Last Updated:** May 24, 2026
**Status:** Ready for Development ✅
