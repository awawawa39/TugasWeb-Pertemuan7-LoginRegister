<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Kalau sudah login, tidak perlu register lagi
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$old = ['nama' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama            = sanitize($_POST['nama'] ?? '');
    $email           = sanitize($_POST['email'] ?? '');
    $password        = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $old['nama']  = $nama;
    $old['email'] = $email;

    // 1. Validasi nama
    if ($nama === '') {
        $errors[] = 'Nama tidak boleh kosong.';
    }

    // 2. Validasi email dengan filter_var()
    if ($email === '') {
        $errors[] = 'Email tidak boleh kosong.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }

    // Validasi password
    if (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    } elseif ($password !== $confirmPassword) {
        $errors[] = 'Konfirmasi password tidak cocok.';
    }

    // 5. Cek duplikasi email
    if (empty($errors) && findUserByEmail($email)) {
        $errors[] = 'Email sudah terdaftar. Silakan gunakan email lain atau login.';
    }

    // Jika semua valid, simpan user baru
    if (empty($errors)) {
        $users = readUsers();

        $newUser = [
            'id'              => generateUserId(),
            'nama'            => $nama,
            'email'           => $email,
            // 3. Password di-hash dengan password_hash()
            'password'        => password_hash($password, PASSWORD_DEFAULT),
            'remember_token'  => null,
            'remember_expiry' => null,
            'created_at'      => date('Y-m-d H:i:s'),
        ];

        $users[] = $newUser;

        // 4. Data disimpan di file JSON
        if (writeUsers($users)) {
            setFlash('success', 'Registrasi berhasil! Silakan login dengan akun Anda.');
            header('Location: login.php');
            exit;
        } else {
            $errors[] = 'Gagal menyimpan data. Coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Akun</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <div class="container">
    <div class="card">
      <h1>Buat Akun Baru</h1>
      <p class="subtitle">Isi data di bawah untuk mendaftar.</p>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <?php foreach ($errors as $error): ?>
            <?= $error ?><br>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="register.php" novalidate>
        <label for="nama">Nama Lengkap</label>
        <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($old['nama']) ?>" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($old['email']) ?>" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <p class="hint">Minimal 6 karakter.</p>

        <label for="confirm_password">Konfirmasi Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit">Daftar</button>
      </form>

      <p class="footer-link">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
  </div>
</body>
</html>
