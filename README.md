# Medical Inventory System UMPKU — Final

Aplikasi CRUD inventaris alat elektromedis menggunakan PHP Native, MySQL/MariaDB, HTML, CSS, dan Chart.js.

## Isi fitur
- Login, register, logout.
- Password disimpan menggunakan `password_hash()` dan diverifikasi dengan `password_verify()`.
- Session authentication dan proteksi halaman.
- Dashboard ringkasan inventaris dan grafik.
- Tambah, edit, hapus, dan pencarian data alat.
- Validasi input, prepared statements, dan token CSRF untuk operasi formulir.
- Tampilan responsif desktop dan mobile.

## Instalasi XAMPP
1. Ekstrak folder `medical_inventory_umpku_final` ke `C:\xampp\htdocs\`.
2. Jalankan Apache dan MySQL dari XAMPP Control Panel.
3. Buka `http://localhost/phpmyadmin`.
4. Pilih menu **Import**, lalu impor file `database.sql`.
5. Pastikan pengaturan database pada `config.php` sesuai:
   - host: `localhost`
   - user: `root`
   - password: kosong (default XAMPP)
   - database: `db_alat_elektromedis`
6. Buka `http://localhost/medical_inventory_umpku_final/`.
7. Pilih **Daftar sekarang** untuk membuat akun pertama, lalu login.

## Catatan
- SQL membuat database `db_alat_elektromedis`, tabel `alat_elektromedis`, dan tabel `users`.
- Lima data inventaris awal mengikuti data pada SQL yang diberikan.
- Jika database lama sudah memiliki data penting, lakukan backup terlebih dahulu sebelum mengimpor SQL.
- Ubah kredensial database di `config.php` jika konfigurasi MySQL lokal berbeda.
- Chart.js dan Google Fonts dimuat dari CDN, sehingga grafik dan font eksternal memerlukan koneksi internet. Fitur CRUD dan autentikasi berjalan di PHP/MySQL lokal.
