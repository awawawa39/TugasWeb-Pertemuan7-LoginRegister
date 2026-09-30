<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$success = getFlash('success');
$flashError = getFlash('error');
if ($flashError) {
    $errors[] = $flashError;
}

$old = ['email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    $old['email'] = $email;

    if ($email === '' || $password === '') {
        $errors[] = 'Email dan password wajib diisi.';
    } else {
        $user = findUserByEmail($email);

        // 6. Sistem login dengan session
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['email']   = $user['email'];

            // Bonus: Remember Me dengan cookies
            if ($remember) {
                $token       = bin2hex(random_bytes(32));
                $tokenHash   = password_hash($token, PASSWORD_DEFAULT);
                $expiry      = time() + REMEMBER_ME_DURATION;

                $users = readUsers();
                $index = findUserIndexById($user['id']);
                $users[$index]['remember_token']  = $tokenHash;
                $users[$index]['remember_expiry'] = $expiry;
                writeUsers($users);

                setcookie(
                    'remember_token',
                    $user['id'] . ':' . $token,
                    [
                        'expires'  => $expiry,
                        'path'     => '/',
                        'httponly' => true,
                        'samesite' => 'Lax',
                    ]
                );
            }

            setFlash('success', 'Login berhasil. Selamat datang, ' . $user['nama'] . '!');
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = 'Email atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <div class="container">
    <div class="card">
      <h1>Masuk ke Akun</h1>
      <p class="subtitle">Silakan login untuk melanjutkan.</p>

      <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <?php foreach ($errors as $error): ?>
            <?= $error ?><br>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="login.php" novalidate>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($old['email']) ?>" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <div class="checkbox-row">
          <input type="checkbox" id="remember" name="remember">
          <label for="remember" style="margin:0;">Ingat saya (Remember Me)</label>
        </div>

        <button type="submit">Login</button>
      </form>

      <p class="footer-link">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </div>
  </div>
</body>
</html>
