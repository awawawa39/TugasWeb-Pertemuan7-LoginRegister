<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$errors = [];
$success = null;

$index = findUserIndexById($_SESSION['user_id']);
$users = readUsers();
$currentUser = $users[$index] ?? null;

if (!$currentUser) {
    // Data user tidak ditemukan (mungkin terhapus manual dari JSON)
    header('Location: logout.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama            = sanitize($_POST['nama'] ?? '');
    $newPassword     = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($nama === '') {
        $errors[] = 'Nama tidak boleh kosong.';
    }

    // Ganti password bersifat opsional
    if ($newPassword !== '' || $confirmPassword !== '') {
        if (strlen($newPassword) < 6) {
            $errors[] = 'Password baru minimal 6 karakter.';
        } elseif ($newPassword !== $confirmPassword) {
            $errors[] = 'Konfirmasi password baru tidak cocok.';
        }
    }

    if (empty($errors)) {
        $users[$index]['nama'] = $nama;

        if ($newPassword !== '') {
            $users[$index]['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        if (writeUsers($users)) {
            $_SESSION['nama'] = $nama;
            setFlash('success', 'Profil berhasil diperbarui.');
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = 'Gagal menyimpan perubahan. Coba lagi.';
        }
    }

    $currentUser['nama'] = $nama;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Profil</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <div class="container">
    <div class="card">
      <h1>Edit Profil</h1>
      <p class="subtitle">Perbarui data akun Anda.</p>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <?php foreach ($errors as $error): ?>
            <?= $error ?><br>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="edit_profile.php" novalidate>
        <label for="nama">Nama Lengkap</label>
        <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($currentUser['nama']) ?>" required>

        <label for="email">Email</label>
        <input type="email" id="email" value="<?= htmlspecialchars($currentUser['email']) ?>" disabled>
        <p class="hint">Email tidak dapat diubah.</p>

        <label for="new_password">Password Baru (opsional)</label>
        <input type="password" id="new_password" name="new_password">
        <p class="hint">Kosongkan jika tidak ingin mengganti password.</p>

        <label for="confirm_password">Konfirmasi Password Baru</label>
        <input type="password" id="confirm_password" name="confirm_password">

        <div class="actions-row">
          <button type="submit">Simpan Perubahan</button>
          <a href="dashboard.php" class="btn btn-secondary">Batal</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
