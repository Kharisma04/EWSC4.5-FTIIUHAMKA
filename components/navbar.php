<?php
/**
 * FILE       : components/navbar.php
 * FUNGSI     : Topbar Navigasi Atas dengan Pemilih Algoritma (C4.5 / Naive Bayes), Info Pengguna & Logout.
 */

$nav_category = isset($nav_category) ? $nav_category : 'Sistem EWS';
$nav_title    = isset($nav_title)    ? $nav_title    : 'Dashboard';
$current_user = function_exists('auth_user') ? auth_user() : null;
$active_algo  = function_exists('get_active_algorithm') ? get_active_algorithm() : 'c45';
$algo_meta    = function_exists('get_algorithm_metadata') ? get_algorithm_metadata($active_algo) : [];
$is_admin_nav = function_exists('auth_is_admin') ? auth_is_admin() : false;
?>
<!-- Topbar -->
<header class="glass-header sticky top-0 z-30 border-b border-slate-200 px-4 sm:px-6 py-3 flex items-center justify-between no-print" aria-label="Navigasi atas">

    <div class="flex items-center gap-3">
        <!-- Tombol Toggle Sidebar Mobile -->
        <button onclick="toggleSidebar()"
                class="md:hidden p-2 rounded-lg text-slate-500 hover:text-[#004b87] hover:bg-blue-50 border border-slate-200 transition focus-visible:outline-2 focus-visible:outline-[#004b87]"
                aria-label="Buka menu navigasi">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <!-- Logo Mobile -->
        <a href="<?= $root_path; ?>index.php" class="md:hidden shrink-0" title="EWS FTII UHAMKA">
            <img src="<?= $root_path; ?>assets/images/uhamka-logo-emblem.webp" alt="UHAMKA" class="h-7 w-7 object-contain">
        </a>

        <!-- Breadcrumb -->
        <div>
            <div class="text-[10px] font-bold text-[#004b87] uppercase tracking-widest"><?= htmlspecialchars($nav_category); ?></div>
            <h1 class="text-sm font-black text-slate-900 leading-tight mt-0.5"><?= htmlspecialchars($nav_title); ?></h1>
        </div>
    </div>

    <!-- Aksi Kanan -->
    <div class="flex items-center gap-2 sm:gap-3">

        <!-- Selector Algoritma (hanya admin) -->
        <?php if ($is_admin_nav): ?>
        <div class="inline-flex items-center p-0.5 sm:p-1 rounded-xl bg-slate-100 border border-slate-200">
            <a href="?algo=c45"
               class="px-2 sm:px-2.5 py-1 rounded-lg text-[11px] sm:text-xs font-bold transition flex items-center gap-1 <?= $active_algo === 'c45' ? 'bg-[#004b87] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'; ?>"
               title="Aktifkan Algoritma Decision Tree C4.5">
                <svg class="w-3.5 h-3.5 hidden sm:inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/>
                </svg>
                <span>C4.5</span>
            </a>
            <a href="?algo=naive_bayes"
               class="px-2 sm:px-2.5 py-1 rounded-lg text-[11px] sm:text-xs font-bold transition flex items-center gap-1 <?= $active_algo === 'naive_bayes' ? 'bg-violet-700 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'; ?>"
               title="Aktifkan Algoritma Gaussian Naive Bayes">
                <svg class="w-3.5 h-3.5 hidden sm:inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span>Naive Bayes</span>
            </a>
        </div>
        <?php endif; ?>

        <!-- Tombol Cetak -->
        <button onclick="window.print()"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-600 bg-white hover:bg-slate-50 rounded-lg border border-slate-300 transition shadow-sm focus-visible:outline-2 focus-visible:outline-[#004b87]"
                aria-label="Cetak laporan">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            <span class="hidden md:inline">Cetak</span>
        </button>

        <?php if ($current_user): ?>
        <!-- Info Akun Login Topbar -->
        <div class="hidden sm:flex items-center gap-2 pl-2 border-l border-slate-200">
            <div class="w-7 h-7 rounded-lg <?= htmlspecialchars($current_user['avatar_bg'] ?? 'bg-[#004b87]'); ?> text-white font-extrabold flex items-center justify-center text-[11px] shrink-0">
                <?= htmlspecialchars($current_user['inisial'] ?? 'U'); ?>
            </div>
            <div class="text-left hidden lg:block">
                <div class="text-xs font-bold text-slate-800 leading-none truncate max-w-[130px]">
                    <?= htmlspecialchars($current_user['nama'] ?? 'User'); ?>
                </div>
                <div class="text-[10px] text-slate-400 mt-0.5 leading-none">
                    <?= htmlspecialchars($current_user['role'] ?? 'Pengguna'); ?>
                </div>
            </div>
        </div>

        <!-- Tombol Logout Cepat Topbar -->
        <a href="<?= $root_path; ?>logout.php"
           onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')"
           class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg border border-rose-200 transition shadow-sm"
           title="Keluar / Logout">
            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            <span class="hidden md:inline">Keluar</span>
        </a>
        <?php endif; ?>
    </div>

</header>
