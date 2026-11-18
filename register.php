<?php
require_once 'config.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $passwordInput = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($nama === '' || mb_strlen($nama) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Nama dan alamat email harus diisi dengan benar.';
    } elseif (strlen($passwordInput) < 8) {
        $error = 'Kata sandi minimal 8 karakter.';
    } elseif ($passwordInput !== $confirm) {
        $error = 'Konfirmasi kata sandi tidak sama.';
    } else {
        $check = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $check->bind_param('s', $email);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;
        $check->close();

        if ($exists) {
            $error = 'Email tersebut sudah terdaftar. Silakan login.';
        } else {
            $hash = password_hash($passwordInput, PASSWORD_DEFAULT);
            $insert = $conn->prepare('INSERT INTO users (nama, email, password) VALUES (?, ?, ?)');
            $insert->bind_param('sss', $nama, $email, $hash);
            $insert->execute();
            $insert->close();
            header('Location: login.php?registered=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Register | Medical Inventory UMPKU</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
<div class="auth-shell">
    <section class="auth-brand-panel">
        <img src="logo-umpku.png" alt="Logo UMPKU" class="auth-logo">
        <span class="eyebrow light">MEDICAL INVENTORY SYSTEM</span>
        <h1>Satu sistem untuk memantau peralatan elektromedis.</h1>
        <p>Buat akun agar dapat mengakses pengelolaan data inventaris secara aman.</p>
        <div class="auth-decoration">UMPKU <span>•</span> D4 ELEKTROMEDIS</div>
    </section>
    <section class="auth-form-panel">
        <div class="auth-form-wrap">
            <div class="auth-mobile-logo"><img src="logo-umpku.png" alt="Logo UMPKU"></div>
            <span class="eyebrow">BUAT AKUN BARU</span>
            <h2>Registrasi</h2>
            <p class="muted">Lengkapi data berikut untuk membuat akun.</p>
            <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
            <form method="post" class="stack-form">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <label>Nama lengkap
                    <input type="text" name="nama" maxlength="100" placeholder="Nama lengkap" required value="<?= e($_POST['nama'] ?? '') ?>">
                </label>
                <label>Email
                    <input type="email" name="email" placeholder="nama@email.com" autocomplete="email" required value="<?= e($_POST['email'] ?? '') ?>">
                </label>
                <label>Kata sandi
                    <input type="password" name="password" minlength="8" placeholder="Minimal 8 karakter" autocomplete="new-password" required>
                </label>
                <label>Konfirmasi kata sandi
                    <input type="password" name="confirm_password" minlength="8" placeholder="Ulangi kata sandi" autocomplete="new-password" required>
                </label>
                <button class="btn btn-primary btn-wide" type="submit">Buat Akun <span>→</span></button>
            </form>
            <p class="auth-switch">Sudah memiliki akun? <a href="login.php">Login</a></p>
            <div class="auth-foot">© <?= date('Y') ?> UMPKU · Medical Inventory System</div>
        </div>
    </section>
</div>
</body>
</html>
