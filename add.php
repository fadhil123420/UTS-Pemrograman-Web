<?php
require_once 'auth.php';

$error = '';
$nama = $tahun = $merek = $lokasi = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $nama = trim($_POST['nama_alat'] ?? '');
    $tahun = filter_input(INPUT_POST, 'tahun', FILTER_VALIDATE_INT);
    $merek = trim($_POST['merek'] ?? '');
    $lokasi = trim($_POST['lokasi'] ?? '');

    if ($nama === '' || mb_strlen($nama) > 100 || !$tahun || $tahun < 1901 || $tahun > 2155 || $merek === '' || mb_strlen($merek) > 100 || $lokasi === '' || mb_strlen($lokasi) > 100) {
        $error = 'Mohon isi seluruh kolom dengan data yang valid.';
    } else {
        $stmt = $conn->prepare('INSERT INTO alat_elektromedis (nama_alat, tahun, merek, lokasi) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('siss', $nama, $tahun, $merek, $lokasi);
        $stmt->execute();
        $stmt->close();
        header('Location: index.php?status=added');
        exit;
    }
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Tambah Alat | UMPKU</title><link rel="stylesheet" href="style.css"></head>
<body class="app-page">
<aside class="sidebar" id="sidebar"><a class="side-brand" href="index.php"><img src="logo-umpku.png" alt="UMPKU"><span><strong>MEDICAL INVENTORY</strong><small>MANAGEMENT SYSTEM</small></span></a><div class="side-label">MENU UTAMA</div><nav class="side-nav"><a href="index.php"><span class="nav-icon">▦</span> Dashboard</a><a class="active" href="add.php"><span class="nav-icon">＋</span> Tambah Inventaris</a></nav><div class="sidebar-spacer"></div><div class="side-user"><div class="avatar"><?= e(mb_strtoupper(mb_substr($_SESSION['user_name'] ?? 'U',0,1))) ?></div><div class="user-meta"><strong><?= e($_SESSION['user_name'] ?? 'Pengguna') ?></strong><small><?= e($_SESSION['user_email'] ?? '') ?></small></div><a class="logout-icon" href="logout.php">↪</a></div></aside>
<div class="main-area"><header class="topbar"><button class="mobile-menu" id="menuToggle">☰</button><div class="breadcrumb">Workspace <span>/</span> <a href="index.php">Inventaris</a> <span>/</span> <strong>Tambah Alat</strong></div><div class="topbar-right"><a href="logout.php" class="top-logout">Logout</a></div></header>
<main class="dashboard-content form-content"><a href="index.php" class="back-link">← Kembali ke dashboard</a><section class="page-heading"><div><span class="eyebrow">DATA MASTER / TAMBAH</span><h1>Tambah Data Alat</h1><p>Masukkan informasi peralatan elektromedis baru.</p></div></section>
<section class="panel form-panel"><div class="form-panel-heading"><div class="form-icon">＋</div><div><h2>Informasi Alat Elektromedis</h2><p>Semua kolom wajib diisi.</p></div></div>
<?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="data-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<label class="field full-field">Nama Alat Elektromedis<input type="text" name="nama_alat" maxlength="100" placeholder="Contoh: Patient Monitor" value="<?= e($nama) ?>" required></label>
<label class="field">Tahun Pengadaan<input type="number" name="tahun" min="1901" max="2155" placeholder="2024" value="<?= e($_POST['tahun'] ?? '') ?>" required></label>
<label class="field">Merek Alat<input type="text" name="merek" maxlength="100" placeholder="Contoh: Mindray" value="<?= e($merek) ?>" required></label>
<label class="field full-field">Lokasi Alat<input type="text" name="lokasi" maxlength="100" placeholder="Contoh: Ruang ICU" value="<?= e($lokasi) ?>" required></label>
<div class="form-buttons full-field"><button class="btn btn-primary" type="submit">Simpan Data</button><a href="index.php" class="btn btn-light">Batal</a></div></form></section>
<footer class="app-footer">© <?= date('Y') ?> Universitas Muhammadiyah PKU Surakarta · D4 Elektromedis</footer></main></div>
<script>document.getElementById('menuToggle').addEventListener('click',()=>document.getElementById('sidebar').classList.toggle('sidebar-open'));</script></body></html>
