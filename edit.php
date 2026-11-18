<?php
require_once 'auth.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) { http_response_code(400); exit('ID alat tidak valid.'); }

$stmt = $conn->prepare('SELECT id, nama_alat, tahun, merek, lokasi FROM alat_elektromedis WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$data) { http_response_code(404); exit('Data alat tidak ditemukan.'); }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $nama = trim($_POST['nama_alat'] ?? '');
    $tahun = filter_input(INPUT_POST, 'tahun', FILTER_VALIDATE_INT);
    $merek = trim($_POST['merek'] ?? '');
    $lokasi = trim($_POST['lokasi'] ?? '');
    if ($nama === '' || mb_strlen($nama) > 100 || !$tahun || $tahun < 1901 || $tahun > 2155 || $merek === '' || mb_strlen($merek) > 100 || $lokasi === '' || mb_strlen($lokasi) > 100) {
        $error = 'Mohon isi seluruh kolom dengan data yang valid.';
        $data = array_merge($data, ['nama_alat'=>$nama, 'tahun'=>$_POST['tahun'] ?? '', 'merek'=>$merek, 'lokasi'=>$lokasi]);
    } else {
        $update = $conn->prepare('UPDATE alat_elektromedis SET nama_alat = ?, tahun = ?, merek = ?, lokasi = ? WHERE id = ?');
        $update->bind_param('sissi', $nama, $tahun, $merek, $lokasi, $id);
        $update->execute();
        $update->close();
        header('Location: index.php?status=updated');
        exit;
    }
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Edit Alat | UMPKU</title><link rel="stylesheet" href="style.css"></head>
<body class="app-page">
<aside class="sidebar" id="sidebar"><a class="side-brand" href="index.php"><img src="logo-umpku.png" alt="UMPKU"><span><strong>MEDICAL INVENTORY</strong><small>MANAGEMENT SYSTEM</small></span></a><div class="side-label">MENU UTAMA</div><nav class="side-nav"><a href="index.php"><span class="nav-icon">▦</span> Dashboard</a><a href="add.php"><span class="nav-icon">＋</span> Tambah Inventaris</a></nav><div class="sidebar-spacer"></div><div class="side-user"><div class="avatar"><?= e(mb_strtoupper(mb_substr($_SESSION['user_name'] ?? 'U',0,1))) ?></div><div class="user-meta"><strong><?= e($_SESSION['user_name'] ?? 'Pengguna') ?></strong><small><?= e($_SESSION['user_email'] ?? '') ?></small></div><a class="logout-icon" href="logout.php">↪</a></div></aside>
<div class="main-area"><header class="topbar"><button class="mobile-menu" id="menuToggle">☰</button><div class="breadcrumb">Workspace <span>/</span> <a href="index.php">Inventaris</a> <span>/</span> <strong>Edit Alat</strong></div><div class="topbar-right"><a href="logout.php" class="top-logout">Logout</a></div></header>
<main class="dashboard-content form-content"><a href="index.php" class="back-link">← Kembali ke dashboard</a><section class="page-heading"><div><span class="eyebrow">DATA MASTER / PERBARUI</span><h1>Edit Data Alat</h1><p>Perbarui informasi alat elektromedis yang tersimpan.</p></div></section>
<section class="panel form-panel"><div class="form-panel-heading"><div class="form-icon">✎</div><div><h2>Perbarui Informasi Alat</h2><p>ID data: #<?= (int)$id ?></p></div></div>
<?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="data-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<label class="field full-field">Nama Alat Elektromedis<input type="text" name="nama_alat" maxlength="100" value="<?= e($data['nama_alat']) ?>" required></label>
<label class="field">Tahun Pengadaan<input type="number" name="tahun" min="1901" max="2155" value="<?= e($data['tahun']) ?>" required></label>
<label class="field">Merek Alat<input type="text" name="merek" maxlength="100" value="<?= e($data['merek']) ?>" required></label>
<label class="field full-field">Lokasi Alat<input type="text" name="lokasi" maxlength="100" value="<?= e($data['lokasi']) ?>" required></label>
<div class="form-buttons full-field"><button class="btn btn-primary" type="submit">Simpan Perubahan</button><a href="index.php" class="btn btn-light">Batal</a></div></form></section>
<footer class="app-footer">© <?= date('Y') ?> Universitas Muhammadiyah PKU Surakarta · D4 Elektromedis</footer></main></div>
<script>document.getElementById('menuToggle').addEventListener('click',()=>document.getElementById('sidebar').classList.toggle('sidebar-open'));</script></body></html>
