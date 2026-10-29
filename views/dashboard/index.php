<section class="hero dashboard-hero">
    <div class="hero-copy">
        <span class="eyebrow">INPROSPER PLATFORM · FOOD RESCUE &amp; FOOD WASTE PREVENTION</span>
        <h1>Selamatkan surplus.<br><em>Cegah food waste bersama.</em></h1>
        <p>InProsper adalah platform marketplace B2B2C yang menghubungkan pelaku usaha kuliner (Merchant) dengan masyarakat (Konsumen) untuk menyelamatkan makanan layak konsumsi dengan potongan harga 30%–70%.</p>
        <div class="hero-points">
            <span>✓ Diskon 30%–70% dari harga normal</span>
            <span>✓ Ambil Mandiri (Self-Pickup) atau Kurir Logistik</span>
            <span>✓ Mitra Toko Terverifikasi</span>
            <span>✓ Transaksi Terpadu &amp; Notifikasi In-App</span>
        </div>
        <div style="margin-top:24px;display:flex;gap:12px;flex-wrap:wrap;">
            <a class="btn btn-primary" href="?page=catalog">Jelajahi Katalog Makanan ↘</a>
            <a class="btn btn-outline" href="?page=register">Daftar Akun / Mitra Usaha</a>
        </div>
    </div>
    <div class="hero-art">
        <div class="sun"></div>
        <div class="plate">
            <span class="food food-one">🍱</span>
            <span class="food food-two">🥐</span>
            <span class="food food-three">🥑</span>
            <span class="food food-four">🥗</span>
        </div>
        <div class="floating-tag">
            <strong><?= (int)$rescued_meals ?> porsi</strong>
            <span>makanan terselamatkan</span>
        </div>
    </div>
</section>

<!-- METRIK STATISTIK UTAMA (FR-09 / SKPL-F-009) -->
<section class="section">
    <div class="section-heading">
        <div>
            <span class="eyebrow">RINGKASAN EKOSISTEM · SKPL-F-009</span>
            <h2>Dampak Penyelamatan Pangan</h2>
            <p class="muted">Statistik operasional dan dampak lingkungan yang tercatat pada sistem InProsper.</p>
        </div>
        <span class="count-pill">Live Platform Overview</span>
    </div>

    <div class="metrics-grid">
        <div class="metric-card">
            <span>Makanan Terselamatkan</span>
            <strong><?= (int)$rescued_meals ?> <small>porsi</small></strong>
            <small>Surplus pangan yang berhasil diselamatkan dari limbah</small>
        </div>
        <div class="metric-card">
            <span>Mitra Terverifikasi</span>
            <strong><?= (int)$verified_count ?> <small>toko</small></strong>
            <small>Merchant aktif yang telah diverifikasi oleh tim internal</small>
        </div>
        <div class="metric-card">
            <span>Produk Tersedia Hari Ini</span>
            <strong><?= (int)$total_active ?> <small>item</small></strong>
            <small>Makanan layak konsumsi dengan potongan 30%–70%</small>
        </div>
        <div class="metric-card">
            <span>Total Transaksi Berhasil</span>
            <strong><?= (int)$successful_orders ?> <small>pesanan</small></strong>
            <small>Nilai tercatat: <?= money($gross_transactions) ?></small>
        </div>
    </div>
</section>

<!-- KATALOG MAKANAN SURPLUS TERBARU (NEAR-EXPIRY HIGHLIGHTS) -->
<section class="section">
    <div class="section-heading">
        <div>
            <span class="eyebrow">PRODUK NEAR-EXPIRY · SKPL-F-002 &amp; SKPL-F-005</span>
            <h2>Makanan Surplus Siap Diselamatkan</h2>
            <p class="muted">Menu berkualitas prima yang mendekati batas waktu konsumsi hari ini.</p>
        </div>
        <a href="?page=catalog" class="btn btn-quiet">Lihat Semua di Katalog →</a>
    </div>

    <?php if (empty($featured_products)): ?>
        <div class="empty-state compact">
            <div class="empty-icon">🍱</div>
            <h3>Belum ada makanan surplus yang terbit</h3>
            <p>Pastikan database sudah terisi sampel data atau login sebagai Merchant untuk menambahkan listing.</p>
            <a href="?page=catalog" class="btn btn-outline btn-small">Buka Halaman Katalog</a>
        </div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($featured_products as $p): $price = discount_price($p); ?>
                <article class="product-card">
                    <a class="product-photo" href="?page=product&id=<?= (int)$p['product_id'] ?>">
                        <?php if ($p['photo_url']): ?>
                            <img src="<?= e($p['photo_url']) ?>" alt="<?= e($p['name']) ?>">
                        <?php else: ?>
                            <div class="photo-placeholder">
                                <span><?= e(match($p['category']){'Roti & bakery'=>'🥐','Camilan'=>'🍪','Minuman'=>'🥤','Buah & sayur'=>'🥑',default=>'🍱'}) ?></span>
                            </div>
                        <?php endif; ?>
                        <span class="discount-badge">-<?= (int)$p['discount_percent'] ?>%</span>
                    </a>
                    <div class="product-info">
                        <span class="category-label"><?= e($p['category']) ?></span>
                        <h3><a href="?page=product&id=<?= (int)$p['product_id'] ?>"><?= e($p['name']) ?></a></h3>
                        <p class="merchant-name">⌂ <?= e($p['business_name']) ?></p>
                        <div class="prices">
                            <span class="sale-price"><?= money($price) ?></span>
                            <del><?= money($p['normal_price']) ?></del>
                        </div>
                        <div class="product-meta">
                            <span>Sisa Stok: <?= (int)$p['stock'] ?></span>
                            <span>Batas: <?= e(date('d M', strtotime($p['expiry_date']))) ?><?= !empty($p['expiry_time']) ? ' '.e(substr($p['expiry_time'], 0, 5)) : '' ?></span>
                        </div>
                        <a class="btn btn-outline btn-full" href="?page=product&id=<?= (int)$p['product_id'] ?>">Detail &amp; Pesan</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- CARA KERJA SISTEM SECARA UMUM (ALUR SDD TANPA BATASAN ROLE) -->
<section class="section">
    <div class="section-heading">
        <div>
            <span class="eyebrow">CARA KERJA INPROSPER · SDD BAB 3</span>
            <h2>Alur Penyelamatan Pangan Sederhana</h2>
            <p class="muted">Bagaimana merchant dan konsumen berkolaborasi mengurangi sampah makanan di platform.</p>
        </div>
    </div>

    <div class="metrics-grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
        <div class="panel" style="padding:22px;">
            <div style="font-size:2rem;margin-bottom:12px;">🏪</div>
            <h3 style="margin-bottom:8px;">1. Merchant Listing</h3>
            <p class="small muted" style="margin:0;">Mitra terverifikasi mengunggah makanan surplus berlebih dengan potongan diskon 30%–70% sebelum waktu konsumsi terlewat.</p>
        </div>
        <div class="panel" style="padding:22px;">
            <div style="font-size:2rem;margin-bottom:12px;">🔍</div>
            <h3 style="margin-bottom:8px;">2. Konsumen Memesan</h3>
            <p class="small muted" style="margin:0;">Konsumen memilih makanan berkualitas dengan harga terjangkau melalui katalog dan filter cerdas di sekitarnya.</p>
        </div>
        <div class="panel" style="padding:22px;">
            <div style="font-size:2rem;margin-bottom:12px;">🛵</div>
            <h3 style="margin-bottom:8px;">3. Fleksibilitas Ambil</h3>
            <p class="small muted" style="margin:0;">Didukung <strong>Self-Pickup</strong> (ambil sendiri tanpa ongkir) atau <strong>Delivery Kurir</strong> instan langsung ke rumah.</p>
        </div>
        <div class="panel" style="padding:22px;">
            <div style="font-size:2rem;margin-bottom:12px;">🌱</div>
            <h3 style="margin-bottom:8px;">4. Dampak Positif</h3>
            <p class="small muted" style="margin:0;">Makanan terselamatkan, pengeluaran lebih hemat, pendapatan penjual terselamatkan, dan emisi limbah berkurang.</p>
        </div>
    </div>
</section>

<!-- AKTIVITAS TRANSAKSI TERAKHIR (LIVE ACTIVITY FEED) -->
<section class="section">
    <div class="section-heading">
        <div>
            <span class="eyebrow">AKTIVITAS TERKINI · SKPL-F-006</span>
            <h2>Riwayat Penyelamatan Pangan</h2>
            <p class="muted">Catatan transaksi dan pesanan terbaru di dalam ekosistem InProsper.</p>
        </div>
        <span class="count-pill">Aktivitas Terkini</span>
    </div>

    <?php if (empty($recent_orders)): ?>
        <div class="panel empty-line">Belum ada pesanan tercatat di dalam sistem.</div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Konsumen</th>
                        <th>Menu Makanan</th>
                        <th>Metode Pemenuhan</th>
                        <th>Nilai Transaksi</th>
                        <th>Waktu</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_orders as $ro): ?>
                        <tr>
                            <td><strong>#ORD-<?= str_pad((string)$ro['order_id'], 4, '0', STR_PAD_LEFT) ?></strong></td>
                            <td><?= e($ro['consumer_name'] ?: 'Pengguna') ?></td>
                            <td><?= e($ro['product_names'] ?: 'Makanan Surplus') ?></td>
                            <td>
                                <span class="status status-<?= $ro['fulfillment_method'] === 'delivery' ? 'diproses' : 'disetujui' ?>">
                                    <?= $ro['fulfillment_method'] === 'delivery' ? '🛵 Pengantaran Kurir' : '🏬 Ambil Sendiri' ?>
                                </span>
                            </td>
                            <td><strong><?= money($ro['total_amount']) ?></strong></td>
                            <td><small class="muted"><?= e(date('d M Y, H:i', strtotime($ro['order_date']))) ?></small></td>
                            <td>
                                <span class="status status-<?= e($ro['status']) ?>">
                                    <?= e(str_replace('_', ' ', $ro['status'])) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<!-- JALUR AKSES PENGGUNA (ROLE ENTRY POINTS) -->
<section class="section">
    <div class="impact-strip" style="grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
        <div>
            <span class="eyebrow" style="color:#a8ddb5;">UNTUK KONSUMEN</span>
            <h3 style="color:#fff;margin:6px 0 10px;">Ingin Hemat Makan Enak?</h3>
            <p class="small" style="color:#d3e6d8;">Temukan puluhan makanan surplus di sekitar Anda dengan diskon hingga 70%.</p>
            <a href="?page=catalog" class="btn btn-small" style="background:#fff;color:var(--green);font-weight:700;margin-top:10px;">Buka Katalog →</a>
        </div>
        <div>
            <span class="eyebrow" style="color:#a8ddb5;">UNTUK MITRA USAHA</span>
            <h3 style="color:#fff;margin:6px 0 10px;">Punya Makanan Berlebih?</h3>
            <p class="small" style="color:#d3e6d8;">Ubah potensi sampah makanan menjadi pendapatan baru dan bantu sesama.</p>
            <a href="?page=register" class="btn btn-small" style="background:#fff;color:var(--green);font-weight:700;margin-top:10px;">Daftar Jadi Merchant →</a>
        </div>
        <div>
            <span class="eyebrow" style="color:#a8ddb5;">AKSES CEPAT</span>
            <h3 style="color:#fff;margin:6px 0 10px;">Sudah Punya Akun?</h3>
            <p class="small" style="color:#d3e6d8;">Masuk untuk mengelola pesanan, melihat notifikasi, atau mengelola toko.</p>
            <a href="?page=login" class="btn btn-small" style="background:#fff;color:var(--green);font-weight:700;margin-top:10px;">Masuk ke Akun →</a>
        </div>
    </div>
</section>

