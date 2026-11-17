<?php
/**
 * ====================================================================================
 * APLIKASI   : Early Warning System (EWS) Prediksi Keberhasilan Studi Mahasiswa
 * METODE     : Algoritma Decision Tree C4.5
 * SKRIPSI    : Penerapan Algoritma Decision Tree C4.5 Sebagai Early Warning System 
 *              Prediksi Keberhasilan Studi Mahasiswa FTII UHAMKA
 * PENELITI   : Kharisma Rahmawati Lubis (NIM: 2203015072)
 * INSTITUSI  : Program Studi Teknik Informatika, FTII UHAMKA Jakarta (2026)
 * ====================================================================================
 * FILE       : app/auth.php
 * FUNGSI     : Manajemen Otentikasi Pengguna (Login, Logout, Session Guard & User State)
 * ====================================================================================
 */

require_once __DIR__ . '/config.php';

/**
 * Memulai sesi PHP secara aman jika belum aktif
 */
function auth_session_start() {
    if (session_status() === PHP_SESSION_NONE) {
        // Pengaturan cookie sesi yang aman (jika header belum terkirim)
        if (!headers_sent()) {
            @ini_set('session.cookie_httponly', '1');
            @ini_set('session.use_only_cookies', '1');
            @session_name('EWS_FTII_UHAMKA_SESSION');
        }
        @session_start();
    }
}

// Otomatis pastikan sesi aktif saat file auth.php disertakan
auth_session_start();

/**
 * Menginisialisasi data pengguna default jika users.json belum tersedia
 *
 * @return array Daftar pengguna default
 */
function auth_init_default_users() {
    $default_users = [
        [
            'id'        => 1,
            'username'  => 'admin',
            'password'  => password_hash('admin123', PASSWORD_DEFAULT),
            'nama'      => 'Dosen Pembimbing Akademik / Admin',
            'email'     => 'dosen@uhamka.ac.id',
            'nim'       => null,
            'role'      => 'Dosen PA / Administrator',
            'inisial'   => 'PA',
            'avatar_bg' => 'bg-[#004b87]'
        ],
        [
            'id'        => 2,
            'username'  => 'kharisma',
            'password'  => password_hash('uhamka2026', PASSWORD_DEFAULT),
            'nama'      => 'Kharisma Rahmawati Lubis',
            'email'     => 'kharisma@uhamka.ac.id',
            'nim'       => '2203015072',
            'role'      => 'Peneliti Skripsi (Mahasiswa)',
            'inisial'   => 'KR',
            'avatar_bg' => 'bg-emerald-600'
        ]
    ];

    $data_dir = dirname(USER_DATA_FILE);
    if (!is_dir($data_dir)) {
        @mkdir($data_dir, 0777, true);
    }

    file_put_contents(
        USER_DATA_FILE,
        json_encode($default_users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );

    return $default_users;
}

/**
 * Mengambil semua data pengguna dari file database JSON
 *
 * @return array
 */
function auth_get_users() {
    if (!defined('USER_DATA_FILE') || !file_exists(USER_DATA_FILE)) {
        return auth_init_default_users();
    }

    $content = @file_get_contents(USER_DATA_FILE);
    if ($content === false) {
        return auth_init_default_users();
    }

    $users = json_decode($content, true);
    if (!is_array($users) || empty($users)) {
        return auth_init_default_users();
    }

    return $users;
}

/**
 * Mencari data pengguna berdasarkan username, email, atau NIM
 *
 * @param string $identifier Username, email, atau NIM
 * @return array|null Data pengguna atau null jika tidak ditemukan
 */
function auth_find_user($identifier) {
    $identifier = trim(strtolower($identifier));
    if ($identifier === '') {
        return null;
    }

    $users = auth_get_users();
    foreach ($users as $user) {
        $username = strtolower($user['username'] ?? '');
        $email    = strtolower($user['email'] ?? '');
        $nim      = strtolower((string)($user['nim'] ?? ''));

        if ($identifier === $username || $identifier === $email || ($nim !== '' && $identifier === $nim)) {
            return $user;
        }
    }

    return null;
}

/**
 * Memverifikasi kecocokan password terhadap hash atau plaintext cadangan
 *
 * @param string $password Password input
 * @param string $stored_password Password tersimpan di database
 * @return bool
 */
function auth_verify_password($password, $stored_password) {
    if (password_verify($password, $stored_password)) {
        return true;
    }
    // Fallback toleransi jika password diedit manual dalam format teks biasa
    if ($password === $stored_password) {
        return true;
    }
    return false;
}

/**
 * Melakukan proses otentikasi login pengguna
 *
 * @param string $identifier Username, NIM, atau Email
 * @param string $password Password
 * @return array ['success' => bool, 'message' => string, 'user' => array|null]
 */
function auth_login($identifier, $password) {
    auth_session_start();

    $identifier = trim($identifier);
    $password   = trim($password);

    if ($identifier === '' || $password === '') {
        return [
            'success' => false,
            'message' => 'Username dan kata sandi wajib diisi.'
        ];
    }

    $user = auth_find_user($identifier);
    if (!$user) {
        return [
            'success' => false,
            'message' => 'Akun dengan username atau identitas tersebut tidak ditemukan.'
        ];
    }

    if (!auth_verify_password($password, $user['password'] ?? '')) {
        return [
            'success' => false,
            'message' => 'Kata sandi yang Anda masukkan salah. Silakan coba lagi.'
        ];
    }

    // Hindari session fixation dengan meregenerasi session ID
    if (!headers_sent()) {
        @session_regenerate_id(true);
    }

    // Simpan data otentikasi dalam session (tanpa hash password)
    $safe_user = $user;
    unset($safe_user['password']);

    $_SESSION['auth_logged_in'] = true;
    $_SESSION['auth_user']      = $safe_user;
    $_SESSION['auth_login_at']  = time();

    return [
        'success' => true,
        'message' => 'Login berhasil!',
        'user'    => $safe_user
    ];
}

/**
 * Mengecek apakah pengguna saat ini sudah login
 *
 * @return bool
 */
function auth_check() {
    auth_session_start();
    return !empty($_SESSION['auth_logged_in']) && !empty($_SESSION['auth_user']);
}

/**
 * Mengambil data profil pengguna yang sedang login saat ini
 *
 * @return array|null Data pengguna atau null jika belum login
 */
function auth_user() {
    auth_session_start();
    return $_SESSION['auth_user'] ?? null;
}

/**
 * Mengakhiri sesi pengguna (Logout)
 *
 * @return void
 */
function auth_logout() {
    auth_session_start();

    // Hapus seluruh variabel sesi
    $_SESSION = [];

    // Hapus cookie sesi dari browser jika ada dan header belum terkirim
    if (!headers_sent() && ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    // Hancurkan sesi jika masih aktif
    if (session_status() === PHP_SESSION_ACTIVE) {
        @session_destroy();
    }
}

/**
 * Guard / Penjaga Halaman: Memastikan pengguna wajib sudah login.
 * Jika belum login, otomatis dialihkan ke halaman login.php.
 *
 * @param string $root_path Path relatif kembali ke root folder aplikasi (misal: './' atau '../../')
 * @return void
 */
function auth_require($root_path = './') {
    auth_session_start();

    if (!auth_check()) {
        // Simpan URL yang sedang dicoba diakses untuk redirect setelah login sukses
        $current_uri = $_SERVER['REQUEST_URI'] ?? '';
        if (!empty($current_uri)) {
            $_SESSION['auth_redirect_after_login'] = $current_uri;
        }

        // Alihkan ke login
        $login_url = rtrim($root_path, '/') . '/login.php';
        header("Location: {$login_url}");
        exit;
    }
}
