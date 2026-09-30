<?php
// functions.php — kumpulan helper function

/**
 * Membaca seluruh data user dari file JSON.
 * Mengembalikan array kosong jika file belum ada / rusak.
 */
function readUsers(): array
{
    if (!file_exists(USERS_FILE)) {
        file_put_contents(USERS_FILE, json_encode([]));
        return [];
    }

    $content = file_get_contents(USERS_FILE);
    $users = json_decode($content, true);

    return is_array($users) ? $users : [];
}

/**
 * Menulis seluruh data user ke file JSON.
 * Menggunakan file lock (LOCK_EX) agar aman dari race condition sederhana.
 */
function writeUsers(array $users): bool
{
    $json = json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    return file_put_contents(USERS_FILE, $json, LOCK_EX) !== false;
}

/**
 * Sanitasi input string dari user.
 */
function sanitize(string $data): string
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Mencari user berdasarkan email. Mengembalikan array user atau null.
 */
function findUserByEmail(string $email): ?array
{
    $users = readUsers();
    foreach ($users as $user) {
        if (strtolower($user['email']) === strtolower($email)) {
            return $user;
        }
    }
    return null;
}

/**
 * Mencari index array user berdasarkan id. Mengembalikan index (int) atau null.
 */
function findUserIndexById(string $id): ?int
{
    $users = readUsers();
    foreach ($users as $index => $user) {
        if ($user['id'] === $id) {
            return $index;
        }
    }
    return null;
}

/**
 * Membuat ID unik sederhana untuk user baru.
 */
function generateUserId(): string
{
    return uniqid('user_', true);
}

/**
 * Set flash message (error/success) yang akan tampil sekali lalu hilang.
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'][$type] = $message;
}

/**
 * Ambil dan hapus flash message tertentu.
 */
function getFlash(string $type): ?string
{
    if (!empty($_SESSION['flash'][$type])) {
        $message = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $message;
    }
    return null;
}

/**
 * Cek apakah user sedang login (session aktif).
 * Jika tidak ada session tapi ada cookie "remember me" yang valid,
 * maka session akan otomatis dipulihkan (auto-login).
 */
function isLoggedIn(): bool
{
    if (!empty($_SESSION['user_id'])) {
        return true;
    }

    // Coba auto-login lewat cookie remember_token
    if (!empty($_COOKIE['remember_token'])) {
        $parts = explode(':', $_COOKIE['remember_token']);
        if (count($parts) === 2) {
            [$userId, $token] = $parts;
            $index = findUserIndexById($userId);

            if ($index !== null) {
                $users = readUsers();
                $user = $users[$index];

                if (
                    !empty($user['remember_token']) &&
                    !empty($user['remember_expiry']) &&
                    $user['remember_expiry'] > time() &&
                    password_verify($token, $user['remember_token'])
                ) {
                    // Token valid -> pulihkan session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['nama']    = $user['nama'];
                    $_SESSION['email']   = $user['email'];
                    return true;
                }
            }
        }

        // Cookie tidak valid / kadaluarsa -> bersihkan
        setcookie('remember_token', '', time() - 3600, '/');
    }

    return false;
}

/**
 * Wajibkan user login untuk mengakses halaman ini.
 * Jika belum login, redirect ke login.php.
 */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        setFlash('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        header('Location: login.php');
        exit;
    }
}
