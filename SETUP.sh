#!/bin/bash
# SETUP GUIDE - Web SEMA FTI UAJY

## Prerequisites
# 1. XAMPP or similar (PHP 8.2+, MySQL/MariaDB)
# 2. Node.js & npm (for frontend development)

## Step 1: Database Setup

echo "=== SETUP DATABASE ==="
echo "1. Open phpMyAdmin: http://localhost/phpmyadmin"
echo "2. Create database: senat_mahasiswa"
echo "3. Import senat_mahasiswa.sql"
echo ""
echo "OR use command line:"
echo "mysql -u root -p < senat_mahasiswa.sql"
echo ""

## Step 2: Frontend Setup

echo "=== SETUP FRONTEND ==="
cd frontend
npm install
echo "Frontend dependencies installed!"
echo ""

## Step 3: Start Services

echo "=== STARTING SERVICES ==="
echo ""
echo "Option A: Using XAMPP/Apache"
echo "- Start Apache from XAMPP Control Panel"
echo "- Homepage: http://localhost/Web_SEMA/"
echo "- Admin Panel: http://localhost/Web_SEMA/admin/login.php"
echo ""

echo "Option B: Using PHP Built-in Server (Backend only)"
echo "- In backend folder: php -S localhost:8000"
echo "- Configure frontend .env with: VITE_API_BASE_URL=http://localhost:8000"
echo ""

echo "Frontend Development Server:"
echo "- In frontend folder: npm run dev"
echo "- Dev URL: http://localhost:5173"
echo ""

## Step 4: Default Login

echo "=== DEFAULT ADMIN LOGIN ==="
echo "Username: edwardchristi4n"
echo "Password: AdminSenatMahasiswa2025_!"
echo ""
