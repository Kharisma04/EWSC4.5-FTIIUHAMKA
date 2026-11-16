<?php
/**
 * ====================================================================================
 * APLIKASI   : Early Warning System (EWS) Prediksi Keberhasilan Studi Mahasiswa
 * METODE     : Algoritma Decision Tree model prediksi
 * SKRIPSI    : Penerapan Algoritma Decision Tree model prediksi Sebagai Early Warning System 
 *              Prediksi Keberhasilan Studi Mahasiswa FTII UHAMKA
 * PENELITI   : Kharisma Rahmawati Lubis (NIM: 2203015072)
 * INSTITUSI  : Program Studi Teknik Informatika, FTII UHAMKA Jakarta (2026)
 * ====================================================================================
 * FILE       : components/navbar.php
 * FUNGSI     : Komponen Topbar Navigasi Atas (Glassmorphism, Breadcrumb, Badges, Cetak).
 * ====================================================================================
 */

$nav_category = isset($nav_category) ? $nav_category : 'Sistem Pemantauan';
$nav_title = isset($nav_title) ? $nav_title : 'Dashboard & Pangkalan Data Mahasiswa';
?>
<!-- Topbar Header (Glassmorphism) -->
<header class="glass-header sticky top-0 z-30 border-b border-slate-200/80 px-4 sm:px-8 py-3.5 flex items-center justify-between no-print">
    
    <!-- Bagian Kiri: Tombol Menu Mobile, Logo Emblem Mobile & Breadcrumbs Dinamis -->
    <div class="flex items-center gap-3 sm:gap-4">
        <button onclick="toggleSidebar()" class="md:hidden p-2 rounded-xl text-slate-600 hover:text-[#004b87] hover:bg-blue-50 border border-slate-200 transition" aria-label="Buka Menu">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Logo Emblem UHAMKA untuk Tampilan Mobile -->
        <a href="<?= $root_path; ?>index.php" class="md:hidden flex items-center shrink-0" title="EWS FTII UHAMKA">
            <img src="<?= $root_path; ?>assets/images/uhamka-logo-emblem.webp" alt="Emblem UHAMKA" class="h-8 w-8 object-contain">
        </a>

        <div>
            <div class="text-[11px] font-extrabold text-[#004b87] uppercase tracking-wider">
                <?= htmlspecialchars($nav_category); ?>
            </div>
            <h1 class="text-base sm:text-lg font-black text-slate-900 tracking-tight leading-none mt-0.5">
                <?= htmlspecialchars($nav_title); ?>
            </h1>
        </div>
    </div>

    <!-- Bagian Kanan: Tombol Cetak Laporan -->
    <div class="flex items-center gap-2.5">
        <button onclick="window.print()" class="px-3.5 py-1.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl border border-slate-300 transition flex items-center gap-1.5 shadow-sm">
            <span>ðŸ–¨ï¸</span> <span class="hidden sm:inline">Cetak Laporan</span>
        </button>
    </div>

</header>

