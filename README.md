# InProsper — Food Rescue & Food Waste Prevention

Starter implementasi berbasis PHP native + MySQL yang diturunkan dari DPPL Kelompok 7. Struktur dibuat menggunakan controller, repository, service, dan view secara sederhana agar bisa dikembangkan bertahap.

## Fitur pada starter

- Registrasi dan login berbasis peran: konsumen, merchant, admin.
- Merchant dapat mengajukan verifikasi dengan unggah dokumen; admin dapat menyetujui atau menolak pengajuan.
- Katalog makanan surplus dengan pencarian, filter kategori, batas harga, urutan tanggal kedaluwarsa, dan detail produk.
- Merchant terverifikasi dapat menambah, memperbarui, dan menonaktifkan listing.
- Validasi diskon 30–70%, stok tidak negatif, dan batas layak konsumsi harus di masa depan.
- Checkout self-pickup/delivery, simulasi status pembayaran, riwayat pesanan, dan pembatalan sebelum pesanan diproses.
- Stok dikurangi saat pembayaran simulasi berhasil; stok dikembalikan ketika pesanan berbayar dibatalkan sebelum diproses.
- Notifikasi in-app, dashboard merchant, dan dashboard monitoring admin.
- Estimasi komisi platform 15% dihitung dari transaksi berhasil.

## Persiapan di Windows / Laragon

1. Pastikan PHP 8.1+ dan MySQL/MariaDB aktif.
2. Salin `config/database.example.php` menjadi `config/database.local.php`, lalu sesuaikan host, port, nama database, username, dan password. File lokal ini diabaikan oleh Git.
3. Buka phpMyAdmin, lalu impor `database/schema.sql`. Skrip membuat database `inprosper_db` serta tabel sesuai rancangan DPPL yang dilengkapi beberapa atribut implementasi seperti kategori, nomor telepon, waktu batas konsumsi, alamat pengiriman, flag listing aktif, serta batas pembayaran 15 menit agar reservasi stok kedaluwarsa tidak mengunci stok selamanya.
4. Dari terminal di folder proyek, jalankan `php database/seed.php` untuk membuat akun demo dan data produk contoh.
5. Jalankan `php -S localhost:8000` dan buka `http://localhost:8000`.

## Persiapan Lokal (XAMPP)

Alternatif dari panduan Laragon di atas untuk yang memakai XAMPP. Perintah proyeknya sama; yang berbeda hanya cara menjalankan MySQL dan PHP.

## Prasyarat

- [XAMPP](https://www.apachefriends.org/) dengan PHP 8.1 atau lebih baru
- [Git](https://git-scm.com/)

## Langkah-langkah

1. **Jalankan MySQL**

   Buka XAMPP Control Panel, lalu klik **Start** pada modul **MySQL**. Apache tidak wajib karena aplikasi dijalankan dengan server bawaan PHP (langkah 5).

2. **Pastikan perintah `php` bisa dipakai di terminal**

   ```bash
   php -v
   ```

   Jika perintah tidak dikenali, tambahkan `C:\xampp\php` ke environment variable `Path` Windows, lalu buka ulang terminal. Pastikan versinya 8.1 atau lebih baru.

3. **Siapkan konfigurasi database**

   Salin `config/database.example.php` menjadi `config/database.local.php`, lalu sesuaikan. Pada XAMPP bawaan: host `localhost`, port `3306`, username `root`, password kosong. File lokal ini diabaikan oleh Git sehingga kredensial tidak ikut terunggah.

4. **Impor skema dan data contoh**

   Buka `http://localhost/phpmyadmin`, pilih tab **Import**, lalu pilih `database/schema.sql` dan klik **Import**. Skrip ini otomatis membuat database `inprosper_db` beserta tabelnya. Setelah itu, dari terminal di folder proyek:

   ```bash
   php database/seed.php
   ```

5. **Jalankan aplikasi**

   ```bash
   php -S localhost:8000
   ```

   Lalu buka `http://localhost:8000`.

## Pemecahan Masalah

- **MySQL tidak mau start:** port 3306 mungkin dipakai layanan MySQL lain. Hentikan layanan tersebut atau ubah port MySQL di XAMPP (dan samakan di `config/database.local.php`).
- **Koneksi database gagal:** periksa host, port, username, password, dan nama database di `config/database.local.php`, dan pastikan MySQL sedang berjalan.
- **`php` tidak dikenali:** lihat langkah 2.
- **Port 8000 sudah terpakai:** jalankan dengan port lain, misalnya `php -S localhost:8080`.

## Akun demo

- Admin: `admin@inprosper.test` / `Admin123!`
- Merchant terverifikasi: `merchant@inprosper.test` / `Merchant123!`
- Konsumen: `consumer@inprosper.test` / `Consumer123!`

Ubah password demo sebelum deployment atau penggunaan di lingkungan bersama.

## Catatan penting: batas prototipe

DPPL mendeskripsikan integrasi REST API/HTTPS dan webhook untuk payment gateway serta layanan kurir, tetapi belum menetapkan vendor/API. Starter ini **tidak memproses pembayaran sungguhan dan tidak memesan kurir sungguhan**. Tombol pembayaran hanya mensimulasikan sukses/gagal; tarif dan tracking kurir belum terhubung. Adapter/pola integrasi nyata sebaiknya dibuat setelah kelompok menentukan layanan eksternal dan kredensial pengujian.

Beberapa atribut tambahan—misalnya kategori produk, file dokumen merchant, alasan penolakan, waktu kedaluwarsa, dan alamat tujuan—dipakai agar alur UI dalam DPPL dapat dijalankan meskipun tidak seluruhnya muncul di tabel kamus data awal. Estimasi makanan terselamatkan saat ini dihitung sebagai jumlah item dari pesanan yang berstatus dibayar/diproses/selesai, bukan estimasi berat.

## Workflow Git untuk praktikum Modul 2

Setiap anggota sebaiknya mengambil tugas yang berbeda, mengubah bagian yang disepakati, lalu melakukan stage, commit, dan push dengan pesan commit yang spesifik. Jangan menganggap semua fungsi sudah selesai diuji hanya karena file tersedia; uji alur login, validasi diskon, listing merchant, pembayaran simulasi, pembatalan, notifikasi, dan verifikasi admin.
