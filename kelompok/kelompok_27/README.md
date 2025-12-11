# Website Penjualan dan Pengelolaan Dana Usaha pada Koperasi Himpunan Mahasiswa

## 👥 Kelompok 27

| No | Nama | NIM | Role |
|:--:|------|-----|------|
| 1 | Amripin Sukma Braji | 2315061090 | Backend Developer |
| 2 | MUHAMMAD FARHAN | 2315061083 | Frontend Developer |
| 3 | Raissa Syahputra | 2315061106 | Database Designer |
| 4 | Febby Yolanda Putri | 2315061003 | UI/UX Designer |

## Deskripsi Singkat
Aplikasi Point of Sales (POS) berbasis web yang dirancang khusus untuk mengelola transaksi dan manajemen penitipan barang jual pada Koperasi Himpunan Mahasiswa.Sistem ini hadir untuk menyelesaikan masalah perhitungan bagi hasil manual yang rentan salah, pencatatan transaksi non-tunai yang tidak rapi, serta risiko integritas data akibat penghapusan barang secara permanen.

## 📋 Persyaratan Sistem

### Teknologi yang Digunakan
| Komponen | Spesifikasi | Versi Minimum |
|----------|-------------|---------------|
| **Server** | Apache | 2.4+ |
| **PHP** | PHP Native | 7.4+ |
| **Database** | MySQL | 5.7+ |
| **Frontend** | HTML5, CSS3, JavaScript Native | - |
| **Version Control** | Git | 2.0+ |

## 🚀 Fitur Aplikasi

### 1. User Management
- ✅ **Login** - Autentikasi dengan username dan password
- ✅ **Logout** - Keluar dari sistem dengan aman
- ✅ **Role / Hak Akses** - Admin dan Staff dengan hak berbeda

### 2. Manajemen Master Data
- ✅ **Master Barang** - CRUD barang, stok, dan harga
- ✅ **Master Supplier** - CRUD supplier/penitip barang
- ✅ **Master User** - CRUD pengguna sistem

### 3. Fitur Transaksi
- ✅ **Penjualan Barang** - Input transaksi dengan detail item
- ✅ **Validasi Data** - Cek stok, validasi harga, validasi qty
- ✅ **Nomor Faktur Otomatis** - Format TRX-XXXXX yang terurut
- ✅ **Metode Pembayaran** - Cash dan QRIS

### 4. Pelaporan & Analisis
- ✅ **Laporan Keuangan** - Omzet, profit, dan bagi hasil supplier
- ✅ **Laporan Harian** - Laporan transaksi per hari
- ✅ **Rekap Supplier** - Tagihan dan hak supplier

## 📦 Struktur Folder

```
kelompok_27/
├── database/
│   └── posma_db.sql          # File database
├── src/
│   ├── config/
│   │   └── koneksi.php        # Konfigurasi database
│   ├── layout/
│   │   ├── header.php         # Header template
│   │   ├── footer.php         # Footer template
│   │   └── sidebar.php        # Sidebar navigation
│   ├── assets/
│   │   └── css/
│   │       └── style.css      # Custom styling
│   ├── admin/
│   │   ├── dashboard.php      # Dashboard admin
│   │   ├── laporan_keuangan.php
│   │   ├── master_barang.php
│   │   ├── master_supplier.php
│   │   └── master_user.php
│   ├── staff/
│   │   ├── dashboard.php      # Dashboard staff
│   │   ├── laporan_harian.php
│   │   └── transaksi.php      # Halaman transaksi
│   ├── proses/
│   │   ├── login_proses.php
│   │   ├── barang_proses.php
│   │   ├── supplier_proses.php
│   │   ├── user_proses.php
│   │   └── transaksi_proses.php
│   ├── index.php              # Halaman login
│   ├── logout.php             # Logout handler
│   └── generate_pass.php      # Generate password
└── README.md                  # Dokumentasi

```

## 🔧 Instalasi & Cara Menjalankan

### Prasyarat
- PHP 7.4+ sudah terinstall
- MySQL Server berjalan
- Web Server (Apache) aktif
- Git terinstall

### Langkah Instalasi

#### 1. **Clone Repository**
```bash
git clone https://github.com/muhparhaan/TUBES_PRK_PEMWEB_2025.git
cd TUBES_PRK_PEMWEB_2025/kelompok/kelompok_27
```

#### 2. **Setup Database**
```bash
# Buka MySQL client atau phpMyAdmin
mysql -u root -p

# Buat database baru (jika belum ada)
CREATE DATABASE posma_db;

# Import file SQL
USE posma_db;
SOURCE database/posma_db.sql;
```

**Atau menggunakan phpMyAdmin:**
- Buka http://localhost/phpmyadmin
- Buat database baru dengan nama `posma_db`
- Import file `database/posma_db.sql`

#### 3. **Konfigurasi Database**
Edit file `src/config/koneksi.php`:
```php
<?php
$host = "localhost";
$user = "root"; // Sesuaikan dengan user MySQL Anda
$password = ""; // Sesuaikan dengan password MySQL Anda
$db = "posma_db";

$conn = mysqli_connect($host, $user, $password, $db);
?>
```

#### 4. **Jalankan Aplikasi**
```bash
# Jika menggunakan Laragon (Windows)
- Pastikan folder berada di D:\laragon\www\TUBES_PRK_PEMWEB_2025
- Akses di browser: http://localhost/TUBES_PRK_PEMWEB_2025/kelompok/kelompok_27/src/
```

#### 5. **Generate Password Untuk User Baru**
Untuk membuat password yang terenkripsi dengan benar:

1. **Akses halaman Generate Password**
   ```
   http://localhost/TUBES_PRK_PEMWEB_2025/kelompok/kelompok_27/src/generate_pass.php
   ```

2. **Generate Password**
   - Klik tombol "Generate" untuk membuat password terenkripsi baru
   - Copy hasil hash password yang ditampilkan

3. **Update Password di Database**
   - Buka phpMyAdmin: http://localhost/phpmyadmin
   - Pilih database `posma_db`
   - Buka tabel `users`
   - Edit user yang ingin diubah passwordnya
   - Paste hash password hasil generate ke kolom `password`
   - Klik Save


> ⚠️ **PENTING**: 
> - Password default sudah ter-hash di database
> - Untuk mengganti password, gunakan `generate_pass.php` lalu update di phpMyAdmin
> - JANGAN paste password plain text langsung ke database!

## 📚 Dokumentasi Singkat

### Alur Kerja Sistem

#### 1. **Admin Dashboard**
- Melihat ringkasan data: omzet hari ini, total supplier, total produk
- Akses menu Master Data, Laporan, dan Manajemen User

#### 2. **Master Data Management**
- **Master Barang**: Tambah, edit, hapus barang dengan stok dan harga
- **Master Supplier**: Kelola supplier/penitip dengan kategori (internal/eksternal)
- **Master User**: Kelola akun pengguna dengan role (admin/staff)

#### 3. **Proses Transaksi**
- Staff membuka menu "Transaksi"
- Pilih barang yang akan dijual dan masukkan qty
- Sistem otomatis menghitung subtotal
- Validasi stok dilakukan sebelum checkout
- Proses pembayaran (cash/QRIS)
- Nomor faktur auto-generate: **TRX-00001**

#### 4. **Laporan **
- **Admin** dapat melihat laporan keuangan lengkap dengan breakdown:
  - Omzet total
  - Profit bersih koperasi (dari penjualan milik koperasi + fee titipan)
  - Rekap tagihan supplier
- **Staff** dapat melihat laporan harian penjualan

### Perhitungan Profit
```
Profit Bersih Koperasi = 
  (Omzet Barang Milik Koperasi) + 
  (10% × Omzet Supplier Internal) + 
  (15% × Omzet Supplier Eksternal)
```

### Fitur Keamanan
- ✅ Validasi login dengan session
- ✅ Role-based access control
- ✅ Password hashing (password_hash PHP)
- ✅ Prepared statement untuk prevent SQL injection
- ✅ Transaction safety saat delete data dengan relasi

## 🖼️ Screenshot Aplikasi

### Halaman Login
<img width="1915" height="1024" alt="image" src="https://github.com/user-attachments/assets/16d50dd0-34e5-476c-923e-7de16a06408b" />

### Admin Dashboard
<img width="1919" height="1048" alt="image" src="https://github.com/user-attachments/assets/cdc953aa-b7a7-40b5-bfdf-9062be8f86fe" />

### Staff Dashboard
<img width="1913" height="1030" alt="image" src="https://github.com/user-attachments/assets/1d55d01e-dd65-4a69-a3eb-df39f2b8f1c2" />
 
### Master Barang
<img width="1918" height="1035" alt="image" src="https://github.com/user-attachments/assets/5627f77d-b382-487e-ae4c-89090fa4ef06" />


### Master Supplier
<img width="1914" height="1035" alt="image" src="https://github.com/user-attachments/assets/ba709af8-f58e-46b0-a1d3-62494fe5f7ce" />

### Master User
<img width="1910" height="1039" alt="image" src="https://github.com/user-attachments/assets/72ee09e8-dc38-4f0b-bf89-5b77baed7ea8" />


### Transaksi 
<img width="1919" height="1041" alt="image" src="https://github.com/user-attachments/assets/3d89b382-2eca-42a9-80f0-7d06b77e9b5a" />


### Laporan Keuangan (Admin)
<img width="1919" height="1036" alt="image" src="https://github.com/user-attachments/assets/7feb4739-73f5-4c59-83ba-bfa5c6c2c3c7" />
<img width="1918" height="1040" alt="image" src="https://github.com/user-attachments/assets/55618332-4045-4cce-b5c9-63836c7507ad" />

### Laporan Hari ini (Staff)'
<img width="1912" height="1040" alt="image" src="https://github.com/user-attachments/assets/cbbf4a20-7d96-47cf-b2e0-6649d13900af" />
<img width="1915" height="1037" alt="image" src="https://github.com/user-attachments/assets/bd87b864-7109-468c-b0ec-4a7267cc5757" />


## Entity Relationship Diagram (ERD)
<img width="1302" height="707" alt="image" src="https://github.com/user-attachments/assets/0849d844-8fa2-4df4-9341-a2cb826d3a3a" />


## 📝 Catatan Penting

- Aplikasi ini menggunakan **PHP Native** tanpa framework
- Database menggunakan **MySQL** dengan struktur relasional
- Semua styling menggunakan **Bootstrap 5.3.0** dan custom CSS
- JavaScript bersifat **Native** tanpa jQuery atau framework lain
- Semua kode mengikuti standar **PHP PSR-12** untuk konsistensi


## 📄 Lisensi

Proyek ini dibuat untuk keperluan akademis - Tugas Besar Kelompok 27 Pemrograman Web.

---

**Terakhir diupdate**: December 2025
