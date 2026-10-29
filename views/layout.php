<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> · InProsper</title><link rel="stylesheet" href="assets/css/style.css">
<script defer src="assets/js/app.js"></script>
</head><body>
<header class="topbar"><a class="brand" href="?page=home"><span class="brand-icon">i</span><span>inprosper<small>food rescue marketplace</small></span></a>
<nav class="nav">
<a href="?page=home">Dashboard</a>
<a href="?page=catalog">Katalog Makanan</a>
<?php if($user && $user['role']==='konsumen'): ?><a href="?page=orders">Pesanan Saya</a><?php endif; ?>
<?php if($user && $user['role']==='merchant'): ?><a href="?page=merchant">Dashboard Toko</a><a href="?page=merchant-orders">Pesanan Masuk</a><?php endif; ?>
<?php if($user && $user['role']==='admin'): ?><a href="?page=admin">Dashboard Admin</a><?php endif; ?>
<?php if($user): ?><a href="?page=notifications">Notifikasi</a><span class="user-pill"><?= e($user['name']) ?> · <?= e(ucfirst($user['role'])) ?></span><form class="inline" method="post"><?= csrf_field() ?><input type="hidden" name="action" value="logout"><button class="btn btn-quiet btn-small">Keluar</button></form>
<?php else: ?><a href="?page=login">Masuk</a><a class="btn btn-primary btn-small" href="?page=register">Daftar</a><?php endif; ?>
</nav></header>
<main class="container">
<?php if($msg=flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
<?php if($msg=flash('error')): ?><div class="alert alert-error"><?= e($msg) ?></div><?php endif; ?>
<?= $content ?>
</main>
<footer class="footer"><div><strong>InProsper</strong> · Selamatkan makanan surplus, kurangi food waste bersama mitra lokal.</div><div>Prototipe akademik · Diskon 30%–70% · Self-Pickup & Delivery</div></footer>
</body></html>
