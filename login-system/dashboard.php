<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// 7. Dashboard yang diproteksi (redirect jika belum login)
requireLogin();

$success = getFlash('success');
$error   = getFlash('error');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="dashboard-header">
        <h1>Dashboard</h1>
        <span class="badge">Login aktif</span>
      </div>
      <p class="subtitle">Selamat datang kembali!</p>

      <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
      <?php endif; ?>

      <div class="profile-info">
        <p><span>Nama:</span> <?= htmlspecialchars($_SESSION['nama']) ?></p>
        <p><span>Email:</span> <?= htmlspecialchars($_SESSION['email']) ?></p>
      </div>

      <div class="actions-row">
        <a href="edit_profile.php" class="btn btn-secondary">Edit Profil</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
      </div>
    </div>
  </div>
</body>
</html>
