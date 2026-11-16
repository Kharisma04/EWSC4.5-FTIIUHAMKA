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
 * FILE       : components/sidebar.php
 * FUNGSI     : Komponen Sidebar Navigasi Samping (Menu Fitur, Branding FTII, Profil).
 * ====================================================================================
 */

$active_page = isset($active_page) ? $active_page : 'dashboard';
?>
<!-- Sidebar Navigasi Kiri (Modern SaaS Style) -->
<aside id="appSidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200/90 flex flex-col justify-between transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out shadow-lg md:shadow-none no-print">
    
    <!-- Bagian Atas: Branding & Logo Kampus UHAMKA -->
    <div class="p-5 border-b border-slate-100 bg-white">
        <div class="flex items-center justify-between">
            <a href="<?= $root_path; ?>index.php" class="flex items-center gap-2 group block" title="Universitas Muhammadiyah Prof. DR. HAMKA">
                <img src="<?= $root_path; ?>assets/images/uhamka-logo-horizontal.webp" alt="Logo UHAMKA" class="h-10 w-auto object-contain transition group-hover:opacity-90">
            </a>

            <!-- Tombol Tutup Mobile -->
            <button onclick="toggleSidebar()" class="md:hidden p-1.5 rounded-lg text-slate-400 hover:text-[#004b87] hover:bg-blue-50 transition" title="Tutup Navigasi">
                ✕
            </button>
        </div>

        <div class="mt-3 text-[11px] text-slate-600 font-medium leading-relaxed bg-blue-50/70 p-2.5 rounded-xl border border-blue-100/80">
            <div class="flex items-center gap-1.5 font-bold text-[#004b87]">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>EWS C4.5 FTII UHAMKA</span>
            </div>
            <p class="text-[10px] text-slate-500 mt-0.5">Early Warning System Keberhasilan Studi Mahasiswa</p>
        </div>
    </div>

    <!-- Menu Utama Aplikasi -->
    <div class="p-4 flex-1 overflow-y-auto space-y-1.5">
        <div class="px-3 py-1.5 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
            Menu Utama
        </div>

        <!-- 1. Menu Dashboard -->
        <a href="<?= $root_path; ?>index.php" class="sidebar-link <?= $active_page === 'dashboard' ? 'active' : ''; ?> text-xs sm:text-sm">
            <span class="text-base">📊</span>
            <span>Dashboard & Data Mhs</span>
        </a>

        <!-- 2. Menu Form Uji Coba -->
        <a href="<?= $root_path; ?>pages/uji-coba/index.php" class="sidebar-link <?= $active_page === 'uji-coba' ? 'active' : ''; ?> text-xs sm:text-sm">
            <span class="text-base">🎯</span>
            <span>Form Simulasi & Uji C4.5</span>
        </a>

    </div>

    <!-- Bagian Bawah: Profil Peneliti Skripsi -->
    <div class="p-4 border-t border-slate-100 bg-slate-50/70">
        <div class="flex items-center gap-3 p-2 rounded-xl bg-white border border-slate-200/80 shadow-sm">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#004b87] to-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-sm">
                KR
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-xs font-bold text-slate-900 truncate"><?= RESEARCHER_NAME; ?></div>
                <div class="text-[10px] text-slate-500 font-mono">NIM: <?= RESEARCHER_NIM; ?></div>
            </div>
        </div>
        <div class="mt-2 text-[10px] text-center text-slate-400 font-medium">
            <?= PRODI_NAME; ?> • FTII UHAMKA © <?= APP_YEAR; ?>
        </div>
    </div>

</aside>
