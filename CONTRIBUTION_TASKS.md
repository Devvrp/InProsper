# Usulan pembagian kontribusi praktikum

Sesuaikan nama/anggota dan tugas dengan kesepakatan kelompok. Setiap anggota perlu menghasilkan perubahan yang benar-benar dibuat sendiri dan melakukan commit/push menggunakan identitas Git masing-masing.

| Area | Kandidat file | Hasil yang bisa diverifikasi |
|---|---|---|
| Autentikasi dan registrasi | `app/Controllers/AuthController.php`, `views/auth/` | Login per role, registrasi konsumen, pengajuan merchant |
| Listing dan katalog produk | `app/Controllers/ProductController.php`, `app/Repositories/ProductRepository.php`, `views/catalog/` | Validasi diskon 30–70%, cari/filter, tambah/edit produk |
| Pesanan dan status | `app/Services/OrderService.php`, `app/Controllers/OrderController.php`, `views/orders/` | Checkout, simulasi pembayaran, pembatalan, perubahan status |
| Verifikasi dan monitoring admin | `app/Controllers/AdminController.php`, `views/admin/` | Setujui/tolak merchant dan ringkasan dashboard |
| UI, pengujian, dokumentasi | `assets/`, `README.md`, `docs/` | Perbaikan UI, skenario uji, bukti screenshot dan laporan |

Gunakan commit yang spesifik, contohnya `feat: add merchant verification workflow`, `feat: validate product discount range`, atau `docs: describe local setup`. Hindari memakai pesan commit contoh jika perubahan itu tidak benar-benar dilakukan.
