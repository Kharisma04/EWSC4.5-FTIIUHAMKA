<?php
/**
 * FILE       : index.php
 * FUNGSI     : Halaman Dashboard Utama EWS.
 */

$root_path = './';
require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/c45_engine.php';
require_once __DIR__ . '/app/student_service.php';

$students = student_get_all();
$kpis     = student_get_kpis();

$page_title  = 'Dashboard';
$active_page = 'dashboard';
$nav_category = 'Sistem Pemantauan';
$nav_title    = 'Dashboard Mahasiswa';

require_once __DIR__ . '/components/header.php';
require_once __DIR__ . '/components/sidebar.php';
?>

<!-- Content Wrapper -->
<div class="flex-1 flex flex-col min-w-0 md:ml-[260px] min-h-screen">

    <?php require_once __DIR__ . '/components/navbar.php'; ?>

    <main class="flex-1 p-4 sm:p-6 space-y-5">

        <!-- Banner -->
        <div class="bg-gradient-to-r from-[#003366] to-[#0369a1] rounded-2xl p-6 text-white shadow-lg shadow-blue-900/20">
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/15 text-white text-[11px] font-bold border border-white/20 mb-3">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/></svg>
                Early Warning System · FTII UHAMKA
            </div>
            <h2 class="text-xl sm:text-2xl font-black tracking-tight">Pemantauan Keberhasilan Studi Mahasiswa</h2>
            <p class="text-blue-200 text-xs sm:text-sm mt-1.5 max-w-xl leading-relaxed">
                Sistem deteksi dini berbasis data untuk membantu Dosen PA memberikan intervensi tepat sasaran.
            </p>
            <a href="<?= $root_path; ?>pages/uji-coba/index.php"
               class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 bg-white text-[#004b87] font-bold text-xs rounded-lg shadow hover:bg-blue-50 transition">
                Coba Simulasi
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">

            <div class="card-modern p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#004b87] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900 "><?= $kpis['total']; ?></div>
                    <div class="text-[11px] text-slate-500 font-medium">Total Mahasiswa</div>
                </div>
            </div>

            <div class="card-modern p-4 flex items-center gap-3 border-l-4 border-l-emerald-500">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-black text-emerald-700 "><?= $kpis['tepat_waktu']; ?></div>
                    <div class="text-[11px] text-slate-500 font-medium">Tepat Waktu <span class="text-emerald-600 font-bold">(<?= $kpis['pct_tepat']; ?>%)</span></div>
                </div>
            </div>

            <div class="card-modern p-4 flex items-center gap-3 border-l-4 border-l-rose-500">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-black text-rose-700 "><?= $kpis['berisiko']; ?></div>
                    <div class="text-[11px] text-slate-500 font-medium">Berisiko <span class="text-rose-600 font-bold">(<?= $kpis['pct_berisiko']; ?>%)</span></div>
                </div>
            </div>

            <div class="card-modern p-4 flex items-center gap-3 border-l-4 border-l-violet-500">
                <div class="w-10 h-10 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-black text-violet-700 ">94,25%</div>
                    <div class="text-[11px] text-slate-500 font-medium">Akurasi Model</div>
                </div>
            </div>

        </div>

        <!-- Chart + Rules -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

            <!-- Donut Chart -->
            <div class="card-modern p-5 lg:col-span-4">
                <div class="font-bold text-slate-800 text-sm mb-4">Distribusi Status</div>
                <div class="relative flex items-center justify-center min-h-[200px]">
                    <canvas id="riskChart"></canvas>
                </div>
                <div class="text-[11px] text-slate-400 text-center mt-3">
                    Dari <b class="text-slate-600"><?= $kpis['total']; ?></b> mahasiswa aktif FTII UHAMKA
                </div>
            </div>

            <!-- Aturan Keputusan -->
            <div class="card-modern p-5 lg:col-span-8">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <div class="font-bold text-slate-800 text-sm">Kriteria Klasifikasi Status</div>
                        <div class="text-[11px] text-slate-400 mt-0.5">3 aturan keputusan yang digunakan sistem</div>
                    </div>
                    <a href="<?= $root_path; ?>pages/uji-coba/index.php"
                       class="text-xs font-bold text-[#004b87] hover:underline flex items-center gap-1">
                        Simulasi
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <div class="space-y-2.5">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-rose-50 border border-rose-200">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-700 font-black text-xs flex items-center justify-center shrink-0">R1</div>
                            <div>
                                <div class="text-xs font-bold text-slate-800 ">Kehadiran ≤ 72,50%</div>
                                <div class="text-[11px] text-slate-500">Presensi di bawah batas aman</div>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 border border-rose-300 shrink-0">Berisiko</span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-amber-50 border border-amber-200">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 font-black text-xs flex items-center justify-center shrink-0">R2</div>
                            <div>
                                <div class="text-xs font-bold text-slate-800 ">Hadir &gt;72,50% &amp; IPK ≤2,995</div>
                                <div class="text-[11px] text-slate-500">Capaian nilai akademik rendah</div>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-amber-100 text-amber-900 border border-amber-300 shrink-0">Berisiko</span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-emerald-50 border border-emerald-200">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 font-black text-xs flex items-center justify-center shrink-0">R3</div>
                            <div>
                                <div class="text-xs font-bold text-slate-800 ">Hadir &gt;72,50% &amp; IPK &gt;2,995</div>
                                <div class="text-[11px] text-slate-500">Jalur aman, kelulusan tepat waktu</div>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 shrink-0">Tepat Waktu</span>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                    <span>Klik <b class="text-slate-600">ℹ</b> pada nama mahasiswa untuk analisis lengkap</span>
                    <a href="<?= $root_path; ?>pages/uji-coba/index.php" class="text-[#004b87] font-bold hover:underline shrink-0">Uji baru →</a>
                </div>
            </div>

        </div>

        <!-- Tabel Data Mahasiswa -->
        <div class="card-modern overflow-hidden">

            <!-- Toolbar -->
            <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/60">
                <div>
                    <h3 class="font-black text-slate-800 text-sm">Data Mahasiswa Terpantau</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Total <b class="text-slate-600"><?= $kpis['total']; ?></b> mahasiswa dari dataset FTII UHAMKA</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">

                    <button onclick="resetStudents()"
                            class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-600 rounded-lg border border-slate-300 font-bold transition shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reset
                    </button>

                    <div class="inline-flex rounded-lg border border-slate-200 overflow-hidden text-xs font-bold">
                        <button data-filter="all"  class="filter-btn active px-3 py-1.5 bg-[#004b87] text-white transition">Semua</button>
                        <button data-filter="hambat" class="filter-btn px-3 py-1.5 bg-white text-slate-600 hover:bg-slate-50 transition">Berisiko</button>
                        <button data-filter="tepat"  class="filter-btn px-3 py-1.5 bg-white text-slate-600 hover:bg-slate-50 transition">Tepat Waktu</button>
                    </div>

                    <div class="relative">
                        <input type="text" id="tableSearch" placeholder="Cari NIM / Nama..."
                               class="text-xs bg-white border border-slate-300 rounded-lg pl-8 pr-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-400 w-44 sm:w-56 text-slate-900 shadow-sm">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                </div>
            </div>

            <!-- Tabel -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-widest">
                            <th class="py-3 px-3 text-center w-10">#</th>
                            <th class="py-3 px-3 w-28">NIM</th>
                            <th class="py-3 px-3">Nama</th>
                            <th class="py-3 px-3 text-center w-24">Kehadiran</th>
                            <th class="py-3 px-3 text-center w-20">IPK</th>
                            <th class="py-3 px-3 text-center w-20">SKS</th>
                            <th class="py-3 px-3 text-center w-36">Status</th>
                            <th class="py-3 px-3 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="studentTableBody" class="divide-y divide-slate-100 text-xs"></tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div id="tablePagination" class="p-4 border-t border-slate-100 bg-slate-50/40"></div>

        </div>

    </main>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const data = <?= json_encode($students, JSON_UNESCAPED_UNICODE); ?>;
        initStudentTable(data);
        initRiskChart(<?= $kpis['tepat_waktu']; ?>, <?= $kpis['berisiko']; ?>);
    });
    </script>

<?php require_once __DIR__ . '/components/footer.php'; ?>
