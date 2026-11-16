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
 * FILE       : index.php
 * FUNGSI     : Halaman Utama (Dashboard & Pangkalan Data Mahasiswa Terpantau EWS).
 * ====================================================================================
 */

// 1. Muat Konfigurasi & Service Data
$root_path = './';
require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/c45_engine.php';
require_once __DIR__ . '/app/student_service.php';

// 2. Ambil Data Mahasiswa & Metrik KPI dari JSON
$students = student_get_all();
$kpis = student_get_kpis();
$eval = c45_get_evaluation_metrics();

// 3. Konfigurasi Navigasi Komponen
$page_title = 'Dashboard & Pangkalan Data';
$active_page = 'dashboard';
$nav_category = 'Sistem Pemantauan EWS';
$nav_title = 'Dashboard & Pangkalan Data Mahasiswa';

// 4. Sertakan Header & Sidebar
require_once __DIR__ . '/components/header.php';
require_once __DIR__ . '/components/sidebar.php';
?>

<!-- Wrapper Konten Utama Kanan -->
<div class="flex-1 flex flex-col min-w-0 md:ml-72 min-h-screen">
    
    <!-- Topbar Navigasi -->
    <?php require_once __DIR__ . '/components/navbar.php'; ?>

    <!-- Main Content Body -->
    <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6">

        <!-- Banner Selamat Datang & Ringkasan EWS -->
        <div class="bg-gradient-to-r from-[#003366] via-[#004b87] to-[#0284c7] rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-blue-900/15 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-white text-xs font-extrabold backdrop-blur-sm mb-3 border border-white/20">
                    <span>ðŸŽ“</span> Early Warning System Mahasiswa FTII UHAMKA
                </div>
                <h2 class="text-xl sm:text-2xl lg:text-3xl font-black tracking-tight">
                    Sistem Pemantauan Status Keberhasilan Studi
                </h2>
                <p class="text-blue-100 text-xs sm:text-sm mt-2 max-w-2xl leading-relaxed">
                    Sistem peringatan dini deteksi kelulusan mahasiswa Teknik Informatika berbasis Algoritma <b>Decision Tree model prediksi</b>. Membantu Dosen Pembimbing Akademik (DPA) memberikan intervensi dini secara akurat dan tepat sasaran.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 self-start md:self-center">
                <a href="<?= $root_path; ?>pages/uji-coba/index.php" class="px-5 py-3 bg-white text-[#004b87] font-extrabold text-xs sm:text-sm rounded-2xl shadow-lg hover:bg-blue-50 transition flex items-center gap-2">
                    <span>ðŸŽ¯</span> Form Simulasi model prediksi
                </a>
            </div>
        </div>

        <!-- 4-Kolom KPI Strip (Ringkasan Data Riil) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            
            <!-- KPI 1: Total Mahasiswa -->
            <div class="card-modern p-5 flex items-center justify-between border-l-4 border-l-blue-500">
                <div>
                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Terpantau</div>
                    <div class="text-3xl font-black text-slate-900 mt-1 font-mono"><?= $kpis['total']; ?></div>
                    <div class="text-[11px] text-slate-500 mt-0.5 font-medium">Mahasiswa Aktif Terdata</div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold border border-blue-200">
                    ðŸ‘¥
                </div>
            </div>

            <!-- KPI 2: Berpotensi Tepat Waktu -->
            <div class="card-modern p-5 flex items-center justify-between border-l-4 border-l-emerald-500">
                <div>
                    <div class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Berpotensi Tepat Waktu</div>
                    <div class="text-3xl font-black text-emerald-700 mt-1 font-mono"><?= $kpis['tepat_waktu']; ?></div>
                    <div class="text-[11px] text-emerald-600 mt-0.5 font-medium">Jalur Aman (<?= $kpis['pct_tepat']; ?>%)</div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold border border-emerald-200">
                    âœ…
                </div>
            </div>

            <!-- KPI 3: Berisiko Terhambat -->
            <div class="card-modern p-5 flex items-center justify-between border-l-4 border-l-rose-500">
                <div>
                    <div class="text-[11px] font-bold text-rose-700 uppercase tracking-wider">Berisiko Terhambat</div>
                    <div class="text-3xl font-black text-rose-700 mt-1 font-mono"><?= $kpis['berisiko']; ?></div>
                    <div class="text-[11px] text-rose-600 mt-0.5 font-semibold">Perlu Bimbingan DPA (<?= $kpis['pct_berisiko']; ?>%)</div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl font-bold border border-rose-200">
                    ðŸš¨
                </div>
            </div>

            <!-- KPI 4: Akurasi Model model prediksi -->
            <div class="card-modern p-5 flex items-center justify-between border-l-4 border-l-purple-500">
                <div>
                    <div class="text-[11px] font-bold text-purple-700 uppercase tracking-wider">Akurasi Model model prediksi</div>
                    <div class="text-3xl font-black text-purple-700 mt-1 font-mono">94,25%</div>
                    <div class="text-[11px] text-purple-600 mt-0.5 font-medium">Model Teruji & Valid</div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-bold border border-purple-200">
                    ðŸŽ¯
                </div>
            </div>

        </div>

        <!-- 2 Kolom: Donut Chart Distribusi (Kiri) & Kriteria Ambang Batas EWS model prediksi (Kanan) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            
            <!-- Chart Donat (5/12) -->
            <div class="card-modern p-5 lg:col-span-5 flex flex-col justify-between">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-sm">Distribusi Status Kelulusan</h3>
                        <p class="text-[11px] text-slate-500">Persentase mahasiswa aman vs berisiko</p>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-blue-50 text-[#004b87] border border-blue-200">Real-Time</span>
                </div>
                <div class="relative flex-grow flex items-center justify-center min-h-[220px] my-4">
                    <canvas id="riskChart"></canvas>
                </div>
                <div class="text-xs text-slate-500 text-center border-t border-slate-100 pt-3">
                    Berdasarkan <b><?= $kpis['total']; ?></b> data mahasiswa aktif FTII UHAMKA
                </div>
            </div>

            <!-- Ambang Batas Aturan model prediksi (7/12) - Ringkas, Rapi, & Hemat Ruang -->
            <div class="card-modern p-5 lg:col-span-7 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3.5">
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-sm">Kriteria & Ambang Batas model prediksi</h3>
                            <p class="text-[11px] text-slate-500">Kaidah klasifikasi status keberhasilan studi</p>
                        </div>
                        <a href="<?= $root_path; ?>pages/rumus-c45/index.php" class="text-xs font-bold text-[#004b87] hover:underline flex items-center gap-1" title="Lihat diagram pohon keputusan lengkap">
                            <span>Alur Keputusan â†’</span>
                        </a>
                    </div>

                    <div class="space-y-2.5">
                        <!-- Rule 1: Presensi -->
                        <div class="p-3 rounded-2xl bg-rose-50/70 border border-rose-200 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 font-black text-xs flex items-center justify-center shrink-0">
                                    R1
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-black text-slate-900 font-mono">Kehadiran â‰¤ 72,50%</div>
                                    <div class="text-[11px] text-slate-500 truncate">Indikator utama kedisiplinan presensi kuliah</div>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 text-[11px] font-extrabold rounded-full bg-rose-100 text-rose-800 border border-rose-300 shrink-0">
                                Berisiko Terhambat
                            </span>
                        </div>

                        <!-- Rule 2: Akademik -->
                        <div class="p-3 rounded-2xl bg-amber-50/70 border border-amber-200 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 font-black text-xs flex items-center justify-center shrink-0">
                                    R2
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-black text-slate-900 font-mono">Hadir > 72,50% & IPK â‰¤ 2,995</div>
                                    <div class="text-[11px] text-slate-500 truncate">Indikator capaian nilai akademik semester</div>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 text-[11px] font-extrabold rounded-full bg-amber-100 text-amber-900 border border-amber-300 shrink-0">
                                Berisiko Terhambat
                            </span>
                        </div>

                        <!-- Rule 3: Jalur Aman -->
                        <div class="p-3 rounded-2xl bg-emerald-50/70 border border-emerald-200 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 font-black text-xs flex items-center justify-center shrink-0">
                                    R3
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-black text-slate-900 font-mono">Hadir > 72,50% & IPK > 2,995</div>
                                    <div class="text-[11px] text-slate-500 truncate">Jalur aman kelulusan tepat waktu 4 tahun</div>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 text-[11px] font-extrabold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 shrink-0">
                                Tepat Waktu
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Footer Baris Ringkas -->
                <div class="mt-3.5 pt-3 border-t border-slate-100 text-xs text-slate-600 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <span class="text-[11px]">ðŸ’¡ Klik <b>â„¹</b> pada nama mahasiswa untuk melihat analisis bimbingan DPA.</span>
                    <a href="<?= $root_path; ?>pages/uji-coba/index.php" class="text-xs text-[#004b87] font-bold hover:underline shrink-0">
                        Uji Mahasiswa Baru â†’
                    </a>
                </div>
            </div>

        </div>

        <!-- Tabel Pangkalan Data Mahasiswa Terpantau -->
        <div class="card-modern overflow-hidden">
            
            <!-- Toolbar Tabel (Filter, Pencarian, Reset) -->
            <div class="p-5 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-slate-50/70">
                <div>
                    <h3 class="font-black text-slate-900 text-base">Pangkalan Data Mahasiswa Terpantau EWS</h3>
                    <p class="text-xs text-slate-500">
                        Total <b><?= $kpis['total']; ?></b> data mahasiswa FTII UHAMKA dari dataset asli skripsi
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Tombol Reset Data -->
                    <button onclick="resetStudents()" title="Kembalikan pangkalan data ke 438 data awal dari file skripsi Excel" class="text-xs px-3.5 py-2 bg-white hover:bg-blue-50 text-slate-700 hover:text-[#004b87] rounded-xl border border-slate-300 hover:border-blue-300 font-bold transition flex items-center gap-1.5 shadow-sm">
                        <span>ðŸ”„</span> Reset Data Awal
                    </button>

                    <!-- Filter Kategori Pill -->
                    <div class="inline-flex rounded-xl border border-slate-300 p-0.5 bg-slate-100 text-xs font-bold">
                        <button data-filter="all" class="filter-btn active px-3.5 py-1.5 rounded-lg bg-[#004b87] text-white transition">Semua</button>
                        <button data-filter="hambat" class="filter-btn px-3.5 py-1.5 rounded-lg bg-white text-slate-700 transition">Berisiko</button>
                        <button data-filter="tepat" class="filter-btn px-3.5 py-1.5 rounded-lg bg-white text-slate-700 transition">Tepat Waktu</button>
                    </div>

                    <!-- Input Pencarian Instan -->
                    <div class="relative">
                        <input type="text" id="tableSearch" placeholder="Cari NIM atau Nama..." class="text-xs bg-white border border-slate-300 rounded-xl pl-3.5 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-48 sm:w-60 text-slate-900 font-medium shadow-sm">
                        <span class="absolute right-2.5 top-2 text-slate-400 text-xs">ðŸ”</span>
                    </div>
                </div>
            </div>

            <!-- Tabel Data Responsif -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100/80 border-b border-slate-200 text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">
                            <th class="py-3.5 px-3 text-center w-12">No</th>
                            <th class="py-3.5 px-3 w-28">NIM</th>
                            <th class="py-3.5 px-3">Nama Mahasiswa & Info</th>
                            <th class="py-3.5 px-3 text-center w-28">Kehadiran (%)</th>
                            <th class="py-3.5 px-3 text-center w-28">Rata-rata IPK</th>
                            <th class="py-3.5 px-3 text-center w-24">Total SKS</th>
                            <th class="py-3.5 px-3 text-center w-40">Status Prediksi</th>
                            <th class="py-3.5 px-3 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="studentTableBody" class="divide-y divide-slate-100 font-medium text-xs">
                        <!-- Baris tabel di-render otomatis oleh JavaScript agar filter, search, & pagination instan -->
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination Tabel -->
            <div id="tablePagination" class="p-4 border-t border-slate-100 bg-slate-50/50">
                <!-- Pagination di-render oleh JavaScript -->
            </div>

        </div>

    </main>

    <!-- Inisialisasi Data Awal ke JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const initialData = <?= json_encode($students, JSON_UNESCAPED_UNICODE); ?>;
            initStudentTable(initialData);
            initRiskChart(<?= $kpis['tepat_waktu']; ?>, <?= $kpis['berisiko']; ?>);
        });
    </script>

<?php
// 5. Sertakan Footer & Modal
require_once __DIR__ . '/components/footer.php';
?>

