<?php
/**
 * FILE       : components/sidebar.php
 * FUNGSI     : Komponen Sidebar Navigasi Samping dengan Pemilih Algoritma, Tombol Logout, dan Info Pengguna.
 */

$active_page = isset($active_page) ? $active_page : 'dashboard';

// Ambil info user yang sedang aktif login
$current_user = function_exists('auth_user') ? auth_user() : null;
$user_name     = $current_user['nama'] ?? RESEARCHER_NAME;
$user_role     = $current_user['role'] ?? ('NIM ' . RESEARCHER_NIM);
$user_initials = $current_user['inisial'] ?? 'KR';
$user_avatar_bg= $current_user['avatar_bg'] ?? 'bg-[#004b87]';

// Algoritma aktif
$active_algo = function_exists('get_active_algorithm') ? get_active_algorithm() : 'c45';
$is_admin    = function_exists('auth_is_admin') ? auth_is_admin() : false;
?>
<!-- Sidebar Navigasi Kiri -->
<aside id="appSidebar" class="fixed inset-y-0 left-0 z-50 w-[260px] bg-white border-r border-slate-200 flex flex-col transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out no-print" aria-label="Navigasi samping">

    <!-- Branding -->
    <div class="p-5 border-b border-slate-100">
        <div class="flex items-center justify-between">
            <a href="<?= $root_path; ?>index.php" class="flex items-center gap-2 group" title="EWS FTII UHAMKA">
                <img src="<?= $root_path; ?>assets/images/uhamka-logo-horizontal.webp" alt="Logo UHAMKA" class="h-9 w-auto object-contain">
            </a>
            <button onclick="toggleSidebar()" class="md:hidden p-1.5 rounded-lg text-slate-400 hover:text-[#004b87] hover:bg-blue-50 transition focus-visible:outline-2 focus-visible:outline-[#004b87]" aria-label="Tutup navigasi">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="mt-3 text-[11px] text-slate-500 font-medium leading-snug">
            Sistem Peringatan Dini Keberhasilan Studi
        </div>
    </div>

    <!-- Menu Navigasi -->
    <nav class="p-3 flex-1 overflow-y-auto space-y-1" aria-label="Menu utama">
        <div class="px-2 py-2 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Menu Navigasi</div>

        <!-- Link Dashboard -->
        <a href="<?= $root_path; ?>index.php"
           class="sidebar-link <?= $active_page === 'dashboard' ? 'active' : ''; ?>"
           <?= $active_page === 'dashboard' ? 'aria-current="page"' : ''; ?>>
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Dashboard</span>
        </a>

        <!-- Link Form Simulasi -->
        <a href="<?= $root_path; ?>pages/uji-coba/index.php"
           class="sidebar-link <?= $active_page === 'uji-coba' ? 'active' : ''; ?>"
           <?= $active_page === 'uji-coba' ? 'aria-current="page"' : ''; ?>>
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span>Form Simulasi</span>
        </a>

        <!-- Pilihan Model Algoritma — hanya untuk Admin -->
        <?php if ($is_admin): ?>
        <div class="pt-4 mt-3 border-t border-slate-100">
            <div class="px-2 py-1.5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest flex items-center justify-between">
                <span>Model Algoritma</span>
                <span class="text-[9px] px-1.5 py-0.5 bg-blue-50 text-blue-700 font-bold rounded">Pilih</span>
            </div>

            <!-- C4.5 Selector -->
            <a href="?algo=c45"
               class="sidebar-link <?= $active_algo === 'c45' ? 'bg-blue-50/80 text-[#004b87] border-blue-200 font-bold' : ''; ?>"
               title="Aktifkan Decision Tree C4.5">
                <svg class="w-4 h-4 shrink-0 <?= $active_algo === 'c45' ? 'text-[#004b87]' : 'text-slate-400'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/>
                </svg>
                <div class="flex-1 flex items-center justify-between text-xs">
                    <span>Decision Tree C4.5</span>
                    <?php if ($active_algo === 'c45'): ?>
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <?php endif; ?>
                </div>
            </a>

            <!-- Naive Bayes Selector -->
            <a href="?algo=naive_bayes"
               class="sidebar-link <?= $active_algo === 'naive_bayes' ? 'bg-violet-50 text-violet-800 border-violet-200 font-bold' : ''; ?>"
               title="Aktifkan Gaussian Naive Bayes">
                <svg class="w-4 h-4 shrink-0 <?= $active_algo === 'naive_bayes' ? 'text-violet-600' : 'text-slate-400'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <div class="flex-1 flex items-center justify-between text-xs">
                    <span>Naive Bayes</span>
                    <?php if ($active_algo === 'naive_bayes'): ?>
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <?php endif; ?>
                </div>
            </a>
        </div>
        <?php endif; ?>

        <!-- Menu Sesi & Otentikasi -->
        <div class="pt-4 mt-3 border-t border-slate-100">
            <div class="px-2 py-1.5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Sesi Akun</div>

            <!-- Tombol Logout di Menu Sidebar -->
            <a href="<?= $root_path; ?>logout.php"
               onclick="return confirm('Apakah Anda yakin ingin keluar (logout) dari sistem EWS?')"
               class="sidebar-link text-rose-600 hover:text-rose-700 hover:bg-rose-50 hover:border-rose-200 group transition">
                <svg class="w-4 h-4 shrink-0 text-rose-500 group-hover:text-rose-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Keluar (Logout)</span>
            </a>
        </div>
    </nav>

    <!-- Profil Pengguna Aktif & Tombol Logout di Bawah Sidebar -->
    <div class="p-4 border-t border-slate-100 bg-slate-50/60">
        <!-- Kartu Identitas Pengguna -->
        <div class="flex items-center gap-2.5 p-2.5 rounded-xl bg-white border border-slate-200 shadow-xs">
            <div class="w-8 h-8 rounded-lg <?= htmlspecialchars($user_avatar_bg); ?> text-white font-extrabold flex items-center justify-center text-xs shrink-0 shadow-xs">
                <?= htmlspecialchars($user_initials); ?>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-xs font-bold text-slate-800 truncate" title="<?= htmlspecialchars($user_name); ?>">
                    <?= htmlspecialchars($user_name); ?>
                </div>
                <div class="text-[10px] text-slate-500 truncate" title="<?= htmlspecialchars($user_role); ?>">
                    <?= htmlspecialchars($user_role); ?>
                </div>
            </div>
        </div>

        <!-- Tombol Logout Khusus Bagian Bawah Sidebar -->
        <a href="<?= $root_path; ?>logout.php"
           onclick="return confirm('Apakah Anda yakin ingin keluar (logout) dari sistem EWS?')"
           class="mt-2.5 w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-bold text-rose-600 bg-white hover:bg-rose-50 hover:text-rose-700 border border-rose-200 transition shadow-xs">
            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            <span>Logout</span>
        </a>

        <div class="mt-2.5 text-[10px] text-center text-slate-400">
            <?= PRODI_NAME; ?> • FTII UHAMKA © <?= APP_YEAR; ?>
        </div>
    </div>

</aside>
