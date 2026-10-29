# Checklist pengujian awal InProsper

- [ ] Login dengan akun valid dan invalid.
- [ ] Merchant yang masih menunggu verifikasi tidak dapat berjualan.
- [ ] Admin dapat menyetujui pengajuan merchant; akun tersebut kemudian dapat login.
- [ ] Admin dapat menolak pengajuan merchant dengan alasan.
- [ ] Diskon 29% dan 71% ditolak; diskon 30%, 40%, dan 70% diterima.
- [ ] Produk dengan tanggal/waktu kedaluwarsa yang sudah lewat tidak dapat diterbitkan / dipesan.
- [ ] Produk hanya tampil di katalog jika merchant disetujui, listing aktif, stok positif, dan belum kedaluwarsa.
- [ ] Konsumen dapat checkout dengan self-pickup dan delivery.
- [ ] Pembayaran simulasi gagal membuat pesanan dibatalkan dan stok tidak berkurang.
- [ ] Pembayaran simulasi berhasil menurunkan stok dan membuat notifikasi untuk konsumen serta merchant.
- [ ] Pesanan dapat dibatalkan ketika masih menunggu pembayaran atau sudah dibayar, sesuai prototipe; pesanan yang telah diproses tidak dapat dibatalkan.
- [ ] Merchant dapat memproses pesanan dan menandai pesanan selesai.
- [ ] Konsumen/merchant melihat notifikasi sesuai perannya.
- [ ] Dashboard admin menampilkan jumlah merchant terverifikasi, transaksi sukses, estimasi porsi terselamatkan, dan nilai transaksi.
- [ ] Uji akses: konsumen tidak dapat membuka halaman admin/merchant melalui URL langsung.
