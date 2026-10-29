# Pemetaan implementasi ke DPPL InProsper

| Kebutuhan | Implementasi starter |
|---|---|
| SKPL-F-001 Verifikasi Merchant | Registrasi merchant, lampiran dokumen, keputusan admin |
| SKPL-F-002 Manajemen Listing | Form tambah/edit/nonaktif produk dan validasi diskon 30–70% |
| SKPL-F-003 Pemesanan | Checkout satu produk per pesanan (sehingga satu merchant per order), self-pickup/delivery, pembayaran simulasi |
| SKPL-F-004 Autentikasi | Login/logout dan registrasi konsumen/merchant berbasis role |
| SKPL-F-005 Pencarian/Filter | Kata kunci, kategori, harga maksimum, urutan kedaluwarsa atau harga |
| SKPL-F-006 Riwayat | Daftar transaksi konsumen dan pesanan merchant |
| SKPL-F-007 Pembatalan | Pembatalan terbatas sebelum status `diproses`; refund hanya simulasi |
| SKPL-F-008 Notifikasi | Notifikasi in-app tersimpan dalam tabel `notifications` |
| SKPL-F-009 Dashboard Admin | Metrik merchant, transaksi, nilai transaksi, dan jumlah item yang terselamatkan |

## Design decisions / limitations

- PHP native dipakai sebagai pilihan awal untuk memudahkan praktikum; DPPL belum menetapkan bahasa/framework/DBMS secara spesifik.
- Repository memisahkan akses data, Service mengoordinasikan proses order, dan controller mengelola permintaan.
- Pemilihan self-pickup/delivery tersedia. Payment gateway dan kurir belum terhubung karena vendor/API belum ditentukan dalam dokumen.
- Checkout pada starter ini dibuat untuk satu produk per order; DPPL mengharuskan validasi single-merchant, sehingga ini memenuhi kasus satu merchant per pesanan namun belum mendukung keranjang multi-produk.
- Skema menambahkan atribut implementasi yang diperlukan oleh wireframe tetapi belum lengkap di kamus data awal, misalnya `category`, `document_url`, `rejection_reason`, `expiry_time`, `destination_address`, dan `transactions.expires_at` untuk masa berlaku pembayaran 15 menit.


Status fulfillment baru berubah menjadi `siap_diambil` atau `dalam_pengantaran` ketika merchant memproses pesanan; pembayaran berhasil saja menempatkan pesanan pada status `dibayar`.
