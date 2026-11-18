# SIMALAB - Sistem Informasi Manajemen Laboratorium

SIMALAB adalah aplikasi web untuk mengelola peminjaman alat laboratorium. Peminjam dapat melihat alat yang tersedia dan mengajukan peminjaman secara online, sedangkan admin dapat memantau alat, peminjaman, pengembalian, dan laporan dari satu dashboard.

## Fitur

### Admin
- Dashboard ringkasan: total alat, total peminjam, peminjaman aktif, dan alat yang sudah dikembalikan
- Kelola data alat laboratorium
- Lihat dan hapus data peminjaman (stok alat otomatis dikembalikan jika peminjaman masih berjalan)
- Kelola data pengembalian
- Laporan peminjaman
- Profil dan logout

### Peminjam
- Dashboard dengan ringkasan aktivitas
- Lihat daftar alat yang tersedia
- Ajukan peminjaman alat (nama, NIM, kelas, nomor HP, alat, jumlah, tanggal pinjam, dan rencana kembali)
- Lihat riwayat peminjaman
- Pengembalian alat dan profil

### Lainnya
- Login dengan pembagian hak akses (admin dan peminjam)
- Stok alat berkurang otomatis saat peminjaman diajukan
- Validasi stok dan tanggal (tanggal kembali tidak boleh sebelum tanggal pinjam)
- Proses simpan data memakai transaksi database, sehingga data tetap konsisten jika terjadi kegagalan
- Status peminjaman: Dipinjam, Terlambat, dan Dikembalikan

## Teknologi

- PHP (MySQLi)
- MySQL / MariaDB
- HTML dan CSS
- Bootstrap 5.3 (halaman admin)

## Tampilan



## Kebutuhan Sistem

- PHP 7.4 atau lebih baru
- MySQL atau MariaDB
- Web server lokal, misalnya XAMPP atau Laragon

## Cara Instalasi

1. **Salin proyek**
   Letakkan folder proyek di `htdocs` (XAMPP) atau `www` (Laragon).

2. **Buat database**
   Buka phpMyAdmin, buat database baru, lalu impor file SQL proyek (misalnya `database.sql`).

3. **Atur koneksi database**
   Buka `config/koneksi.php` dan sesuaikan dengan pengaturan Anda:

   ```php
   $connect = mysqli_connect("localhost", "root", "", "nama_database");
   ```

4. **Jalankan aplikasi**
   Nyalakan Apache dan MySQL, lalu buka di browser:

   ```
   http://localhost/nama-folder-proyek/auth/login.php
   ```

## Akun Contoh

|   Peran  | Username | Password |
|----------|----------|----------|
| Admin    |  superadmin_baru  | lab2026 |
| Peminjam | 123250901| 123456   |


## Struktur Folder

```
simalab/
├── admin/                   # Halaman admin
│   ├── alat.php
│   ├── index.php
│   ├── laporan.php
│   ├── peminjaman.php
│   ├── pengembalian.php
|   ├── sidebar.php
│   └── profil.php
├── auth/                    # Autentikasi
│   ├── login.php
│   ├── logout.php
│   └── register.php
├── config/
│   └── koneksi.php          # Koneksi database
├── user/                    # Halaman peminjam
│   ├── ajukan_pengembalian.php
│   ├── data_alat.php
│   ├── index.php
│   ├── peminjaman.php
│   ├── pengembalian.php
│   ├── profil.php
│   ├── riwayat.php
│   ├── sidebar.php
│   └── user.css             # CSS halaman peminjam
├── lab_management           # MYSQL database
├── style.css                # CSS halaman admin
└── README.md
```


## Struktur Database

|       Tabel         |                      Fungsi                               |
|---------------------|-----------------------------------------------------------|
| `admin`             | Akun administrator                                        |
| `kategori`          | Kategori alat (Alat Ukur, Alat Elektronik, dan lain-lain) |
| `alat`              | Data alat laboratorium beserta stoknya                    |
| `peminjam`          | Data peminjam (mahasiswa)                                 |
| `peminjaman`        | Transaksi peminjaman                                      |
| `detail_peminjaman` | Alat dan jumlah yang dipinjam pada tiap transaksi         |

## Alur Peminjaman

1. Peminjam mengisi formulir pengajuan.
2. Sistem memeriksa stok alat.
3. Jika stok cukup, stok dikurangi dan data peminjaman disimpan.
4. Admin memantau peminjaman di halaman Data Peminjaman.
5. Saat dikembalikan, status berubah menjadi Dikembalikan.

## Pengembang

- Nama: Cut Guspona Zadilla, Anisya Awalia, Marina Diah Ayu Puspitasari
- Kontak: cutzadilla, anisyaawalia710-beep, MarinaDiahAyu

## Lisensi

Proyek ini dibuat untuk keperluan projek kuliah.