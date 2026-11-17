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
 * FILE       : login.php
 * FUNGSI     : Halaman Otentikasi Masuk Sistem EWS FTII UHAMKA
 * ====================================================================================
 */

$root_path = './';
require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/auth.php';

// Jika pengguna sudah login sebelumnya, langsung arahkan ke Dashboard
if (auth_check()) {
    header("Location: index.php");
    exit;
}

$error_message = '';
$success_message = '';

// Pesan saat baru saja logout
if (isset($_GET['logout']) && $_GET['logout'] == '1') {
    $success_message = 'Anda telah berhasil keluar (logout) dari sistem EWS.';
}

// Proses submit form login (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $result = auth_login($username, $password);

    if ($result['success']) {
        // Alihkan ke URL yang ingin diakses sebelumnya jika ada, atau ke dashboard
        $redirect_to = $_SESSION['auth_redirect_after_login'] ?? 'index.php';
        unset($_SESSION['auth_redirect_after_login']);
        header("Location: " . $redirect_to);
        exit;
    } else {
        $error_message = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Masuk — EWS FTII UHAMKA</title>
    <meta name="description" content="Halaman login Early Warning System (EWS) Prediksi Keberhasilan Studi Mahasiswa FTII UHAMKA.">
    
    <!-- Favicon -->
    <link rel="icon" type="image/webp" href="<?= $root_path; ?>assets/images/uhamka-logo-emblem.webp">
    <link rel="apple-touch-icon" href="<?= $root_path; ?>assets/images/uhamka-logo-emblem.webp">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $root_path; ?>assets/css/styles.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: radial-gradient(circle at top right, #e0f2fe 0%, #f1f5f9 50%, #e2e8f0 100%);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between text-slate-800 antialiased p-4 sm:p-6">

    <!-- Container Utama -->
    <div class="flex-1 flex items-center justify-center py-6 sm:py-10">
        <div class="w-full max-w-md">

            <!-- Card Login -->
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/80 border border-slate-200/80 overflow-hidden backdrop-blur-md">
                
                <!-- Header Card Biru Khas UHAMKA -->
                <div class="bg-gradient-to-br from-[#003366] via-[#004b87] to-[#0369a1] p-6 sm:p-8 text-white relative overflow-hidden">
                    <!-- Ornamen Visual Latar Belakang -->
                    <div class="absolute -right-8 -top-8 w-36 h-36 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="absolute -left-6 -bottom-6 w-32 h-32 bg-sky-400/20 rounded-full blur-xl pointer-events-none"></div>

                    <div class="relative z-10 flex flex-col items-center text-center">
                        <div class="w-16 h-16 rounded-2xl bg-white p-2 shadow-lg mb-4 flex items-center justify-center">
                            <img src="<?= $root_path; ?>assets/images/uhamka-logo-emblem.webp" 
                                 alt="Logo UHAMKA" 
                                 class="w-full h-full object-contain">
                        </div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 text-white text-[11px] font-bold border border-white/20 mb-2">
                            <svg class="w-3.5 h-3.5 text-amber-300" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                            </svg>
                            Early Warning System (EWS) C4.5
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white">
                            Masuk ke Dashboard
                        </h1>
                        <p class="text-blue-100 text-xs mt-1.5 max-w-xs leading-relaxed">
                            Sistem Peringatan Dini Prediksi Keberhasilan Studi Mahasiswa FTII UHAMKA
                        </p>
                    </div>
                </div>

                <!-- Form Area -->
                <div class="p-6 sm:p-8 space-y-5">

                    <!-- Notifikasi Pesan Sukses (Logout) -->
                    <?php if (!empty($success_message)): ?>
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="flex-1">
                            <div class="font-bold">Logout Berhasil</div>
                            <div class="text-[11px] text-emerald-700 mt-0.5"><?= htmlspecialchars($success_message); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Notifikasi Pesan Error -->
                    <?php if (!empty($error_message)): ?>
                    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-start gap-3">
                        <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="flex-1">
                            <div class="font-bold">Otentikasi Gagal</div>
                            <div class="text-[11px] text-rose-700 mt-0.5"><?= htmlspecialchars($error_message); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="login.php" class="space-y-4" autocomplete="on">

                        <!-- Input Username / Identitas -->
                        <div>
                            <label for="username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Username / NIM / Email
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <input type="text" 
                                       id="username" 
                                       name="username" 
                                       required 
                                       autofocus
                                       value="<?= htmlspecialchars($_POST['username'] ?? ''); ?>"
                                       placeholder="Contoh: admin atau kharisma"
                                       class="w-full text-xs sm:text-sm pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl font-medium text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#004b87] focus:border-transparent transition">
                            </div>
                        </div>

                        <!-- Input Password -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Kata Sandi (Password)
                                </label>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       required 
                                       placeholder="••••••••"
                                       class="w-full text-xs sm:text-sm pl-10 pr-11 py-2.5 bg-slate-50 border border-slate-300 rounded-xl font-medium text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#004b87] focus:border-transparent transition">
                                <!-- Tombol Lihat / Sembunyikan Password -->
                                <button type="button" 
                                        onclick="togglePasswordVisibility()" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition" 
                                        aria-label="Tampilkan atau sembunyikan kata sandi">
                                    <svg id="eyeIconOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg id="eyeIconClosed" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Tombol Submit Masuk -->
                        <button type="submit" 
                                class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-white font-bold text-xs sm:text-sm bg-[#004b87] hover:bg-[#003b6b] active:scale-[0.99] shadow-lg shadow-blue-900/20 transition cursor-pointer">
                            <span>Masuk ke Sistem</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </form>

                    <!-- Akun Uji Coba / Demo Cepat -->
                    <div class="pt-4 border-t border-slate-200">
                        <div class="flex items-center justify-between mb-2.5">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">
                                Akun Uji Coba / Demo Skripsi
                            </span>
                            <span class="text-[10px] font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">
                                1-Klik Isi
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-left">
                            <!-- Akun Dosen / Admin -->
                            <button type="button" 
                                    onclick="fillDemoCredentials('admin', 'admin123')"
                                    class="p-2.5 rounded-xl border border-slate-200 bg-slate-50/70 hover:bg-blue-50 hover:border-blue-300 text-left transition group">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                    <span class="text-[11px] font-bold text-slate-800 group-hover:text-blue-700">Dosen PA / Admin</span>
                                </div>
                                <div class="text-[10px] text-slate-500 mt-1">User: <code class="text-slate-800 font-bold">admin</code></div>
                                <div class="text-[10px] text-slate-500">Pass: <code class="text-slate-800 font-bold">admin123</code></div>
                            </button>

                            <!-- Akun Peneliti / Mahasiswa -->
                            <button type="button" 
                                    onclick="fillDemoCredentials('kharisma', 'uhamka2026')"
                                    class="p-2.5 rounded-xl border border-slate-200 bg-slate-50/70 hover:bg-emerald-50 hover:border-emerald-300 text-left transition group">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                    <span class="text-[11px] font-bold text-slate-800 group-hover:text-emerald-700">Peneliti Skripsi</span>
                                </div>
                                <div class="text-[10px] text-slate-500 mt-1">User: <code class="text-slate-800 font-bold">kharisma</code></div>
                                <div class="text-[10px] text-slate-500">Pass: <code class="text-slate-800 font-bold">uhamka2026</code></div>
                            </button>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Identitas Akademik Bawah -->
            <div class="mt-6 text-center text-xs text-slate-500 space-y-1">
                <div class="font-semibold text-slate-700"><?= CAMPUS_NAME; ?></div>
                <div class="text-[11px] text-slate-400">
                    <?= FAKULTAS_NAME; ?> • <?= PRODI_NAME; ?>
                </div>
                <div class="text-[10px] text-slate-400 pt-1">
                    Peneliti: <b><?= RESEARCHER_NAME; ?></b> (NIM <?= RESEARCHER_NIM; ?>) © <?= APP_YEAR; ?>
                </div>
            </div>

        </div>
    </div>

    <!-- Script Interaktif Toggle & Demo Fill -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeOpen = document.getElementById('eyeIconOpen');
            const eyeClosed = document.getElementById('eyeIconClosed');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }

        function fillDemoCredentials(user, pass) {
            const userInput = document.getElementById('username');
            const passInput = document.getElementById('password');

            userInput.value = user;
            passInput.value = pass;

            // Visual feedback singkat
            userInput.classList.add('ring-2', 'ring-blue-500');
            passInput.classList.add('ring-2', 'ring-blue-500');
            setTimeout(() => {
                userInput.classList.remove('ring-2', 'ring-blue-500');
                passInput.classList.remove('ring-2', 'ring-blue-500');
            }, 600);
        }
    </script>
</body>
</html>
