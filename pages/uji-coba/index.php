<?php
/**
 * FILE       : pages/uji-coba/index.php
 * FUNGSI     : Form Simulasi & Uji Prediksi Mandiri (C4.5 & Naive Bayes, Tanpa SKS, Validasi Ketat NIM FTII UHAMKA).
 */

$root_path = '../../';
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/auth.php';
auth_require($root_path);

require_once __DIR__ . '/../../app/c45_engine.php';
require_once __DIR__ . '/../../app/naive_bayes_engine.php';
require_once __DIR__ . '/../../app/algorithm_service.php';
require_once __DIR__ . '/../../app/student_service.php';

$active_algo = get_active_algorithm();
$algo_meta   = get_algorithm_metadata($active_algo);

$page_title   = 'Form Simulasi';
$active_page  = 'uji-coba';
$nav_category = 'Simulasi & Uji Coba';
$nav_title    = 'Form Simulasi Prediksi (' . $algo_meta['short_name'] . ')';

// Ambil data seluruh mahasiswa untuk lookup NIM instan di sisi klien
$all_students = student_get_all();
$student_nim_lookup = [];
foreach ($all_students as $s) {
    $student_nim_lookup[trim((string)$s['nim'])] = [
        'nama'      => $s['nama'],
        'kehadiran' => $s['kehadiran'],
        'ipk'       => $s['ipk'],
        'status'    => $s['status']
    ];
}

$saved_message = null;
$error_nim_message = null;

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['btn_save_manual'])) {
    $nim       = trim($_POST['nim'] ?? '');
    $nama      = trim($_POST['nama'] ?? '');
    $kehadiran = (float)($_POST['kehadiran'] ?? 0);
    $ipk       = (float)($_POST['ipk'] ?? 0);
    $algo      = trim($_POST['algo'] ?? $active_algo);

    // Validasi server-side
    $val = student_validate_uhamka_nim($nim);
    if (!$val['valid']) {
        $error_nim_message = $val['message'];
    } elseif ($nim && $nama && $kehadiran > 0) {
        $res = student_save([
            'nim'       => $nim,
            'nama'      => $nama,
            'kehadiran' => $kehadiran,
            'ipk'       => $ipk,
            'sks'       => 100
        ], $algo);

        if ($res['success']) {
            $saved_message = "Data mahasiswa <b>{$nama} ({$nim})</b> berhasil disimpan ke sistem.";
        } else {
            $error_nim_message = $res['message'];
        }
    }
}

require_once __DIR__ . '/../../components/header.php';
require_once __DIR__ . '/../../components/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0 md:ml-[260px] min-h-screen">

    <?php require_once __DIR__ . '/../../components/navbar.php'; ?>

    <main class="flex-1 p-4 sm:p-6 space-y-5">

        <!-- Notifikasi Sukses Simpan -->
        <?php if ($saved_message): ?>
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span><?= $saved_message; ?></span>
            </div>
            <a href="<?= $root_path; ?>index.php" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition shrink-0">Lihat Dashboard</a>
        </div>
        <?php endif; ?>

        <!-- Notifikasi Error Validasi Server -->
        <?php if ($error_nim_message): ?>
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-300 text-rose-900 text-xs font-semibold flex items-center gap-3 shadow-xs">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <div class="font-bold">Validasi NIM Ditolak!</div>
                <div class="text-[11px] text-rose-700 mt-0.5"><?= htmlspecialchars($error_nim_message); ?></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Banner & Selector Algoritma Simulasi -->
        <div class="bg-gradient-to-r <?= $active_algo === 'naive_bayes' ? 'from-[#3b0764] via-[#581c87] to-[#1e1b4b]' : 'from-[#003366] via-[#004b87] to-[#0369a1]'; ?> rounded-2xl p-6 text-white shadow-lg shadow-blue-900/20 transition-all duration-300">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/15 text-white text-[11px] font-bold border border-white/20">
                    <svg class="w-3.5 h-3.5 text-amber-300" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    Simulasi Real-Time · Model: <?= $algo_meta['name']; ?>
                </div>

                <!-- Pemilih Model Algoritma Simulasi -->
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

            <h2 class="text-xl sm:text-2xl font-black tracking-tight">Form Simulasi Prediksi Kelulusan</h2>
            <p class="text-blue-100 text-xs sm:text-sm mt-1.5 max-w-xl leading-relaxed">
                <?= $active_algo === 'c45' 
                    ? 'Hasil prediksi dihitung secara rule-based melalui pohon keputusan C4.5 berdasarkan ambang batas kehadiran (72,50%) dan IPK (2,995).' 
                    : 'Hasil prediksi dihitung secara probabilistik menggunakan Gaussian Naive Bayes berdasarkan kurva sebaran normal data riil FTII UHAMKA.'; ?>
            </p>
        </div>

        <!-- Layout Dua Kolom -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

            <!-- KIRI: Form Input Parameter -->
            <div class="lg:col-span-7 space-y-4">

                <!-- Preset Skenario Cepat -->
                <div class="card-modern p-4">
                    <div class="text-xs font-bold text-slate-700 mb-2.5 flex items-center justify-between">
                        <span>Skenario Uji Coba Cepat (FTII UHAMKA)</span>
                        <span class="text-[10px] text-slate-400 font-medium">Klik untuk auto-fill parameter</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="fillSimulationPreset('2203015001', 60.0, 3.71, 'MALIK EMIR HAFTA')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200 transition"
                                title="Kedua model sepakat Berisiko">
                            <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>Presensi Rendah (60%)
                        </button>
                        <button type="button" onclick="fillSimulationPreset('2203015075', 90.0, 2.88, 'MUHAMAD KAFIIL ANJANI')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-900 border border-purple-200 transition"
                                title="Uji Kasus Beda: C4.5 Berisiko vs Naive Bayes Tepat Waktu">
                            <span class="w-2 h-2 rounded-full bg-purple-500 shrink-0"></span>Uji Beda 1 (90%, 2.88)
                        </button>
                        <button type="button" onclick="fillSimulationPreset('2303015066', 75.0, 3.08, 'IJLAL HIBRIZI SURATMAN')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 transition"
                                title="Uji Kasus Beda: C4.5 Tepat Waktu vs Naive Bayes Berisiko">
                            <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>Uji Beda 2 (75%, 3.08)
                        </button>
                        <button type="button" onclick="fillSimulationPreset('2203015072', 92.0, 3.65, 'Kharisma Rahmawati Lubis')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-900 border border-emerald-200 transition"
                                title="Kedua model sepakat Tepat Waktu">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>Jalur Aman (92%, 3.65)
                        </button>
                    </div>
                </div>

                <!-- Formulir Parameter Mahasiswa (Validasi Ketat NIM FTII UHAMKA) -->
                <div class="card-modern p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-black text-slate-800">Parameter Akademik Mahasiswa</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Sistem membatasi khusus data mahasiswa FTII UHAMKA</p>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?= $active_algo === 'naive_bayes' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'; ?>">
                            Model: <?= $algo_meta['short_name']; ?>
                        </span>
                    </div>

                    <form id="simForm" onsubmit="handleLiveSimSubmit(event)" class="space-y-4">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Input NIM dengan Validasi Ketat Kampus UHAMKA -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label for="simNim" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide">
                                        NIM Mahasiswa <span class="text-rose-500">*</span>
                                    </label>
                                    <span class="text-[10px] text-blue-600 font-bold bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100">
                                        Format: 2x03015xxx
                                    </span>
                                </div>
                                <div class="relative">
                                    <input type="text" 
                                           id="simNim" 
                                           required 
                                           maxlength="10"
                                           placeholder="Contoh: 2203015072"
                                           oninput="handleNimInput(this.value)"
                                           class="w-full text-xs sm:text-sm bg-white border border-slate-300 rounded-lg px-3.5 py-2.5 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-400 text-slate-900 font-bold transition">
                                    <div id="nimCheckIcon" class="absolute right-3 top-2.5 hidden">
                                        <!-- Icon checkmark atau warning -->
                                    </div>
                                </div>
                                <!-- Pesan Status Validasi NIM Real-Time -->
                                <div id="nimValidationMsg" class="mt-1.5 text-xs hidden"></div>
                            </div>

                            <!-- Input Nama Mahasiswa -->
                            <div>
                                <label for="simNama" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                                    Nama Lengkap <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="simNama" required placeholder="Nama lengkap mahasiswa"
                                       class="w-full text-xs sm:text-sm bg-white border border-slate-300 rounded-lg px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-400 text-slate-900 font-semibold transition">
                            </div>
                        </div>

                        <!-- Kotak Peringatan Eror Jika NIM dari Kampus Lain -->
                        <div id="nimErrorBox" class="hidden p-3.5 rounded-xl bg-rose-50 border border-rose-300 text-rose-900 text-xs font-semibold flex items-start gap-3">
                            <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div class="flex-1">
                                <div class="font-bold text-rose-800">Pencarian Eror: Data Tidak Sesuai!</div>
                                <div id="nimErrorBoxText" class="text-[11px] text-rose-700 mt-0.5 leading-relaxed">
                                    NIM yang dimasukkan bukan mahasiswa FTII UHAMKA (Prodi Teknik Informatika). Sistem EWS menolak prediksi untuk data dari luar institusi!
                                </div>
                            </div>
                        </div>

                        <!-- 2 Kolom: Kehadiran dan IPK (Bagian SKS telah dihapus sesuai arahan riset skripsi) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="simKehadiran" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                                    Kehadiran Kuliah (%) <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" step="0.1" min="0" max="100" id="simKehadiran" required placeholder="0–100"
                                           class="w-full text-xs sm:text-sm bg-white border border-slate-300 rounded-lg px-3.5 py-2.5 pr-8 focus:outline-none focus:ring-2 focus:ring-blue-400 text-slate-900 font-bold transition">
                                    <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-bold">%</span>
                                </div>
                                <span class="text-[10px] text-slate-400 mt-1 block">
                                    Batas Aman C4.5: &gt; 72,50% · Rata-rata Tepat: 86,51%
                                </span>
                            </div>
                            <div>
                                <label for="simIpk" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                                    IPK (Indeks Prestasi) <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" step="0.01" min="0" max="4.00" id="simIpk" required placeholder="0.00–4.00"
                                       class="w-full text-xs sm:text-sm bg-white border border-slate-300 rounded-lg px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-400 text-slate-900 font-bold transition">
                                <span class="text-[10px] text-slate-400 mt-1 block">
                                    Batas Aman C4.5: &gt; 2,995 · Rata-rata Tepat: 3,37
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <button type="submit" id="btnHitungSim"
                                    class="flex-1 py-2.5 px-5 rounded-lg <?= $active_algo === 'naive_bayes' ? 'bg-purple-700 hover:bg-purple-800' : 'bg-[#004b87] hover:bg-[#003b6b]'; ?> text-white font-bold text-xs sm:text-sm shadow transition flex items-center justify-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span>Hitung Prediksi (<?= $algo_meta['short_name']; ?>)</span>
                            </button>
                            <button type="button" onclick="resetSimForm()"
                                    class="py-2.5 px-4 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition border border-slate-300">
                                Reset
                            </button>
                        </div>

                    </form>
                </div>

            </div>

            <!-- KANAN: Hasil Prediksi Berdasarkan Model -->
            <div class="lg:col-span-5">

                <!-- Placeholder Belum Ada Prediksi -->
                <div id="simPlaceholder" class="card-modern p-8 text-center border-2 border-dashed border-slate-200">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 text-[#004b87] flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <h4 id="simPlaceholderTitle" class="text-sm font-black text-slate-700">Belum Ada Prediksi</h4>
                    <p id="simPlaceholderDesc" class="text-xs text-slate-400 mt-1.5 leading-relaxed max-w-xs mx-auto">
                        Masukkan NIM FTII UHAMKA, nilai kehadiran, dan IPK pada formulir di sebelah kiri, lalu klik <b>Hitung Prediksi</b>.
                    </p>
                </div>

                <!-- Kartu Hasil Prediksi -->
                <div id="simResultSection" class="hidden card-modern overflow-hidden border-2 <?= $active_algo === 'naive_bayes' ? 'border-purple-300' : 'border-blue-300'; ?> shadow-lg fade-in">

                    <div class="bg-slate-50 p-4 border-b border-slate-200">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-black uppercase tracking-widest text-[#004b87]">Hasil Prediksi</span>
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded <?= $active_algo === 'naive_bayes' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'; ?>">
                                    <?= $algo_meta['short_name']; ?>
                                </span>
                            </div>
                            <span id="resStatusBadge" class="px-2.5 py-1 rounded-full font-bold text-[11px] border"></span>
                        </div>
                        <h3 id="resStudentName" class="text-sm font-black text-slate-900"></h3>
                    </div>

                    <div class="p-4 space-y-3.5" role="alert" aria-live="polite">

                        <!-- Panel Probabilitas Khusus Naive Bayes -->
                        <div id="nbProbabilityCard" class="<?= $active_algo === 'naive_bayes' ? '' : 'hidden'; ?> p-3.5 rounded-xl bg-purple-50/80 border border-purple-200">
                            <div class="text-[10px] font-bold text-purple-900 uppercase tracking-wider mb-2">
                                Probabilitas Posterior Naive Bayes P(Kelas | X)
                            </div>
                            <div class="space-y-2">
                                <div>
                                    <div class="flex items-center justify-between text-xs mb-1 font-semibold">
                                        <span class="text-emerald-800">Tepat Waktu:</span>
                                        <span id="nbPctTepatVal" class="font-bold text-emerald-700">0%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                        <div id="nbPctTepatBar" class="bg-emerald-500 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between text-xs mb-1 font-semibold">
                                        <span class="text-rose-800">Berisiko Terhambat:</span>
                                        <span id="nbPctBerisikoVal" class="font-bold text-rose-700">0%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                        <div id="nbPctBerisikoBar" class="bg-rose-500 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Aturan / Kriteria Terpicu -->
                        <div class="p-3 bg-blue-50 rounded-xl border border-blue-200">
                            <div class="text-[10px] font-bold text-[#004b87] uppercase tracking-wider mb-1">
                                <?= $active_algo === 'naive_bayes' ? 'Keputusan Teorema Bayes' : 'Aturan Pohon Keputusan C4.5'; ?>
                            </div>
                            <div id="resRuleTitle" class="text-xs font-bold text-slate-900"></div>
                            <div id="resCoverage" class="text-[11px] text-[#004b87] mt-0.5"></div>
                        </div>

                        <!-- Analisis Ilmiah -->
                        <div>
                            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Analisis Model</div>
                            <p id="resReason" class="text-xs text-slate-600 bg-white p-3 rounded-xl border border-slate-200 leading-relaxed"></p>
                        </div>

                        <!-- Rekomendasi Intervensi DPA -->
                        <div>
                            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Rekomendasi Dosen PA</div>
                            <p id="resAction" class="text-xs text-slate-700 bg-blue-50 p-3 rounded-xl border border-blue-200 font-medium leading-relaxed"></p>
                        </div>

                        <!-- Komparasi Langsung Kedua Algoritma (C4.5 vs Naive Bayes) -->
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-bold text-slate-600 uppercase tracking-wider">Komparasi Langsung Kedua Algoritma</span>
                                <span id="simCompMatchBadge" class="text-[9px] font-bold px-2 py-0.5 rounded-full"></span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div class="p-2.5 bg-white rounded-lg border border-slate-200">
                                    <div class="flex items-center justify-between text-[10px] font-bold text-slate-500 mb-1">
                                        <span>Decision Tree C4.5</span>
                                        <span class="text-[9px] px-1 py-0.2 bg-blue-50 text-[#004b87] rounded">Pohon</span>
                                    </div>
                                    <div id="simCompC45Status"></div>
                                    <div id="simCompC45Detail" class="text-[10px] text-slate-400 mt-1"></div>
                                </div>

                                <div class="p-2.5 bg-white rounded-lg border border-purple-200">
                                    <div class="flex items-center justify-between text-[10px] font-bold text-purple-700 mb-1">
                                        <span>Gaussian Naive Bayes</span>
                                        <span class="text-[9px] px-1 py-0.2 bg-purple-50 text-purple-700 rounded">Probabilistik</span>
                                    </div>
                                    <div id="simCompNbStatus"></div>
                                    <div id="simCompNbDetail" class="text-[10px] text-purple-600 mt-1"></div>
                                </div>
                            </div>

                            <div id="simCompDiffNote" class="mt-2.5 p-2 rounded-lg text-[11px] leading-relaxed"></div>
                        </div>

                        <!-- Tombol Simpan ke Database -->
                        <div class="pt-2 border-t border-slate-100">
                            <button type="button" onclick="saveSimResultToDatabase()"
                                    class="w-full py-2.5 px-4 rounded-lg bg-[#004b87] hover:bg-[#003b6b] text-white font-bold text-xs shadow transition flex items-center justify-center gap-2 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                </svg>
                                <span>Simpan Hasil ke Pangkalan Data</span>
                            </button>
                            <p class="text-[10px] text-slate-400 text-center mt-1.5">Data mahasiswa akan otomatis tersimpan dan terhitung di Dashboard.</p>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </main>

    <script>
    let currentSimResult = null;
    const currentAlgo = '<?= $active_algo; ?>';

    // Pangkalan data mahasiswa FTII UHAMKA untuk auto-lookup nama
    const masterStudentsMap = <?= json_encode($student_nim_lookup, JSON_UNESCAPED_UNICODE); ?>;

    /**
     * Memvalidasi format resmi NIM FTII UHAMKA (Prodi Teknik Informatika)
     * Format: 10 digit, dengan kode 03015 (contoh: 2203015072)
     */
    function validateUhamkaNim(nim) {
        nim = (nim || '').trim();
        if (!nim) {
            return { valid: false, message: 'NIM wajib diisi.' };
        }
        if (!/^\d{10}$/.test(nim)) {
            return {
                valid: false,
                message: `NIM (${nim}) tidak valid! NIM mahasiswa FTII UHAMKA harus 10 digit angka.`
            };
        }
        // Cek pola: XX03015XXX (03 = FTII, 015 = Teknik Informatika)
        const match = nim.match(/^(\d{2})03015(\d{3})$/);
        if (!match) {
            return {
                valid: false,
                message: `Pencarian Error: NIM (${nim}) bukan mahasiswa FTII UHAMKA (Prodi Teknik Informatika)! Data dari kampus lain ditolak oleh sistem EWS.`
            };
        }
        return {
            valid: true,
            angkatan: '20' + match[1],
            message: `NIM terverifikasi sebagai mahasiswa FTII UHAMKA (Angkatan 20${match[1]}).`
        };
    }

    /**
     * Handler Real-Time saat user mengetikkan NIM
     */
    function handleNimInput(nimVal) {
        nimVal = (nimVal || '').trim();
        const inputEl = document.getElementById('simNim');
        const errBox = document.getElementById('nimErrorBox');
        const errBoxText = document.getElementById('nimErrorBoxText');
        const msgEl = document.getElementById('nimValidationMsg');
        const btnHitung = document.getElementById('btnHitungSim');

        hideSimResult();

        if (nimVal.length === 0) {
            errBox.classList.add('hidden');
            msgEl.classList.add('hidden');
            inputEl.classList.remove('border-rose-500', 'border-emerald-500', 'ring-2', 'ring-rose-200');
            btnHitung.disabled = false;
            return;
        }

        // Jika sudah 10 digit atau mencoba memasukkan pola bukan UHAMKA
        if (nimVal.length === 10) {
            const check = validateUhamkaNim(nimVal);
            if (!check.valid) {
                // NIM dari kampus lain / tidak valid
                inputEl.classList.add('border-rose-500', 'ring-2', 'ring-rose-200');
                inputEl.classList.remove('border-emerald-500');
                errBox.classList.remove('hidden');
                errBoxText.innerText = check.message;

                msgEl.className = 'mt-1.5 text-xs font-bold text-rose-600 block';
                msgEl.innerHTML = `✕ Ditolak: Bukan mahasiswa FTII UHAMKA`;
                btnHitung.disabled = true;
            } else {
                // Format valid UHAMKA
                inputEl.classList.remove('border-rose-500', 'ring-2', 'ring-rose-200');
                inputEl.classList.add('border-emerald-500');
                errBox.classList.add('hidden');
                btnHitung.disabled = false;

                // Cek apakah ada di master database
                if (masterStudentsMap[nimVal]) {
                    const std = masterStudentsMap[nimVal];
                    document.getElementById('simNama').value = std.nama;
                    document.getElementById('simKehadiran').value = std.kehadiran;
                    document.getElementById('simIpk').value = std.ipk;

                    msgEl.className = 'mt-1.5 text-xs font-bold text-emerald-700 block';
                    msgEl.innerHTML = `✓ Terdaftar di Database FTII UHAMKA: <b>${std.nama}</b> (Angkatan ${check.angkatan})`;
                } else {
                    msgEl.className = 'mt-1.5 text-xs font-bold text-blue-700 block';
                    msgEl.innerHTML = `✓ Format NIM FTII UHAMKA Terverifikasi (Angkatan ${check.angkatan} - Mahasiswa Baru)`;
                }
            }
        } else if (nimVal.length > 10) {
            // Melebihi 10 digit (kampus lain yang NIM-nya panjang)
            inputEl.classList.add('border-rose-500', 'ring-2', 'ring-rose-200');
            errBox.classList.remove('hidden');
            errBoxText.innerText = `NIM (${nimVal}) melebihi 10 digit. NIM FTII UHAMKA tepat berjumlah 10 digit angka!`;
            btnHitung.disabled = true;
        } else {
            // Sedang mengetik
            errBox.classList.add('hidden');
            msgEl.classList.add('hidden');
            inputEl.classList.remove('border-rose-500', 'border-emerald-500', 'ring-2', 'ring-rose-200');
            btnHitung.disabled = false;
        }
    }

    function fillSimulationPreset(nim, kehadiran, ipk, nama) {
        document.getElementById('simNim').value       = nim;
        document.getElementById('simNama').value      = nama;
        document.getElementById('simKehadiran').value = kehadiran;
        document.getElementById('simIpk').value       = ipk;
        
        handleNimInput(nim);

        hideSimResult();
        const t = document.getElementById('simPlaceholderTitle');
        const d = document.getElementById('simPlaceholderDesc');
        if (t) t.innerText = 'Skenario Siap';
        if (d) d.innerHTML = `Parameter <b>${nama}</b> (${nim}) telah diisi. Klik <b>Hitung Prediksi</b>.`;
        const btn = document.getElementById('btnHitungSim');
        if (btn) { btn.focus(); }
    }

    function handleLiveSimSubmit(e) {
        e.preventDefault();
        const nim  = document.getElementById('simNim').value.trim();
        const nama = document.getElementById('simNama').value.trim();
        const h    = parseFloat(document.getElementById('simKehadiran').value);
        const i    = parseFloat(document.getElementById('simIpk').value);

        // Validasi Ketat NIM FTII UHAMKA sebelum kalkulasi
        const check = validateUhamkaNim(nim);
        if (!check.valid) {
            const errBox = document.getElementById('nimErrorBox');
            const errBoxText = document.getElementById('nimErrorBoxText');
            errBox.classList.remove('hidden');
            errBoxText.innerText = check.message;
            alert('Pencarian Error: Data tidak sesuai!\n\n' + check.message);
            document.getElementById('simNim').focus();
            return;
        }

        if (!nama) { alert('Harap isi Nama mahasiswa.'); document.getElementById('simNama').focus(); return; }
        if (isNaN(h) || h < 0 || h > 100) { alert('Kehadiran harus antara 0–100%.'); document.getElementById('simKehadiran').focus(); return; }
        if (isNaN(i) || i < 0 || i > 4.0) { alert('IPK harus antara 0,00–4,00.'); document.getElementById('simIpk').focus(); return; }

        const btn      = document.getElementById('btnHitungSim');
        const origHTML = btn.innerHTML;
        btn.innerHTML  = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Menghitung...';
        btn.disabled   = true;

        setTimeout(() => {
            btn.innerHTML = origHTML;
            btn.disabled  = false;
            calculateAndDisplay(h, i, nama, nim);
            if (window.innerWidth < 1024) {
                document.getElementById('simResultSection')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }, 150);
    }

    function calculateAndDisplay(kehadiran, ipk, nama, nim) {
        const c45Res = (typeof classifyC45Client === 'function') 
            ? classifyC45Client(kehadiran, ipk) 
            : classifyClient(kehadiran, ipk, 'c45');
        const nbRes  = (typeof classifyNaiveBayesClient === 'function') 
            ? classifyNaiveBayesClient(kehadiran, ipk) 
            : classifyClient(kehadiran, ipk, 'naive_bayes');

        const res = (currentAlgo === 'naive_bayes') ? nbRes : c45Res;

        currentSimResult = {
            nim,
            nama,
            kehadiran,
            ipk,
            sks: 100, // SKS default sistem
            status: res.status,
            rule: res.ruleNum,
            algo: currentAlgo,
            c45: c45Res,
            naive_bayes: nbRes
        };

        document.getElementById('simPlaceholder').classList.add('hidden');
        const sec = document.getElementById('simResultSection');
        sec.classList.remove('hidden');

        document.getElementById('resStudentName').innerText = `${nama} · ${nim}`;
        const badge = document.getElementById('resStatusBadge');
        badge.className = `px-2.5 py-1 rounded-full font-bold text-[11px] border ${res.badgeClass}`;
        badge.innerText = res.status;
        document.getElementById('resRuleTitle').innerText = res.ruleTitle;
        document.getElementById('resCoverage').innerText  = res.coverage;
        document.getElementById('resReason').innerText    = res.reason;
        document.getElementById('resAction').innerText    = res.recommendation;

        // Tampilkan progress probabilitas jika Naive Bayes
        const nbCard = document.getElementById('nbProbabilityCard');
        if (currentAlgo === 'naive_bayes' && res.pctTepat !== undefined) {
            nbCard.classList.remove('hidden');
            document.getElementById('nbPctTepatVal').innerText = res.pctTepat + '%';
            document.getElementById('nbPctTepatBar').style.width = res.pctTepat + '%';
            document.getElementById('nbPctBerisikoVal').innerText = res.pctBerisiko + '%';
            document.getElementById('nbPctBerisikoBar').style.width = res.pctBerisiko + '%';
        } else {
            nbCard.classList.add('hidden');
        }

        // Tampilkan Komparasi Kedua Algoritma
        const c45StatusEl = document.getElementById('simCompC45Status');
        const c45DetailEl = document.getElementById('simCompC45Detail');
        const nbStatusEl  = document.getElementById('simCompNbStatus');
        const nbDetailEl  = document.getElementById('simCompNbDetail');
        const matchBadge  = document.getElementById('simCompMatchBadge');
        const diffNote    = document.getElementById('simCompDiffNote');

        if (c45StatusEl) {
            c45StatusEl.innerHTML = `<span class="px-2 py-0.5 rounded text-[10px] font-bold inline-block border ${c45Res.badgeClass}">${c45Res.status}</span>`;
            c45DetailEl.innerText = `Aturan ${c45Res.ruleNum} (Ambang: Hadir 72,5%, IPK 2,995)`;
        }

        if (nbStatusEl) {
            nbStatusEl.innerHTML = `<span class="px-2 py-0.5 rounded text-[10px] font-bold inline-block border ${nbRes.badgeClass}">${nbRes.status}</span>`;
            nbDetailEl.innerText = `Peluang: ${nbRes.pctTepat}% Tepat vs ${nbRes.pctBerisiko}% Risiko`;
        }

        const isMatch = (c45Res.status === nbRes.status);
        if (matchBadge && diffNote) {
            if (isMatch) {
                matchBadge.className = 'text-[9px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200';
                matchBadge.innerText = 'Hasil Sepakat';
                diffNote.className = 'mt-2.5 p-2.5 rounded-lg text-[11px] leading-relaxed bg-emerald-50 border border-emerald-200 text-emerald-900 font-medium';
                diffNote.innerHTML = `✓ <b>Kedua algoritma menghasilkan prediksi yang sama</b> (${res.status}). Parameter akademik berada di luar zona batas kritis sehingga C4.5 dan Naive Bayes saling mengonfirmasi.`;
            } else {
                matchBadge.className = 'text-[9px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-300';
                matchBadge.innerText = 'Hasil Berbeda';
                diffNote.className = 'mt-2.5 p-2.5 rounded-lg text-[11px] leading-relaxed bg-amber-50 border border-amber-300 text-amber-950 font-medium';
                diffNote.innerHTML = `⚠️ <b>Perbedaan Hasil Prediksi:</b> Model C4.5 menghasilkan <b>${c45Res.status}</b> (Aturan ${c45Res.ruleNum}), sedangkan Naive Bayes menghasilkan <b>${nbRes.status}</b> (${nbRes.isAtRisk ? nbRes.pctBerisiko + '% risiko' : nbRes.pctTepat + '% tepat'}). Perbedaan ini terjadi karena C4.5 menggunakan ambang pemisah kaku (Hard Decision Boundary), sedangkan Naive Bayes memperhitungkan sebaran probabilitas kontinu (Gaussian PDF).`;
            }
        }
    }

    function hideSimResult() {
        currentSimResult = null;
        document.getElementById('simResultSection')?.classList.add('hidden');
        document.getElementById('simPlaceholder')?.classList.remove('hidden');
    }

    function resetSimForm() {
        document.getElementById('simForm').reset();
        hideSimResult();
        document.getElementById('nimErrorBox').classList.add('hidden');
        document.getElementById('nimValidationMsg').classList.add('hidden');
        const inputEl = document.getElementById('simNim');
        inputEl.classList.remove('border-rose-500', 'border-emerald-500', 'ring-2', 'ring-rose-200');
        document.getElementById('btnHitungSim').disabled = false;

        const t = document.getElementById('simPlaceholderTitle');
        const d = document.getElementById('simPlaceholderDesc');
        if (t) t.innerText = 'Belum Ada Prediksi';
        if (d) d.innerHTML = 'Masukkan NIM FTII UHAMKA, nilai kehadiran, dan IPK pada formulir di sebelah kiri, lalu klik <b>Hitung Prediksi</b>.';
    }

    document.addEventListener('DOMContentLoaded', () => {
        ['simNama','simKehadiran','simIpk'].forEach(id => {
            document.getElementById(id)?.addEventListener('input', () => {
                if (currentSimResult !== null) hideSimResult();
            });
        });
    });

    async function saveSimResultToDatabase() {
        if (!currentSimResult) { alert('Lakukan perhitungan prediksi terlebih dahulu.'); return; }
        
        // Verifikasi kembali sebelum kirim ke server
        const check = validateUhamkaNim(currentSimResult.nim);
        if (!check.valid) {
            alert('Pencarian Error: ' + check.message);
            return;
        }

        const apiUrl = (window.APP_ROOT || '../../') + 'app/api_student.php?action=add';
        const fd = new FormData();
        Object.entries(currentSimResult).forEach(([k,v]) => fd.append(k,v));
        try {
            const r    = await fetch(apiUrl, { method: 'POST', body: fd });
            const json = await r.json();
            if (json.success) {
                alert(`Data mahasiswa ${currentSimResult.nama} (${currentSimResult.nim}) berhasil disimpan.`);
                window.location.href = (window.APP_ROOT || '../../') + 'index.php';
            } else {
                alert('Pencarian Error / Gagal Menyimpan: ' + (json.message || 'Data tidak sesuai.'));
            }
        } catch (err) {
            alert('Terjadi kesalahan koneksi.');
        }
    }
    </script>

<?php require_once __DIR__ . '/../../components/footer.php'; ?>
