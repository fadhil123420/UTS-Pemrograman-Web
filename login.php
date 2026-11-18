<?php
 require_once __DIR__ . '/config.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $passwordInput = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $passwordInput === '') {
        $error = 'Masukkan email dan kata sandi yang valid.';
    } else {
        
$stmt = $conn->prepare(
    "SELECT id, nama, email, password
     FROM users
     WHERE email = ?
     LIMIT 1"
);

$stmt->bind_param('s', $email);
$stmt->execute();

$result = $stmt->get_result();
$userData = $result->fetch_assoc();

$stmt->close();


        if ($userData && password_verify($passwordInput, $userData['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$userData['id'];
            $_SESSION['user_name'] = $userData['nama'];
            $_SESSION['user_email'] = $userData['email'];
            header('Location: index.php');
            exit;
        }
        $error = 'Email atau kata sandi tidak sesuai.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login | Medical Inventory UMPKU</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
<div class="auth-shell">
    <section class="auth-brand-panel">
        <img src="logo-umpku.png" alt="Logo UMPKU" class="auth-logo">
        <span class="eyebrow light">MEDICAL INVENTORY SYSTEM</span>
        <h1>Kelola inventaris alat kesehatan dengan lebih terstruktur.</h1>
        <p>Sistem informasi inventaris alat elektromedis Universitas Muhammadiyah PKU Surakarta.</p>
        <div class="auth-decoration">UMPKU <span>•</span> D4 ELEKTROMEDIS</div>
    </section>
    <section class="auth-form-panel">
        <div class="auth-form-wrap">
            <div class="auth-mobile-logo"><img src="logo-umpku.png" alt="Logo UMPKU"></div>
            <span class="eyebrow">SELAMAT DATANG KEMBALI</span>
            <h2>Login akun</h2>
            <p class="muted">Masuk untuk melanjutkan ke dashboard inventaris.</p>
            <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
            <?php if (isset($_GET['registered'])): ?><div class="alert alert-success">Registrasi berhasil. Silakan login.</div><?php endif; ?>
            <form method="post" class="stack-form">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <label>Email
                    <input type="email" name="email" placeholder="nama@email.com" autocomplete="email" required value="<?= e($_POST['email'] ?? '') ?>">
                </label>
                <label>Kata sandi
                    <input type="password" name="password" placeholder="Masukkan kata sandi" autocomplete="current-password" required>
                </label>
                <button class="btn btn-primary btn-wide" type="submit">Masuk ke Dashboard <span>→</span></button>
            </form>
            <p class="auth-switch">Belum memiliki akun? <a href="register.php">Daftar sekarang</a></p>
            <div class="auth-foot">© <?= date('Y') ?> UMPKU · Medical Inventory System</div>
        </div>
    </section>
</div>
</body>
</html>
