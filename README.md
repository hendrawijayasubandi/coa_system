# COA System

## Teknologi yang Digunakan
- Laravel 12 (PHP 8.2)
- Bootstrap Authentication
- jQuery DataTables (Server-side)
- MySQL
- Laravel Excel
- Yajra DataTables

## Instalasi
Ikuti langkah-langkah berikut untuk menginstal dan menjalankan proyek ini di lingkungan pengembangan Anda.

### 1. Clone Repository
```bash
git clone https://github.com/hendrawijayasubandi/coa_system.git
cd coa_system
```

### 2. Install Dependency
Pastikan Anda telah menginstal Composer dan Node.js, kemudian jalankan:
```bash
composer install
npm install
```

### 3. Konfigurasi Environment
Salin file `.env.example` menjadi `.env` dan sesuaikan konfigurasi database:
```bash
cp .env.example .env
```
Edit file `.env` dan atur kredensial database:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=coa_system
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Key dan Migrasi Database
```bash
php artisan key:generate
php artisan migrate --seed
```

### 5. Menjalankan Aplikasi
Untuk menjalankan aplikasi, buka dua terminal. Pada terminal pertama, jalankan perintah berikut untuk menjalankan Vite:
```bash
npm run dev
```
Pada terminal kedua, jalankan perintah berikut untuk menjalankan server Laravel:
```bash
php artisan serve
```
Akses aplikasi di browser: `http://127.0.0.1:8000`

## Fitur
- Authentication (Register, Login, Logout) menggunakan Bootstrap Authentication.
- Manajemen Kategori, Chart of Account (COA), Transaksi, dan Profit/Loss.
- Laporan Profit/Loss dengan Export ke Excel menggunakan Yajra DataTables.
- Semua fitur CRUD menggunakan AJAX untuk meningkatkan pengalaman pengguna dan efisiensi data.
- DataTable server-side untuk efisiensi data menggunakan fungsi AJAX.

## Kontributor
- **Hendra** - [GitHub](https://github.com/hendrawijayasubandi)

## Lisensi
Proyek ini dilisensikan di bawah MIT License - lihat file [LICENSE](LICENSE) untuk detail lebih lanjut.