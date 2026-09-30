* [ ] 

# Sistem Login/Register — PHP Native + JSON

Sistem login/register sederhana menggunakan PHP native (tanpa framework) dengan file JSON sebagai penyimpanan data.

## Struktur Folder

```
login-system/
├── index.php           # Entry point (redirect ke dashboard/login)
├── register.php        # Halaman & proses registrasi
├── login.php           # Halaman & proses login (+ Remember Me)
├── dashboard.php        # Halaman dashboard (diproteksi)
├── edit_profile.php    # Edit nama & password (diproteksi, bonus)
├── logout.php           # Proses logout
├── includes/
│   ├── config.php       # Session start, konstanta
│   └── functions.php    # Helper: baca/tulis JSON, validasi, auth
├── data/
│   └── users.json       # Penyimpanan data user
└── assets/
    └── style.css         # Tampilan CSS
```

## Cara Menjalankan

Pastikan PHP terinstall (PHP 7.4+ disarankan, 8.x juga bisa).

Dari dalam folder `login-system`, jalankan server bawaan PHP:

```bash
php -S localhost:8000
```


Buka `http://localhost:8000` di browser.

Pastikan folder `data/` bisa ditulis oleh PHP (permission write), karena `users.json` akan diperbarui setiap ada registrasi/login/edit profil.

## Fitur yang Diimplementasikan

- Form registrasi dengan validasi nama, email, password.
- Validasi email menggunakan `filter_var($email, FILTER_VALIDATE_EMAIL)`.
- Password di-hash dengan `password_hash()` dan diverifikasi dengan `password_verify()`.
- Data user disimpan di `data/users.json`.
- Cek duplikasi email saat registrasi.
- Sistem login dengan PHP session.
- Dashboard diproteksi — otomatis redirect ke `login.php` jika belum login.
- Logout dengan `session_destroy()`.
- Semua input disanitasi dengan `htmlspecialchars()`.
- Pesan error & sukses ditampilkan lewat flash message (session sekali pakai).
- **Bonus:**
  - **Remember Me** — saat dicentang, token acak dibuat, di-hash dengan `password_hash()`, disimpan di `users.json`, dan dikirim ke browser lewat cookie `httponly`. Saat user kembali tanpa session aktif, token cookie dicocokkan untuk auto-login.
  - **Edit Profile** — user bisa mengubah nama dan password setelah login.
  - **Tampilan CSS** — dark theme yang rapi dan responsif untuk semua halaman.

## Catatan Keamanan

- Ini adalah proyek belajar/latihan. Untuk produksi sesungguhnya, sebaiknya:
  - Gunakan database (MySQL/PostgreSQL) alih-alih file JSON untuk menghindari race condition saat banyak user mengakses bersamaan.
  - Tambahkan proteksi CSRF token pada setiap form.
  - Tambahkan rate limiting untuk mencegah brute-force login.
  - Set `display_errors` ke `0` dan aktifkan `error_log` di `config.php`.
