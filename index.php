<?php
require_once 'auth.php';

$result = $conn->query('SELECT id, nama_alat, tahun, merek, lokasi FROM alat_elektromedis ORDER BY id DESC');
$items = $result->fetch_all(MYSQLI_ASSOC);
$total = count($items);
$brandCount = count(array_unique(array_column($items, 'merek')));
$locationCount = count(array_unique(array_column($items, 'lokasi')));
$yearCount = count(array_unique(array_column($items, 'tahun')));

$byYear = [];
$byLocation = [];
foreach ($items as $item) {
    $year = (string)$item['tahun'];
    $location = (string)$item['lokasi'];
    $byYear[$year] = ($byYear[$year] ?? 0) + 1;
    $byLocation[$location] = ($byLocation[$location] ?? 0) + 1;
}
ksort($byYear);
arsort($byLocation);
$byLocation = array_slice($byLocation, 0, 6, true);
$status = $_GET['status'] ?? '';
$statusMessages = [
    'added' => 'Data alat berhasil ditambahkan.',
    'updated' => 'Data alat berhasil diperbarui.',
    'deleted' => 'Data alat berhasil dihapus.'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Dashboard sistem inventaris alat elektromedis UMPKU Surakarta.">
<title>Dashboard | Medical Inventory UMPKU</title>
<link rel="stylesheet" href="style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="app-page">
<aside class="sidebar" id="sidebar">
    <a class="side-brand" href="index.php">
        <img src="logo-umpku.png" alt="UMPKU">
        <span><strong>MEDICAL INVENTORY</strong><small>MANAGEMENT SYSTEM</small></span>
    </a>
    <div class="side-label">MENU UTAMA</div>
    <nav class="side-nav">
        <a class="active" href="index.php"><span class="nav-icon">▦</span> Dashboard</a>
        <a href="add.php"><span class="nav-icon">＋</span> Tambah Inventaris</a>
        <a href="#inventory"><span class="nav-icon">▤</span> Data Inventaris</a>
    </nav>
    <div class="side-label side-label-bottom">SISTEM</div>
    <div class="sidebar-note"><span class="online-dot"></span><span><strong>Sistem aktif</strong><small>Database terhubung</small></span></div>
    <div class="side-user">
        <div class="avatar"><?= e(mb_strtoupper(mb_substr($_SESSION['user_name'] ?? 'U', 0, 1))) ?></div>
        <div class="user-meta"><strong><?= e($_SESSION['user_name'] ?? 'Pengguna') ?></strong><small><?= e($_SESSION['user_email'] ?? '') ?></small></div>
        <a class="logout-icon" href="logout.php" title="Logout" aria-label="Logout">↪</a>
    </div>
</aside>

<div class="main-area">
    <header class="topbar">
        <button class="mobile-menu" id="menuToggle" aria-label="Buka menu">☰</button>
        <div class="breadcrumb">Workspace <span>/</span> <strong>Dashboard</strong></div>
        <div class="topbar-right">
            <span class="top-date"><?= date('d M Y') ?></span>
            <div class="top-user"><span class="top-avatar"><?= e(mb_strtoupper(mb_substr($_SESSION['user_name'] ?? 'U', 0, 1))) ?></span><span><?= e($_SESSION['user_name'] ?? 'Pengguna') ?></span></div>
            <a href="logout.php" class="top-logout">Logout</a>
        </div>
    </header>

    <main class="dashboard-content">
        <section class="page-heading">
            <div>
                <span class="eyebrow">OVERVIEW / INVENTARIS</span>
                <h1>Dashboard Inventaris</h1>
                <p>Ringkasan dan pemantauan data alat elektromedis UMPKU.</p>
            </div>
            <a href="add.php" class="btn btn-primary"><span class="plus">＋</span> Tambah Alat</a>
        </section>

        <?php if (isset($statusMessages[$status])): ?>
            <div class="alert alert-success"><?= e($statusMessages[$status]) ?></div>
        <?php endif; ?>

        <section class="metric-grid">
            <article class="metric-card metric-primary">
                <div class="metric-top"><span>Total Inventaris</span><span class="metric-symbol">▦</span></div>
                <div class="metric-value"><?= number_format($total) ?></div>
                <div class="metric-foot"><span class="metric-dot"></span> Alat terdaftar dalam sistem</div>
            </article>
            <article class="metric-card">
                <div class="metric-top"><span>Jenis Merek</span><span class="metric-symbol pink">◇</span></div>
                <div class="metric-value"><?= number_format($brandCount) ?></div>
                <div class="metric-foot">Merek berbeda tercatat</div>
            </article>
            <article class="metric-card">
                <div class="metric-top"><span>Lokasi Penempatan</span><span class="metric-symbol orange">⌖</span></div>
                <div class="metric-value"><?= number_format($locationCount) ?></div>
                <div class="metric-foot">Lokasi alat terdata</div>
            </article>
            <article class="metric-card">
                <div class="metric-top"><span>Tahun Pengadaan</span><span class="metric-symbol purple">◷</span></div>
                <div class="metric-value"><?= number_format($yearCount) ?></div>
                <div class="metric-foot">Variasi tahun pengadaan</div>
            </article>
        </section>

        <section class="chart-grid">
            <article class="panel chart-panel">
                <div class="panel-heading"><div><span class="eyebrow">ANALISIS DATA</span><h2>Inventaris berdasarkan tahun</h2><p>Jumlah alat menurut tahun pengadaan.</p></div><span class="panel-tag">Tahunan</span></div>
                <div class="chart-wrap"><canvas id="yearChart"></canvas></div>
            </article>
            <article class="panel chart-panel">
                <div class="panel-heading"><div><span class="eyebrow">DISTRIBUSI</span><h2>Lokasi penempatan alat</h2><p>Distribusi alat berdasarkan lokasi.</p></div></div>
                <div class="chart-wrap"><canvas id="locationChart"></canvas></div>
            </article>
        </section>

        <section class="panel inventory-panel" id="inventory">
            <div class="inventory-heading">
                <div><span class="eyebrow">DATA MASTER</span><h2>Daftar Inventaris Alat</h2><p>Kelola seluruh data peralatan elektromedis.</p></div>
                <label class="search-field"><span>⌕</span><input type="search" id="tableSearch" placeholder="Cari nama, merek, lokasi..."></label>
            </div>
            <div class="table-responsive">
                <table id="inventoryTable">
                    <thead><tr><th>No.</th><th>Nama Alat</th><th>Tahun</th><th>Merek</th><th>Lokasi</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php if ($items): ?>
                        <?php foreach ($items as $i => $item): ?>
                        <tr>
                            <td class="muted-cell"><?= $i + 1 ?></td>
                            <td><strong class="equipment-title"><?= e($item['nama_alat']) ?></strong><small class="equipment-id">ID INVENTARIS #<?= (int)$item['id'] ?></small></td>
                            <td><span class="year-chip"><?= e($item['tahun']) ?></span></td>
                            <td><?= e($item['merek']) ?></td>
                            <td><span class="location-chip">⌖ <?= e($item['lokasi']) ?></span></td>
                            <td><div class="table-actions"><a class="action-edit" href="edit.php?id=<?= (int)$item['id'] ?>">Edit</a><form method="post" action="delete.php" onsubmit="return confirm('Hapus data <?= e($item['nama_alat']) ?>?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$item['id'] ?>"><button class="action-delete" type="submit">Hapus</button></form></div></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="empty-state">Belum ada data inventaris. Silakan tambahkan alat pertama.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
                <p id="noSearchResult" class="empty-state" hidden>Data yang dicari tidak ditemukan.</p>
            </div>
            <div class="table-footer"><span>Menampilkan <strong id="visibleCount"><?= $total ?></strong> dari <?= $total ?> data</span><span>Medical Inventory System · UMPKU</span></div>
        </section>
        <footer class="app-footer">© <?= date('Y') ?> Universitas Muhammadiyah PKU Surakarta · Program Studi D4 Elektromedis</footer>
    </main>
</div>

<script>
const yearLabels = <?= json_encode(array_keys($byYear), JSON_UNESCAPED_UNICODE) ?>;
const yearValues = <?= json_encode(array_values($byYear)) ?>;
const locationLabels = <?= json_encode(array_keys($byLocation), JSON_UNESCAPED_UNICODE) ?>;
const locationValues = <?= json_encode(array_values($byLocation)) ?>;

new Chart(document.getElementById('yearChart'), {
    type: 'bar',
    data: { labels: yearLabels, datasets: [{ label: 'Jumlah alat', data: yearValues, backgroundColor: '#ed4b82', borderRadius: 7, maxBarThickness: 38 }] },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f0edf1' } } } }
});
new Chart(document.getElementById('locationChart'), {
    type: 'doughnut',
    data: { labels: locationLabels, datasets: [{ data: locationValues, backgroundColor: ['#ed4b82','#ff9d52','#9b7bea','#42b6a5','#f3c64e','#6d8de8'], borderWidth: 0, hoverOffset: 5 }] },
    options: { responsive: true, maintainAspectRatio: false, cutout: '68%', plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 7, padding: 15, font: { size: 10 } } } } }
});

const searchInput = document.getElementById('tableSearch');
const rows = [...document.querySelectorAll('#inventoryTable tbody tr')];
const noResult = document.getElementById('noSearchResult');
const visibleCount = document.getElementById('visibleCount');
searchInput.addEventListener('input', function () {
    const q = this.value.toLocaleLowerCase('id').trim();
    let count = 0;
    rows.forEach(row => {
        const match = row.innerText.toLocaleLowerCase('id').includes(q);
        row.hidden = !match;
        if (match) count++;
    });
    noResult.hidden = count !== 0 || q === '';
    visibleCount.textContent = count;
});
document.getElementById('menuToggle').addEventListener('click', () => document.getElementById('sidebar').classList.toggle('sidebar-open'));
</script>
</body>
</html>
