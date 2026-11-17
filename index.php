<?php
/**
 * FILE       : index.php
 * FUNGSI     : Halaman Dashboard Utama EWS — Tampilan Terpisah Admin vs User Mahasiswa.
 */

$root_path = './';
require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/auth.php';
auth_require($root_path);

require_once __DIR__ . '/app/c45_engine.php';
require_once __DIR__ . '/app/naive_bayes_engine.php';
require_once __DIR__ . '/app/algorithm_service.php';
require_once __DIR__ . '/app/student_service.php';

$is_admin     = auth_is_admin();
$current_user = auth_user();
$active_algo  = get_active_algorithm();
$algo_meta    = get_algorithm_metadata($active_algo);

$page_title   = 'Dashboard';
$active_page  = 'dashboard';
$nav_category = 'Sistem Pemantauan';
$nav_title    = $is_admin
    ? 'Dashboard Administrator (' . $algo_meta['short_name'] . ')'
    : 'Portal Pemantauan Studi Mahasiswa';

// Data hanya dimuat penuh jika admin
$students = [];
$kpis     = [];
if ($is_admin) {
    $students = student_get_all_classified($active_algo);
    $kpis     = student_get_kpis($active_algo);
}

require_once __DIR__ . '/components/header.php';
require_once __DIR__ . '/components/sidebar.php';
?>

<!-- Content Wrapper -->
<div class="flex-1 flex flex-col min-w-0 md:ml-[260px] min-h-screen">

    <?php require_once __DIR__ . '/components/navbar.php'; ?>

    <main class="flex-1 p-4 sm:p-6 space-y-6">

        <?php if ($is_admin): ?>
        <!-- ============================================================
             DASHBOARD KHUSUS ADMINISTRATOR / DOSEN PA
             ============================================================ -->

        <!-- Banner Utama Admin -->
        <div class="bg-gradient-to-r <?= $active_algo === 'naive_bayes' ? 'from-[#3b0764] via-[#581c87] to-[#1e1b4b]' : 'from-[#003366] via-[#004b87] to-[#0369a1]'; ?> rounded-2xl p-6 text-white shadow-lg shadow-blue-900/20 transition-all duration-300">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/15 text-white text-[11px] font-bold border border-white/20">
                    <svg class="w-3.5 h-3.5 text-amber-300" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/></svg>
                    EWS FTII UHAMKA · Mode Analisis: <?= $algo_meta['name']; ?>
                </div>

                <!-- Switcher Model Algoritma -->
                <div class="inline-flex items-center p-1 rounded-xl bg-black/20 border border-white/15 text-xs font-bold">
                    <a href="?algo=c45" 
                       class="px-3 py-1 rounded-lg transition <?= $active_algo === 'c45' ? 'bg-white text-[#004b87] shadow-sm' : 'text-white/80 hover:text-white'; ?>">
                        Decision Tree C4.5
                    </a>
                    <a href="?algo=naive_bayes" 
                       class="px-3 py-1 rounded-lg transition <?= $active_algo === 'naive_bayes' ? 'bg-white text-purple-900 shadow-sm' : 'text-white/80 hover:text-white'; ?>">
                        Naive Bayes
                    </a>
                </div>
            </div>

            <h2 class="text-xl sm:text-2xl font-black tracking-tight">
                Panel Kendali Pemantauan Studi Mahasiswa
            </h2>
            <p class="text-blue-100 text-xs sm:text-sm mt-1.5 max-w-2xl leading-relaxed">
                <?= htmlspecialchars($algo_meta['description']); ?>
            </p>

            <div class="flex flex-wrap items-center gap-2.5 mt-5">
                <a href="<?= $root_path; ?>pages/uji-coba/index.php"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-[#004b87] font-bold text-xs rounded-xl shadow hover:bg-blue-50 transition">
                    Uji Form Simulasi
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
                <button onclick="openUploadModal()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-white/15 hover:bg-white/25 border border-white/30 text-white font-bold text-xs rounded-xl shadow transition cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Impor Data CSV / Excel
                </button>
            </div>
        </div>

        <!-- KPI Cards Admin -->
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="card-modern p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#004b87] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900"><?= $kpis['total']; ?></div>
                    <div class="text-[11px] text-slate-500 font-medium">Total Mahasiswa</div>
                </div>
            </div>

            <div class="card-modern p-4 flex items-center gap-3 border-l-4 border-l-emerald-500">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-2xl font-black text-emerald-700"><?= $kpis['tepat_waktu']; ?></div>
                    <div class="text-[11px] text-slate-500 font-medium">Tepat Waktu <span class="text-emerald-600 font-bold">(<?= $kpis['pct_tepat']; ?>%)</span></div>
                </div>
            </div>

            <div class="card-modern p-4 flex items-center gap-3 border-l-4 border-l-rose-500">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <div class="text-2xl font-black text-rose-700"><?= $kpis['berisiko']; ?></div>
                    <div class="text-[11px] text-slate-500 font-medium">Berisiko <span class="text-rose-600 font-bold">(<?= $kpis['pct_berisiko']; ?>%)</span></div>
                </div>
            </div>

            <div class="card-modern p-4 flex items-center gap-3 border-l-4 <?= $active_algo === 'naive_bayes' ? 'border-l-purple-500' : 'border-l-violet-500'; ?>">
                <div class="w-10 h-10 rounded-xl <?= $active_algo === 'naive_bayes' ? 'bg-purple-50 text-purple-600' : 'bg-violet-50 text-violet-600'; ?> flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <div class="text-2xl font-black <?= $active_algo === 'naive_bayes' ? 'text-purple-700' : 'text-violet-700'; ?>"><?= $algo_meta['accuracy']; ?></div>
                    <div class="text-[11px] text-slate-500 font-medium">Akurasi (<?= $algo_meta['short_name']; ?>)</div>
                </div>
            </div>
        </div>

        <!-- Visualisasi Chart + Kriteria Klasifikasi Admin -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <!-- Donut Chart -->
            <div class="card-modern p-5 lg:col-span-4">
                <div class="flex items-center justify-between mb-4">
                    <div class="font-bold text-slate-800 text-sm">Distribusi Status</div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?= $active_algo === 'naive_bayes' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'; ?>">
                        <?= $algo_meta['short_name']; ?>
                    </span>
                </div>
                <div class="relative flex items-center justify-center min-h-[200px]">
                    <canvas id="riskChart"></canvas>
                </div>
                <div class="text-[11px] text-slate-400 text-center mt-3">
                    Dari <b class="text-slate-600"><?= $kpis['total']; ?></b> mahasiswa aktif FTII UHAMKA
                </div>
            </div>

            <!-- Panel Kriteria Model -->
            <div class="card-modern p-5 lg:col-span-8">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <div class="font-bold text-slate-800 text-sm">
                            Kriteria Klasifikasi Model <?= $algo_meta['name']; ?>
                        </div>
                        <div class="text-[11px] text-slate-400 mt-0.5">
                            <?= $active_algo === 'c45'
                                ? 'Pohon keputusan berbasis Information Gain skripsi Bab 4'
                                : 'Probabilitas bersyarat Gaussian Naive Bayes dari data riil 437 mahasiswa'; ?>
                        </div>
                    </div>
                    <a href="<?= $root_path; ?>pages/uji-coba/index.php"
                       class="text-xs font-bold text-[#004b87] hover:underline flex items-center gap-1">
                        Simulasi
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <?php if ($active_algo === 'c45'): ?>
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-rose-50 border border-rose-200">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-700 font-black text-xs flex items-center justify-center shrink-0">R1</div>
                            <div>
                                <div class="text-xs font-bold text-slate-800">Simpul Akar (Root Node): Kehadiran ≤ 72,50%</div>
                                <div class="text-[11px] text-slate-500">Presensi di bawah batas aman (Gain = 0.720)</div>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 border border-rose-300 shrink-0">Berisiko</span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-amber-50 border border-amber-200">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 font-black text-xs flex items-center justify-center shrink-0">R2</div>
                            <div>
                                <div class="text-xs font-bold text-slate-800">Simpul Cabang: Hadir &gt; 72,50% &amp; IPK ≤ 2,995</div>
                                <div class="text-[11px] text-slate-500">Capaian nilai akademik rendah (Gain = 0.450)</div>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-amber-100 text-amber-900 border border-amber-300 shrink-0">Berisiko</span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-emerald-50 border border-emerald-200">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 font-black text-xs flex items-center justify-center shrink-0">R3</div>
                            <div>
                                <div class="text-xs font-bold text-slate-800">Kondisi Ideal: Hadir &gt; 72,50% &amp; IPK &gt; 2,995</div>
                                <div class="text-[11px] text-slate-500">Jalur aman studi, kelulusan tepat waktu 4 tahun</div>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 shrink-0">Tepat Waktu</span>
                    </div>
                </div>
                <?php else: ?>
                <div class="space-y-2.5">
                    <div class="p-3 rounded-xl bg-purple-50/70 border border-purple-200">
                        <div class="flex items-center justify-between mb-1">
                            <div class="text-xs font-bold text-slate-800 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                                Prior Probabilitas Kelas P(C)
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-purple-100 text-purple-800">Distribusi Data Riil</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs text-slate-600 mt-2">
                            <div class="p-2 bg-white rounded-lg border border-purple-100">
                                <div class="text-[10px] text-slate-400">P(Tepat Waktu)</div>
                                <div class="font-bold text-emerald-700">55,15% <span class="text-[10px] text-slate-400 font-normal">(241 mhs)</span></div>
                            </div>
                            <div class="p-2 bg-white rounded-lg border border-purple-100">
                                <div class="text-[10px] text-slate-400">P(Berisiko)</div>
                                <div class="font-bold text-rose-700">44,85% <span class="text-[10px] text-slate-400 font-normal">(196 mhs)</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Sebaran Kehadiran (Gaussian)</div>
                            <div class="text-[11px] text-slate-700">Tepat Waktu: <b>μ = 86,51%</b> (σ = 8,71)</div>
                            <div class="text-[11px] text-slate-700 mt-0.5">Berisiko: <b>μ = 58,94%</b> (σ = 14,26)</div>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Sebaran Nilai IPK (Gaussian)</div>
                            <div class="text-[11px] text-slate-700">Tepat Waktu: <b>μ = 3,37</b> (σ = 0,17)</div>
                            <div class="text-[11px] text-slate-700 mt-0.5">Berisiko: <b>μ = 3,26</b> (σ = 0,28)</div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                    <span>Model Aktif: <b class="text-slate-700"><?= $algo_meta['name']; ?></b></span>
                    <a href="<?= $root_path; ?>pages/uji-coba/index.php" class="text-[#004b87] font-bold hover:underline shrink-0">Buka Form Simulasi →</a>
                </div>
            </div>
        </div>

        <!-- Tabel Lengkap Mahasiswa Admin -->
        <div class="card-modern overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/60">
                <div>
                    <h3 class="font-black text-slate-800 text-sm">Pangkalan Data Mahasiswa Terpantau</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Total <b class="text-slate-600"><?= $kpis['total']; ?></b> mahasiswa terdaftar dalam sistem EWS</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button onclick="openUploadModal()"
                            class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 bg-[#004b87] hover:bg-[#003b6b] text-white rounded-lg font-bold transition shadow-sm cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <span>Upload Data</span>
                    </button>

                    <button onclick="resetStudents()"
                            class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-600 rounded-lg border border-slate-300 font-bold transition shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Reset
                    </button>

                    <div class="inline-flex rounded-lg border border-slate-200 overflow-hidden text-xs font-bold">
                        <button data-filter="all"   class="filter-btn active px-3 py-1.5 bg-[#004b87] text-white transition">Semua</button>
                        <button data-filter="hambat" class="filter-btn px-3 py-1.5 bg-white text-slate-600 hover:bg-slate-50 transition">Berisiko</button>
                        <button data-filter="tepat"  class="filter-btn px-3 py-1.5 bg-white text-slate-600 hover:bg-slate-50 transition">Tepat Waktu</button>
                    </div>

                    <div class="relative">
                        <input type="text" id="tableSearch" placeholder="Cari NIM / Nama..."
                               class="text-xs bg-white border border-slate-300 rounded-lg pl-8 pr-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-400 w-44 sm:w-56 text-slate-900 shadow-sm">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Tabel Data -->
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
                            <th class="py-3 px-3 text-center w-40">Status (<?= $algo_meta['short_name']; ?>)</th>
                            <th class="py-3 px-3 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="studentTableBody" class="divide-y divide-slate-100 text-xs"></tbody>
                </table>
            </div>

            <div id="tablePagination" class="p-4 border-t border-slate-100 bg-slate-50/40"></div>
        </div>

        <?php else: ?>
        <!-- ============================================================
             DASHBOARD KHUSUS USER / MAHASISWA (BERSIH & TANPA DATA/RUMUS)
             ============================================================ -->

        <!-- Banner Selamat Datang Mahasiswa -->
        <div class="bg-gradient-to-r from-[#003366] via-[#004b87] to-[#0369a1] rounded-2xl p-6 sm:p-8 text-white shadow-xl shadow-blue-900/20">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 text-white text-xs font-bold border border-white/20 mb-3">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Portal Mahasiswa · EWS FTII UHAMKA
            </div>
            
            <h2 class="text-xl sm:text-3xl font-black tracking-tight leading-snug">
                Selamat Datang, <?= htmlspecialchars($current_user['nama'] ?? 'Mahasiswa'); ?>!
            </h2>
            <p class="text-blue-100 text-xs sm:text-sm mt-2 max-w-2xl leading-relaxed">
                Sistem Peringatan Dini (Early Warning System) membantu Anda memantau kelancaran studi dan mendeteksi potensi hambatan kelulusan secara mandiri.
            </p>

            <div class="mt-6 flex flex-wrap items-center gap-3">
                <a href="<?= $root_path; ?>pages/uji-coba/index.php"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-[#004b87] font-black text-xs sm:text-sm rounded-xl shadow-md hover:bg-blue-50 transition transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4 text-[#004b87]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>Mulai Simulasi Prediksi Mandiri</span>
                </a>
            </div>
        </div>

        <!-- 3 Kartu Panduan Belajar & Indikator Studi Ramah Mahasiswa -->
        <div>
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-3 px-1">Indikator Kunci Keberhasilan Studi</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <div class="card-modern p-5 border-t-4 border-t-blue-600 flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#004b87] flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h4 class="font-bold text-slate-800 text-sm">Kedisiplinan Presensi</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Kehadiran perkuliahan adalah penentu utama. Pastikan selalu memenuhi syarat minimal presensi kuliah untuk dapat mengikuti ujian semester.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] font-semibold text-blue-700">
                        Indikator Utama Keberhasilan
                    </div>
                </div>

                <div class="card-modern p-5 border-t-4 border-t-emerald-600 flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <h4 class="font-bold text-slate-800 text-sm">Capaian Nilai Akademik</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Capaian Indeks Prestasi Kumulatif (IPK) mencerminkan pemahaman materi mata kuliah inti dan prasyarat tugas akhir / skripsi.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] font-semibold text-emerald-700">
                        Fokus Mata Kuliah Prasyarat
                    </div>
                </div>

                <div class="card-modern p-5 border-t-4 border-t-purple-600 flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <h4 class="font-bold text-slate-800 text-sm">Bimbingan Dosen PA</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Konsultasikan rencana Kartu Rencana Studi (KRS) dan kendala belajar secara berkala kepada Dosen Pembimbing Akademik Anda.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] font-semibold text-purple-700">
                        Konsultasi Rutin Setiap Semester
                    </div>
                </div>

            </div>
        </div>

        <!-- Alur 3 Langkah EWS & Layanan Mahasiswa -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
            
            <!-- Alur 3 Langkah -->
            <div class="card-modern p-5 lg:col-span-8 flex flex-col justify-between">
                <div>
                    <h3 class="text-sm font-black text-slate-800 mb-1">Alur Peringatan Dini Studi Mahasiswa</h3>
                    <p class="text-xs text-slate-400 mb-4">Bagaimana sistem EWS membantu Anda mencapai kelulusan tepat waktu 4 tahun</p>
                    
                    <div class="space-y-3.5">
                        <div class="flex items-start gap-3.5 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-[#004b87] font-black text-xs flex items-center justify-center shrink-0">
                                1
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-800">Cek Status di Form Simulasi</div>
                                <div class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                                    Masukkan NIM Anda, persentase kehadiran kuliah saat ini, dan nilai IPK terkini pada formulir simulasi.
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3.5 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 font-black text-xs flex items-center justify-center shrink-0">
                                2
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-800">Dapatkan Diagnosis & Rekomendasi Instan</div>
                                <div class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                                    Sistem akan memberikan diagnosis status studi (Tepat Waktu atau Berisiko) serta rekomendasi langkah tindakan nyata.
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3.5 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 font-black text-xs flex items-center justify-center shrink-0">
                                3
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-800">Tindak Lanjut & Bimbingan dengan Dosen PA</div>
                                <div class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                                    Jika hasil terdeteksi berisiko, segera hubungi Dosen Pembimbing Akademik untuk mendapatkan pendampingan remedial dan penjadwalan ulang beban SKS.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-400 font-medium">Siap memeriksa status akademik Anda?</span>
                    <a href="<?= $root_path; ?>pages/uji-coba/index.php" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#004b87] text-white text-xs font-bold rounded-lg hover:bg-[#003b6b] transition">
                        Buka Form Simulasi
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <!-- Kontak Bimbingan & Layanan Kampus -->
            <div class="card-modern p-5 lg:col-span-4 flex flex-col justify-between bg-gradient-to-br from-slate-50 to-blue-50/40">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-[#004b87] flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3 class="text-sm font-black text-slate-800">Layanan Akademik Mahasiswa</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Fakultas Teknologi Industri dan Informatika (FTII)<br>
                        <b>Universitas Muhammadiyah Prof. DR. HAMKA</b>
                    </p>

                    <div class="mt-4 space-y-2 text-xs">
                        <div class="p-2.5 rounded-lg bg-white border border-slate-200">
                            <span class="text-[10px] text-slate-400 font-bold uppercase block">Program Studi</span>
                            <span class="font-bold text-slate-800">Teknik Informatika (S1)</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-white border border-slate-200">
                            <span class="text-[10px] text-slate-400 font-bold uppercase block">Lokasi Kampus</span>
                            <span class="text-slate-700">Gedung FTII Kampus B UHAMKA, Pasar Rebo, Jakarta Timur</span>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-3 border-t border-slate-200/80 text-[11px] text-slate-400 text-center">
                    Konsultasi akademik wajib dilakukan minimal 3 kali per semester.
                </div>
            </div>

        </div>

        <?php endif; ?>

    </main>

    <script>
    window.IS_ADMIN = <?= $is_admin ? 'true' : 'false'; ?>;
    <?php if ($is_admin): ?>
    document.addEventListener('DOMContentLoaded', () => {
        const data = <?= json_encode($students, JSON_UNESCAPED_UNICODE); ?>;
        initStudentTable(data);
        initRiskChart(<?= $kpis['tepat_waktu']; ?>, <?= $kpis['berisiko']; ?>);
    });
    <?php endif; ?>
    </script>

<?php require_once __DIR__ . '/components/footer.php'; ?>
