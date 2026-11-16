<?php
/**
 * FILE       : components/sidebar.php
 * FUNGSI     : Komponen Sidebar Navigasi Samping.
 */

$active_page = isset($active_page) ? $active_page : 'dashboard';
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

    <!-- Menu Utama -->
    <nav class="p-3 flex-1 overflow-y-auto space-y-1" aria-label="Menu utama">
        <div class="px-2 py-2 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Menu</div>

        <a href="<?= $root_path; ?>index.php"
           class="sidebar-link <?= $active_page === 'dashboard' ? 'active' : ''; ?>"
           <?= $active_page === 'dashboard' ? 'aria-current="page"' : ''; ?>>
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Dashboard</span>
        </a>

        <a href="<?= $root_path; ?>pages/uji-coba/index.php"
           class="sidebar-link <?= $active_page === 'uji-coba' ? 'active' : ''; ?>"
           <?= $active_page === 'uji-coba' ? 'aria-current="page"' : ''; ?>>
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span>Form Simulasi</span>
        </a>


    </nav>

    <!-- Profil Peneliti -->
    <div class="p-4 border-t border-slate-100">
        <div class="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-50 border border-slate-200">
            <div class="w-8 h-8 rounded-lg bg-[#004b87] text-white font-extrabold flex items-center justify-center text-xs shrink-0">
                KR
            </div>
            <div class="min-w-0">
                <div class="text-xs font-bold text-slate-800 truncate"><?= RESEARCHER_NAME; ?></div>
                <div class="text-[10px] text-slate-500 ">NIM <?= RESEARCHER_NIM; ?></div>
            </div>
        </div>
        <div class="mt-2 text-[10px] text-center text-slate-400">
            <?= PRODI_NAME; ?> • FTII UHAMKA © <?= APP_YEAR; ?>
        </div>
    </div>

</aside>
