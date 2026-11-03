# Template Test Case – InProsper

Kelompok 7 – PPPL Kelas A

Dokumen ini adalah template test case untuk pengujian sistem InProsper. Setiap skenario mengacu pada Sequence Diagram (SEQ) di DPPL. Kolom **Hasil Aktual** dan **Status** diisi saat pengujian dijalankan.

## Petunjuk Pengisian

- **ID**: kode unik, format `TC-XXX`.
- **Kebutuhan**: kode kebutuhan SKPL (SKPL-F / SKPL-B) yang diuji. Samakan dengan Bab 6 DPPL (traceability matrix).
- **Skenario**: situasi yang diuji, satu skenario per baris.
- **Langkah**: langkah pengujian berurutan.
- **Hasil Diharapkan**: perilaku sistem yang benar menurut DPPL.
- **Hasil Aktual**: apa yang terjadi saat pengujian (kosongkan sebelum diuji).
- **Status**: `Lulus`, `Gagal`, atau `Belum diuji`.

## Tabel Test Case

| ID | Kebutuhan | Skenario | Langkah | Hasil Diharapkan | Hasil Aktual | Status |
|----|-----------|----------|---------|------------------|--------------|--------|
| TC-001 | SKPL-F-xx (SEQ-004) | Login dengan password salah | 1. Buka halaman login (UI-001).<br>2. Isi email dengan akun yang terdaftar.<br>3. Isi password yang salah.<br>4. Klik tombol Login. | Login ditolak, sistem menampilkan pesan kesalahan, dan pengguna tetap di halaman login tanpa sesi aktif. | | Belum diuji |
| TC-002 | SKPL-F-xx / SKPL-B-xx (SEQ-002) | Listing produk dengan diskon di luar rentang 30–70% | 1. Login sebagai Merchant terverifikasi.<br>2. Buka form tambah produk.<br>3. Isi data produk dengan diskon di luar 30–70% (misalnya 20% atau 80%).<br>4. Klik Simpan. | Produk tidak tersimpan, sistem menampilkan pesan bahwa diskon harus berada di rentang 30–70%. | | Belum diuji |
| TC-003 | SKPL-F-xx (SEQ-003) | Checkout dengan stok tidak cukup | 1. Login sebagai pembeli.<br>2. Tambahkan produk ke keranjang dengan jumlah melebihi stok yang tersedia.<br>3. Lanjutkan ke checkout. | Pesanan tidak dibuat, sistem menampilkan pesan bahwa stok tidak mencukupi, dan stok produk tidak berubah. | | Belum diuji |