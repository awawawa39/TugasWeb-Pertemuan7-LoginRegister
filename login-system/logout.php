<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Hapus remember_token dari data user (agar tidak bisa auto-login lagi)
if (!empty($_SESSION['user_id'])) {
    $index = findUserIndexById($_SESSION['user_id']);
    if ($index !== null) {
        $users = readUsers();
        $users[$index]['remember_token']  = null;
        $users[$index]['remember_expiry'] = null;
        writeUsers($users);
    }
}

// Hapus cookie remember_token
if (!empty($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
    unset($_COOKIE['remember_token']);
}

// 8. Logout functionality (session_destroy)
$_SESSION = [];
session_destroy();

session_start();
setFlash('success', 'Anda berhasil logout.');
header('Location: login.php');
exit;
