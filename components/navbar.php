<?php
/**
 * FILE       : components/navbar.php
 * FUNGSI     : Topbar Navigasi Atas.
 */

$nav_category = isset($nav_category) ? $nav_category : 'Sistem EWS';
$nav_title    = isset($nav_title)    ? $nav_title    : 'Dashboard';
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
    <div class="flex items-center gap-2">
        <button onclick="window.print()"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-600 bg-white hover:bg-slate-50 rounded-lg border border-slate-300 transition shadow-sm focus-visible:outline-2 focus-visible:outline-[#004b87]"
                aria-label="Cetak laporan">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            <span class="hidden sm:inline">Cetak</span>
        </button>
    </div>

</header>
