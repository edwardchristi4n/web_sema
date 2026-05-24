# 📋 Web SEMA - Server Startup Instructions

## ✅ Pre-Startup Checklist (All VERIFIED ✓)

- ✅ Database `senat_mahasiswa` exists with all 5 tables
- ✅ Admin user created (edwardchristi4n)
- ✅ All PHP files have NO syntax errors
- ✅ Frontend npm dependencies installed (11 packages)
- ✅ Upload directories created (`asset/uploads/proker/`, `event/`, `member/`)
- ✅ Frontend `.env` file configured
- ✅ Config files ready (`config/database.php`)

---

## 🚀 Quick Start (Choose ONE Option)

### Option 1: XAMPP Apache (EASIEST - Recommended)

**Step 1: Start Apache**

1. Open XAMPP Control Panel (`C:\xampp\xampp-control.exe`)
2. Click "Start" next to Apache
3. Wait for status to show "Running" (green)

**Step 2: Access Website**

- Homepage: `http://localhost/Web_SEMA/`
- Admin Panel: `http://localhost/Web_SEMA/admin/login.php`
- Login with: `edwardchristi4n` / `AdminSenatMahasiswa2025_!`

**Step 3 (Optional): Start Frontend Dev Server**

```powershell
cd C:\xampp\htdocs\Web_SEMA\frontend
npm run dev
```

- Frontend Dev: `http://localhost:5173`

---

### Option 2: PHP Built-in Server + Vite

**Terminal 1: Start Backend**

```powershell
cd C:\xampp\htdocs\Web_SEMA\backend
php -S localhost:8000
```

**Terminal 2: Start Frontend**

```powershell
cd C:\xampp\htdocs\Web_SEMA\frontend
npm run dev
```

**Access:**

- Frontend: `http://localhost:5173`
- Backend API: `http://localhost:8000`

**Update `.env` in frontend:**

```env
VITE_API_BASE_URL=http://localhost:8000
VITE_ASSET_BASE_URL=http://localhost:5173
```

---

## 🔐 Default Admin Credentials

| Field    | Value                       |
| -------- | --------------------------- |
| Username | `edwardchristi4n`           |
| Password | `AdminSenatMahasiswa2025_!` |

---

## 🧪 Testing the Setup

### Test 1: Homepage Loading

```
Visit: http://localhost/Web_SEMA/
Expected: Page loads with SEMA header, hero section, divisions
```

### Test 2: Admin Login

```
Visit: http://localhost/Web_SEMA/admin/login.php
Enter: edwardchristi4n / AdminSenatMahasiswa2025_!
Expected: Redirects to dashboard
```

### Test 3: API Endpoint

```
Browser: http://localhost/Web_SEMA/backend/api/divisi.php
Expected: JSON response with list of divisions
```

### Test 4: Frontend Dev

```
Terminal: npm run dev (in frontend folder)
Browser: http://localhost:5173
Expected: React app loads with Vite hot reload
```

---

## ❌ Troubleshooting

### "Page Not Found" Error

**Problem:** 404 when accessing http://localhost/Web_SEMA/

**Solutions:**

1. ✅ Apache must be running (check XAMPP)
2. ✅ Project must be in `C:\xampp\htdocs\Web_SEMA\`
3. ✅ Restart Apache from XAMPP

---

### "Cannot Connect to Database" Error

**Problem:** Error message about database connection

**Solution:**

```php
// Edit: config/database.php
$host = "127.0.0.1";  // Change if needed
$user = "root";       // Change to your MySQL user
$pass = "";           // Add password if set
$db   = "senat_mahasiswa";
```

Then restart Apache.

---

### "API Endpoint Returns 404"

**Problem:** Frontend can't reach backend API

**Solutions:**

1. ✅ Check `.env` has correct `VITE_API_BASE_URL`
2. ✅ For XAMPP: Use `http://localhost/Web_SEMA/backend/api`
3. ✅ For PHP Server: Use `http://localhost:8000`

---

### "Blank Page / White Screen"

**Problem:** Browser shows blank page

**Solution:**

1. Open DevTools (F12)
2. Check Console tab for errors
3. If API error: Check if backend is running
4. If CSS missing: Clear browser cache (Ctrl+Shift+Del)

---

### "Upload Files Fail"

**Problem:** Cannot upload images in admin panel

**Solution:**

1. ✅ Verify folders exist:
   ```powershell
   dir C:\xampp\htdocs\Web_SEMA\asset\uploads\
   ```
2. ✅ Check folder permissions (should be writable)
3. ✅ Max file size: 2MB per image

---

## 📊 Architecture Overview

```
┌─────────────────────────────────────┐
│   Frontend (React + Vite)           │
│   Port: 5173 (dev) / :80 (prod)     │
└────────────────┬────────────────────┘
                 │ (Axios HTTP)
                 ↓
┌─────────────────────────────────────┐
│   Backend API (PHP)                 │
│   http://localhost/Web_SEMA/backend  │
└────────────────┬────────────────────┘
                 │ (mysqli)
                 ↓
┌─────────────────────────────────────┐
│   MySQL Database                    │
│   senat_mahasiswa (5 tables)         │
└─────────────────────────────────────┘
```

---

## 📁 Key Files Location

| File            | Purpose             | Path                         |
| --------------- | ------------------- | ---------------------------- |
| Database Config | Connection settings | `config/database.php`        |
| Frontend Env    | API URL config      | `frontend/.env`              |
| Database Dump   | Schema & data       | `senat_mahasiswa.sql`        |
| Admin Panel     | Legacy UI           | `admin/login.php`            |
| API Bootstrap   | API setup           | `backend/api/_bootstrap.php` |

---

## 🔄 Common Tasks

### Restart Everything

1. Stop Apache (XAMPP Control Panel)
2. Stop any npm dev servers (Ctrl+C)
3. Start Apache again
4. Start npm if needed

### Reset Database

```powershell
cd C:\xampp\mysql\bin
.\mysql -u root senat_mahasiswa < C:\xampp\htdocs\Web_SEMA\senat_mahasiswa.sql
```

### Check Services Status

```powershell
# Check if port 80 is open (Apache)
netstat -ano | findstr :80

# Check if port 5173 is open (Frontend Dev)
netstat -ano | findstr :5173
```

---

## 📞 Emergency Help

If stuck, try these in order:

1. **Restart XAMPP Apache** (always first step)
2. **Clear browser cache** (Ctrl+Shift+Del)
3. **Check browser console** (F12)
4. **Check Apache error log** (XAMPP → Apache → "Config" → "Apache" → "Error Log")
5. **Verify database** exists with tables
6. **Check file permissions** on `asset/uploads/`

---

**Status:** ✅ ALL SYSTEMS READY
**Last Check:** May 24, 2026
**Next Steps:** Start Apache → Access http://localhost/Web_SEMA/
