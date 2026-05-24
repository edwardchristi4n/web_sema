# 🔧 Web SEMA - Comprehensive Troubleshooting Guide

## Table of Contents

1. [Connection Issues](#connection-issues)
2. [Database Problems](#database-problems)
3. [Frontend Errors](#frontend-errors)
4. [Backend API Issues](#backend-api-issues)
5. [File Upload Problems](#file-upload-problems)
6. [Performance Issues](#performance-issues)

---

## Connection Issues

### ❌ "Cannot Connect to Server"

**Symptoms:** Browser shows "ERR_CONNECTION_REFUSED" or "localhost refused to connect"

**Cause:** Apache/PHP server not running

**Solution:**

```
1. Open XAMPP Control Panel
2. Check if Apache has a green status
3. If not, click "Start" button next to Apache
4. Wait 3-5 seconds for it to start
5. Refresh browser
```

**If still fails:**

```powershell
# Check if Apache is on port 80
netstat -ano | findstr :80

# If nothing appears, Apache is not running
# Check Apache error log:
# XAMPP → Apache → Config → Error Log
```

---

### ❌ "This Site Can't Be Reached"

**Symptoms:** Error when visiting `http://localhost/Web_SEMA/`

**Causes:**

1. Apache not running
2. Project not in correct folder
3. Wrong URL path

**Solutions:**

**Check 1: Is Apache running?**

```
XAMPP Control Panel → Apache should show "Running" in green
```

**Check 2: Is project in correct location?**

```powershell
dir C:\xampp\htdocs\Web_SEMA
# Should show: index.php, admin/, frontend/, backend/, etc.
```

**Check 3: Try correct URL**

```
http://localhost/Web_SEMA/              ← Correct
http://localhost:80/Web_SEMA/           ← Also works
http://127.0.0.1/Web_SEMA/              ← Also works
```

---

## Database Problems

### ❌ "Database Connection Failed"

**Symptoms:** Error message: "Database connection failed" or "Koneksi gagal"

**Common Causes:**

1. MySQL not running
2. Wrong credentials in `config/database.php`
3. Database doesn't exist
4. Wrong hostname

**Solutions:**

**Step 1: Verify MySQL is Running**

```powershell
# XAMPP Control Panel → Check MySQL status
# Should show "Running" in green

# OR check from command line
cd C:\xampp\mysql\bin
.\mysql -u root
# If command prompt appears, MySQL is running
# Type: exit
```

**Step 2: Check Credentials**

```php
// File: config/database.php
$host = "127.0.0.1";    // ← Host
$user = "root";         // ← Username
$pass = "";             // ← Password (empty by default)
$db   = "senat_mahasiswa";  // ← Database name
```

**Verify these are correct:**

```powershell
# Test login
cd C:\xampp\mysql\bin
.\mysql -u root -p
# Press Enter if no password, else enter password
```

**Step 3: Check Database Exists**

```powershell
cd C:\xampp\mysql\bin
.\mysql -u root -e "SHOW DATABASES LIKE 'senat_mahasiswa';"
# Should return: senat_mahasiswa
```

**Step 4: Import Database if Missing**

```powershell
cd C:\xampp\mysql\bin
.\mysql -u root senat_mahasiswa < C:\xampp\htdocs\Web_SEMA\senat_mahasiswa.sql
```

---

### ❌ "Table 'senat_mahasiswa.divisi' Doesn't Exist"

**Symptom:** Error when visiting pages or admin panel

**Cause:** Database tables not imported

**Solution:**

```powershell
# Re-import the database
cd C:\xampp\mysql\bin
.\mysql -u root senat_mahasiswa < C:\xampp\htdocs\Web_SEMA\senat_mahasiswa.sql

# Verify tables exist
.\mysql -u root senat_mahasiswa -e "SHOW TABLES;"
# Should show: admin, divisi, event, member, proker
```

---

### ❌ "Access Denied for User 'root'@'localhost'"

**Symptom:** Password error on database connection

**Cause:** Wrong password in `config/database.php`

**Solution:**

**Check what password MySQL has:**

```powershell
# Try to login without password
cd C:\xampp\mysql\bin
.\mysql -u root

# If it works, password is empty ("")
# If it fails, you need to set correct password
```

**Edit config/database.php:**

```php
// If password is empty:
$pass = "";

// If you set a password:
$pass = "your_password_here";
```

---

## Frontend Issues

### ❌ "Blank White Page"

**Symptoms:** Browser loads but page is completely blank

**Causes:**

1. CSS not loading from CDN
2. JavaScript error preventing render
3. React component error

**Solutions:**

**Step 1: Check Browser Console**

```
Press F12 → Console tab
Look for red error messages
Common errors:
- "Failed to fetch" → Backend API issue
- "Cannot find module" → Missing npm package
- "Tailwind is not defined" → CSS issue
```

**Step 2: Check if Using Frontend Dev Server**

```powershell
# If using npm run dev:
cd C:\xampp\htdocs\Web_SEMA\frontend
npm run dev
# Access: http://localhost:5173
# (NOT http://localhost/Web_SEMA/)
```

**Step 3: Clear Cache**

```
Press Ctrl+Shift+Delete
Select "All time"
Check: Cookies, Cache
Click "Clear data"
Refresh page
```

**Step 4: Rebuild Frontend**

```powershell
cd C:\xampp\htdocs\Web_SEMA\frontend
rm -r node_modules
npm install
npm run dev
```

---

### ❌ "API is not responding"

**Symptom:** Console shows API error, page content missing

**Causes:**

1. Wrong API base URL
2. Apache not running
3. Backend API having issues

**Solutions:**

**Check 1: Verify API URL**

```
File: frontend/.env
Content: VITE_API_BASE_URL=http://localhost/Web_SEMA/backend/api

If using PHP Server (port 8000):
VITE_API_BASE_URL=http://localhost:8000
```

**Check 2: Test API Directly**

```
Browser: http://localhost/Web_SEMA/backend/api/divisi.php
Expected: JSON response with division data
If blank: Check browser console for PHP errors
```

**Check 3: Verify Apache is Running**

```
XAMPP Control Panel → Apache should be "Running"
```

---

## Backend API Issues

### ❌ "API Returns 404"

**Symptom:** Browser shows "404 Not Found" at backend/api/

**Causes:**

1. Apache not running
2. Wrong API URL
3. File doesn't exist

**Solutions:**

**Step 1: Check Apache Status**

```
XAMPP Control Panel → Apache = "Running"?
If not, click "Start"
```

**Step 2: Verify Correct URL**

```
http://localhost/Web_SEMA/backend/api/divisi.php    ← Correct
http://localhost:8000                                ← If using PHP Server
```

**Step 3: Check File Exists**

```powershell
dir C:\xampp\htdocs\Web_SEMA\backend\api\
# Should show: _bootstrap.php, auth.php, divisi.php, etc.
```

---

### ❌ "API Returns 500 Error"

**Symptom:** "Internal Server Error" from API

**Causes:**

1. Database connection error
2. PHP syntax error
3. Missing required field

**Solutions:**

**Check 1: Verify Database**

```powershell
cd C:\xampp\mysql\bin
.\mysql -u root senat_mahasiswa -e "SELECT COUNT(*) FROM divisi;"
# Should return a number
```

**Check 2: Check PHP Errors**

```
XAMPP → Apache → Config → Error Log
Look for recent errors
```

**Check 3: Manually Test API**

```powershell
# Test divisi endpoint
cd C:\xampp\htdocs\Web_SEMA
php backend/api/divisi.php

# Should output JSON
```

---

### ❌ "Session Expired / Not Authenticated"

**Symptom:** Admin login not working, API returns 401

**Causes:**

1. Session not initialized
2. Login credentials wrong
3. Cookie issues

**Solutions:**

**Step 1: Verify Login Credentials**

```
Username: edwardchristi4n
Password: AdminSenatMahasiswa2025_!

Try logging in to: http://localhost/Web_SEMA/admin/login.php
```

**Step 2: Check Database Admin User**

```powershell
cd C:\xampp\mysql\bin
.\mysql -u root senat_mahasiswa -e "SELECT id_admin, username FROM admin;"
# Should show edwardchristi4n
```

**Step 3: Clear Cookies**

```
Press F12 → Storage tab → Cookies
Delete all cookies for localhost
Try login again
```

---

## File Upload Problems

### ❌ "Failed to Upload Image"

**Symptoms:** Upload returns error, no files appear

**Causes:**

1. Upload directory missing
2. Directory not writable
3. File too large
4. Wrong file type

**Solutions:**

**Step 1: Verify Upload Directories Exist**

```powershell
dir C:\xampp\htdocs\Web_SEMA\asset\uploads\

# Should show:
# proker/
# event/
# member/

# If missing, create them:
mkdir C:\xampp\htdocs\Web_SEMA\asset\uploads\proker
mkdir C:\xampp\htdocs\Web_SEMA\asset\uploads\event
mkdir C:\xampp\htdocs\Web_SEMA\asset\uploads\member
```

**Step 2: Check Directory Permissions**

```
Right-click folder → Properties
Security tab → Edit → Check "Full Control"
Apply → OK
```

**Step 3: Check File Requirements**

```
Max size: 2MB per file
Allowed types: JPG, PNG, WEBP
Filename: Letters, numbers, underscores only
```

**Step 4: Test Upload**

```
1. Login to admin panel
2. Go to Program Kerja (Proker)
3. Try uploading a small JPG image
4. Check: asset/uploads/proker/ folder for file
```

---

## Performance Issues

### ❌ "Page Loads Slowly"

**Symptoms:** Website takes 10+ seconds to load

**Causes:**

1. Slow database queries
2. Large image files
3. Many network requests
4. Insufficient server resources

**Solutions:**

**Step 1: Check Database Performance**

```powershell
cd C:\xampp\mysql\bin
.\mysql -u root senat_mahasiswa -e "SELECT COUNT(*) FROM event;"
.\mysql -u root senat_mahasiswa -e "SELECT COUNT(*) FROM member;"

# If very large numbers (10000+), queries might be slow
```

**Step 2: Optimize Images**

```
Image sizes should be:
- Homepage hero: ~50-100KB
- Thumbnails: ~10-30KB
- Member photos: ~30-50KB

If larger, compress using:
- Online: tinypng.com
- Windows: Paint → Save as JPG
```

**Step 3: Check Server Resources**

```
Task Manager → Performance
CPU: Should be below 50% idle
Memory: Should have free space
Disk: Should have free space
```

**Step 4: Enable Caching**

```
Clear browser cache helps loading times:
Ctrl+Shift+Delete → Clear all
```

---

## Emergency Reset

### Reset Everything (Last Resort)

```powershell
# 1. Stop Apache from XAMPP Control Panel
# 2. Stop MySQL from XAMPP Control Panel

# 3. Re-import database
cd C:\xampp\mysql\bin
.\mysql -u root senat_mahasiswa < C:\xampp\htdocs\Web_SEMA\senat_mahasiswa.sql

# 4. Clear upload folders
rm C:\xampp\htdocs\Web_SEMA\asset\uploads\proker\*
rm C:\xampp\htdocs\Web_SEMA\asset\uploads\event\*
rm C:\xampp\htdocs\Web_SEMA\asset\uploads\member\*

# 5. Restart Apache and MySQL from XAMPP Control Panel
# 6. Clear browser cache (Ctrl+Shift+Del)
# 7. Restart browser
```

---

## Getting Help

If stuck, follow this order:

1. **Check XAMPP Status** → Are Apache and MySQL running?
2. **Check Browser Console** → F12 → Are there errors?
3. **Check Database** → Does database exist with tables?
4. **Check Logs** → XAMPP → Logs folder
5. **Check File Permissions** → Are upload folders writable?
6. **Clear Cache** → Ctrl+Shift+Del
7. **Restart Everything** → Stop and start XAMPP services

---

**Last Updated:** May 24, 2026  
**For more help:** See STARTUP.md or AGENTS.md
