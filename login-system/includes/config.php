<?php
// config.php — bootstrap: session, paths, error display

// Tampilkan error saat development. Matikan (set ke 0) saat production.
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Lokasi file JSON penyimpanan user
define('USERS_FILE', __DIR__ . '/../data/users.json');

// Durasi cookie "Remember Me" (30 hari)
define('REMEMBER_ME_DURATION', 60 * 60 * 24 * 30);
