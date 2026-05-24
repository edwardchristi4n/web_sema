# SEMA FTI UAJY - Senat Mahasiswa

Welcome to the official website of **Senat Mahasiswa (SEMA)** - Fakultas Teknologi Industri, Universitas Atma Jaya Yogyakarta.

🌐 **Website:** [http://localhost/Web_SEMA](http://localhost/Web_SEMA)

---

## About SEMA

SEMA adalah wadah aspirasi, kolaborasi, dan kreativitas mahasiswa Fakultas Teknologi Industri UAJY. Kami berkomitmen untuk:

- **Menampung Aspirasi** - Menjadi jembatan komunikasi antara mahasiswa dan jajaran fakultas
- **Memfasilitasi Kolaborasi** - Menghubungkan 5 bidang dan 12+ komunitas dalam sinergi
- **Mengembangkan Potensi** - Menyediakan platform untuk pertumbuhan akademik dan non-akademik

## 5 Bidang Utama SEMA

### 🏛️ Pengurus Harian

Memimpin dan mengoordinasikan seluruh kegiatan operasional SEMA dengan fokus pada sinergi antar bidang dan perencanaan program strategis.

### ⭐ Minat & Bakat

Memfasilitasi pengembangan potensi mahasiswa melalui festival, kompetisi, dan showcase bakat di berbagai bidang seni dan olahraga.

### 🤝 Sosial Masyarakat

Merancang gerakan sosial dan advokasi, memastikan kebijakan kampus berpihak pada mahasiswa dan masyarakat sekitar.

### 💰 Usaha Dana

Mengelola sumber pendanaan organisasi dan mengembangkan program wirausaha mahasiswa yang berkelanjutan.

### 📢 Komunikasi & Informasi

Mengemas narasi positif, membangun dokumentasi visual, dan menyebarkan informasi akurat kepada seluruh komunitas FTI.

## 12+ Komunitas FTI

SEMA membina dan menghubungkan berbagai komunitas mahasiswa:

| Komunitas             | Kategori      | Fokus                         |
| --------------------- | ------------- | ----------------------------- |
| **Badminton**         | Olahraga      | Kompetisi dan latihan rutin   |
| **SIKMA**             | Seni & Budaya | Musik, vokal, dan pertunjukan |
| **Texere**            | Olahraga      | Basketball                    |
| **FTI Image**         | Visual        | Fotografi dan videografi      |
| **Futsal**            | Olahraga      | Kompetitif                    |
| + 7 Komunitas lainnya | -             | -                             |

## Fitur Website

### 🏠 Landing Page

- **Hero Section** dengan galeri foto otomatis
- **Informasi Bidang** dengan deskripsi lengkap
- **Daftar Komunitas** yang aktif
- **Berita Terbaru** dari SEMA dan kampus
- **Statistik** tentang aktivitas dan pencapaian
- **Form Kontak** untuk pertanyaan dan saran

### 👥 Halaman Detail Bidang

Setiap bidang memiliki halaman detail dengan:

- Visi, misi, dan deskripsi
- Daftar program kerja (proker)
- Struktur anggota dengan foto
- Link kembali ke landing page

### 🎛️ Admin Dashboard

_Khusus admin SEMA untuk mengelola konten:_

- **Manajemen Bidang** - Tambah/edit/hapus divisi
- **Manajemen Event** - Input berita dan acara terbaru
- **Manajemen Komunitas** - Data anggota dan foto
- **Manajemen Program** - Daftar program kerja per bidang
- **Upload Foto** - Dukungan upload gambar untuk setiap konten

## Cara Menggunakan Website

### Untuk Pengunjung Umum

1. **Jelajahi Beranda**
   - Scroll untuk melihat informasi lengkap tentang SEMA
   - Klik menu di atas untuk navigasi cepat

2. **Ketahui Bidang SEMA**
   - Klik kartu bidang untuk melihat detail lengkap
   - Lihat program kerja dan struktur anggota setiap bidang

3. **Cari Komunitas yang Sesuai**
   - Lihat daftar komunitas di section Komunitas
   - Baca deskripsi singkat setiap komunitas

4. **Ikuti Berita & Event**
   - Scroll ke section Berita untuk update terbaru
   - Baca lokasi dan tanggal setiap acara

5. **Hubungi Kami**
   - Isi form kontak dengan pertanyaan atau saran
   - Kunjungi kantor SEMA di Gedung Bonaventura Lt. 4

### Untuk Admin SEMA

1. **Login ke Dashboard**
   - Buka: `http://localhost/Web_SEMA/admin/login.php`
   - Masukkan username dan password

2. **Kelola Konten**
   - Pilih menu di sidebar (Divisi, Event, Member, Proker)
   - Klik tombol "Tambah" untuk membuat entri baru
   - Edit atau hapus entri yang sudah ada

3. **Upload Foto**
   - Setiap form memiliki field upload foto
   - Format yang didukung: JPG, PNG, GIF
   - Ukuran maksimal: 5MB

4. **Lihat Statistik**
   - Dashboard menampilkan ringkasan aktivitas
   - Monitor jumlah komunitas, acara, dan anggota

## Persyaratan Teknis

### Untuk Pengguna

- Browser modern (Chrome, Firefox, Safari, Edge)
- Koneksi internet stabil
- JavaScript diaktifkan

### Untuk Admin

- Akun admin dengan username dan password
- Akses ke admin dashboard

### Untuk Developer

- PHP 7.4+
- MySQL 5.7+ atau MariaDB
- Apache web server dengan mod_rewrite
- Tailwind CSS via CDN (tidak perlu instalasi)

---

## Modern Refactor (React + API)

Refactor bertahap telah ditambahkan dengan struktur baru:

- **Frontend React (Vite)** di folder `frontend/`
- **Backend API (PHP)** di folder `backend/api/`

Frontend baru mengambil data dari API JSON, sedangkan PHP hanya berfungsi sebagai API.

### Menjalankan Backend API

Pastikan XAMPP/Apache berjalan dan project tersedia di `http://localhost/Web_SEMA`.

API dapat diakses di:

```
http://localhost/Web_SEMA/backend/api
```

### Menjalankan Frontend React

1. Masuk ke folder frontend:

```bash
cd frontend
```

2. Install dependencies:

```bash
npm install
```

3. Salin env dan sesuaikan base API:

```bash
copy .env.example .env
```

Atur variabel berikut bila frontend tidak bisa menampilkan gambar dari XAMPP:

```
VITE_ASSET_BASE_URL=http://localhost/Web_SEMA
```

4. Jalankan dev server:

```bash
npm run dev
```

Frontend akan berjalan di `http://localhost:5173`.

### Struktur Folder Baru (Ringkas)

```
backend/
   api/
      _bootstrap.php
      auth.php
      divisi.php
      event.php
      member.php
      proker.php
      upload.php
frontend/
   src/
      components/
      layouts/
      pages/
      services/
      hooks/
      utils/
```

### Daftar Endpoint API

Format response:

```json
{
  "success": true,
  "message": "Success message",
  "data": []
}
```

Endpoints:

- `GET /divisi.php`
- `GET /divisi.php?id={id}`
- `POST /divisi.php`
- `PUT /divisi.php?id={id}`
- `DELETE /divisi.php?id={id}`

- `GET /event.php`
- `GET /event.php?limit=3`
- `POST /event.php`
- `PUT /event.php?id={id}`
- `DELETE /event.php?id={id}`

- `GET /member.php`
- `GET /member.php?divisi_id={id}`
- `POST /member.php`
- `PUT /member.php?id={id}`
- `DELETE /member.php?id={id}`

- `GET /proker.php`
- `GET /proker.php?divisi_id={id}`
- `POST /proker.php`
- `PUT /proker.php?id={id}`
- `DELETE /proker.php?id={id}`

- `POST /auth.php` (login)
- `GET /auth.php` (check session)
- `DELETE /auth.php` (logout)

- `POST /upload.php?type=divisi|event|member|proker`

### Login Admin React

Gunakan kredensial yang sama dengan admin lama. Login tersedia di:

```
http://localhost:5173/admin/login
```

## Instalasi & Setup

### 1. Persiapan Database

```bash
# Import database schema
mysql -u username -p database_name < senat_mahasiswa.sql
```

### 2. Konfigurasi Koneksi

Edit `config/database.php`:

```php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'senat_mahasiswa';
```

### 3. Buat Admin Pertama

Jalankan script:

```bash
php admin/create_admin.php
```

### 4. Akses Website

```
Homepage: http://localhost/Web_SEMA/
Admin: http://localhost/Web_SEMA/admin/login.php
```

## Fitur Keamanan

- ✅ **Prepared Statements** - Proteksi dari SQL injection
- ✅ **Input Validation** - Semua input divalidasi
- ✅ **Session Authentication** - Login dengan session token
- ✅ **HTTPS Recommended** - Untuk produksi gunakan SSL

## FAQ

### Q: Bagaimana cara bergabung dengan SEMA?

**A:** Ikuti proses recruitment yang diumumkan di awal semester. Informasi dapat dilihat di halaman Event SEMA.

### Q: Saya ingin mengusulkan ide program kerja?

**A:** Hubungi kami melalui form kontak di website atau datang langsung ke kantor SEMA (Gedung Bonaventura Lt. 4).

### Q: Bagaimana cara menjadi admin website?

**A:** Hubungi tim KOMINFO SEMA untuk mendapatkan akses admin.

### Q: Apakah ada API untuk integrasi dengan sistem lain?

**A:** Saat ini tidak ada. Hubungi KOMINFO untuk kebutuhan khusus.

### Q: Bagaimana jika menemukan bug atau error?

**A:** Laporkan ke KOMINFO SEMA melalui email atau datang langsung ke kantor.

## Kontak & Informasi

**📍 Lokasi:** Gedung Bonaventura Lt. 4, Kampus FTI UAJY  
**📧 Email:** sema@fti.uajy.ac.id  
**📱 Telepon:** +62 812 3456 7890  
**📱 Instagram:** [@semafti.uajy](https://instagram.com)  
**💬 Line:** @semafti

## Tim Pengembang

**Dikembangkan oleh:** KOMINFO SEMA FTI UAJY  
**Terakhir Diperbarui:** May 2026  
**Versi:** 2.0 (Minimal Clean Design)

---

## Design Principles

Website SEMA dirancang dengan filosofi:

- **Clean** - Antarmuka yang bersih dan terorganisir
- **Minimal** - Fokus pada konten, tidak ada elemen yang berlebihan
- **Accessible** - Mudah diakses dari berbagai perangkat
- **Fast** - Loading cepat dan responsif
- **Modern** - Teknologi terkini dengan design kontemporer

## Changelog

### v2.0 - May 2026

- ✨ Redesign hero section dengan full-width auto-sliding images
- 🎨 Simplifikasi color palette (single accent color)
- 🔤 Ubah typography ke sans-serif only (Space Grotesk)
- ✅ Hapus glassmorphism effects
- 🧹 Cleanup HTML dan CSS untuk performa lebih baik

### v1.0 - Previous

- 🎯 Initial website launch
- 📱 Responsive design implementation
- 🔐 Admin authentication system

---

**Terima kasih telah mengunjungi SEMA FTI UAJY!** 🎓

Untuk informasi lebih lanjut, hubungi tim SEMA kami. Mari bersama membangun komunitas FTI yang lebih baik!
