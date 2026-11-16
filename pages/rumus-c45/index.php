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
 * FILE       : pages/rumus-c45/index.php
 * FUNGSI     : Panduan Alur Keputusan C4.5, Pohon Keputusan, dan Evaluasi Model.
 *              Disajikan secara ringkas, intuitif, dan ramah pengguna umum.
 * ====================================================================================
 */

$root_path = '../../';
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/c45_engine.php';

$page_title = 'Panduan Alur Keputusan & Evaluasi Model';
$active_page = 'rumus-c45';
$nav_category = 'Panduan & Evaluasi';
$nav_title = 'Alur Keputusan C4.5 & Evaluasi Model EWS';

$eval = c45_get_evaluation_metrics();

require_once __DIR__ . '/../../components/header.php';
require_once __DIR__ . '/../../components/sidebar.php';
?>

<!-- Wrapper Konten Utama Kanan -->
<div class="flex-1 flex flex-col min-w-0 md:ml-72 min-h-screen">
    
    <!-- Topbar Navigasi -->
    <?php require_once __DIR__ . '/../../components/navbar.php'; ?>

    <!-- Main Content Body -->
    <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6">

        <!-- Banner Header Panduan -->
        <div class="bg-gradient-to-r from-[#003366] via-[#004b87] to-[#0284c7] p-6 sm:p-8 rounded-3xl text-white shadow-xl shadow-blue-900/15 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 text-white text-xs font-extrabold backdrop-blur-sm mb-3 border border-white/20">
                    <span>💡</span> Panduan Alur Keputusan & Kinerja Sistem
                </div>
                <h2 class="text-xl sm:text-2xl lg:text-3xl font-black tracking-tight">
                    Alur Logika Pohon Keputusan & Hasil Evaluasi
                </h2>
                <p class="text-blue-100 text-xs sm:text-sm mt-2 max-w-2xl leading-relaxed">
                    Penjelasan sederhana mengenai cara kerja model <b>Decision Tree C4.5</b> dalam mengklasifikasikan status kelulusan mahasiswa, dilengkapi matriks evaluasi pengujian pada <b>87 data testing</b>.
                </p>
            </div>
            <div class="flex flex-col gap-2 self-start md:self-center">
                <div class="px-4 py-2 bg-white/10 rounded-2xl border border-white/20 text-xs text-blue-100 font-mono">
                    Peneliti: <b><?= RESEARCHER_NAME; ?></b>
                </div>
                <div class="px-4 py-2 bg-white/10 rounded-2xl border border-white/20 text-xs text-blue-100 font-mono">
                    NIM: <b><?= RESEARCHER_NIM; ?></b> • FTII UHAMKA
                </div>
            </div>
        </div>

        <!-- ======================================================================== -->
        <!-- BAGIAN 1: PENJELASAN KRITERIA KEPUTUSAN C4.5                             -->
        <!-- ======================================================================== -->
        <div class="card-modern p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-base font-black text-slate-900">Kriteria Penentu Status Keberhasilan Studi</h3>
                    <p class="text-xs text-slate-500">Urutan parameter yang digunakan model untuk mendeteksi risiko studi mahasiswa</p>
                </div>
                <span class="text-xs font-extrabold text-[#004b87] bg-blue-50 px-3 py-1 rounded-xl border border-blue-200 self-start sm:self-auto">
                    Hierarki Pohon Keputusan
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <!-- Kartu 1: Kehadiran -->
                <div class="p-5 rounded-2xl bg-blue-50/70 border border-blue-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-black uppercase text-[#004b87] tracking-wider">Tahap 1 • Prioritas Utama</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-rose-100 text-rose-800 border border-rose-200">Kritis</span>
                        </div>
                        <h4 class="text-sm font-black text-slate-900">Kedisiplinan Kehadiran Kuliah</h4>
                        <div class="text-xs font-mono font-bold text-rose-700 mt-1">Batas Aman: > 72,50%</div>
                        <p class="text-xs text-slate-600 mt-2.5 leading-relaxed">
                            Kehadiran merupakan indikator perilaku awal (*leading indicator*). Jika kehadiran mahasiswa <b>≤ 72,50%</b>, sistem langsung mengelompokkannya sebagai <b>Berisiko Terhambat</b> tanpa memandang nilai IPK, karena ketidakhadiran berisiko menggugurkan hak ujian akhir.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-blue-200/80 text-[11px] text-[#004b87] font-semibold flex items-center gap-1.5">
                        <span>🎯</span> Simpul Utama (Root Node)
                    </div>
                </div>

                <!-- Kartu 2: IPK -->
                <div class="p-5 rounded-2xl bg-amber-50/70 border border-amber-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-black uppercase text-amber-900 tracking-wider">Tahap 2 • Prestasi Nilai</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-200">Akademik</span>
                        </div>
                        <h4 class="text-sm font-black text-slate-900">Capaian Indeks Prestasi Kumulatif</h4>
                        <div class="text-xs font-mono font-bold text-amber-800 mt-1">Batas Aman: > 2,995</div>
                        <p class="text-xs text-slate-600 mt-2.5 leading-relaxed">
                            Bagi mahasiswa yang kehadirannya sudah tertib (> 72,50%), sistem mengevaluasi pencapaian IPK. Mahasiswa dengan IPK <b>≤ 2,995</b> tetap dikategorikan <b>Berisiko Terhambat</b> dan disarankan mendapat bimbingan belajar remedial.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-amber-200/80 text-[11px] text-amber-800 font-semibold flex items-center gap-1.5">
                        <span>📊</span> Simpul Cabang (Node 2)
                    </div>
                </div>

                <!-- Kartu 3: Jalur Aman & SKS -->
                <div class="p-5 rounded-2xl bg-emerald-50/70 border border-emerald-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-black uppercase text-emerald-900 tracking-wider">Tahap 3 • Jalur Aman</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-900 border border-emerald-200">Aman</span>
                        </div>
                        <h4 class="text-sm font-black text-slate-900">Kelulusan Tepat Waktu 4 Tahun</h4>
                        <div class="text-xs font-mono font-bold text-emerald-800 mt-1">Hadir > 72,5% & IPK > 2,995</div>
                        <p class="text-xs text-slate-600 mt-2.5 leading-relaxed">
                            Mahasiswa yang memenuhi kedua kriteria di atas berada pada <b>Jalur Hijau Studi</b> dan diprediksi <b>Berpotensi Tepat Waktu</b>. Total SKS digunakan sebagai parameter pemantauan beban studi per semester.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-emerald-200/80 text-[11px] text-emerald-800 font-semibold flex items-center gap-1.5">
                        <span>✅</span> Jalur Hijau Studi
                    </div>
                </div>

            </div>
        </div>

        <!-- ======================================================================== -->
        <!-- BAGIAN 2: DIAGRAM ALUR POHON KEPUTUSAN C4.5                              -->
        <!-- ======================================================================== -->
        <div class="card-modern p-6 space-y-5">
            <div class="border-b border-slate-100 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h3 class="text-base font-black text-slate-900">Diagram Alur Pohon Keputusan C4.5</h3>
                    <p class="text-xs text-slate-500">Representasi visual alur pemeriksaan kondisi mahasiswa</p>
                </div>
                <span class="text-xs font-bold text-slate-500">FTII UHAMKA</span>
            </div>

            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-200 flex flex-col items-center space-y-6">
                
                <!-- Root Node -->
                <div class="p-4 rounded-2xl bg-gradient-to-r from-[#003366] to-[#004b87] text-white font-extrabold text-center shadow-lg shadow-blue-900/15 max-w-sm w-full border border-white/20">
                    <span class="text-[10px] uppercase tracking-wider block text-blue-200 font-bold">Langkah 1: Periksa Presensi Kuliah</span>
                    <span class="text-base">Parameter Kehadiran (%)</span>
                    <div class="mt-1 text-[11px] text-blue-100 font-medium">Ambang Batas = 72,50%</div>
                </div>

                <!-- Cabang dari Root Node -->
                <div class="w-full max-w-2xl grid grid-cols-1 md:grid-cols-2 gap-8 relative">
                    
                    <!-- Cabang Kiri: Kehadiran <= 72.50% -->
                    <div class="flex flex-col items-center space-y-3">
                        <div class="text-xs font-mono font-black text-rose-700 bg-rose-100 px-3.5 py-1.5 rounded-full border border-rose-300">
                            Kehadiran ≤ 72,50%
                        </div>
                        <div class="w-0.5 h-6 bg-rose-300"></div>
                        <div class="p-4 rounded-2xl bg-rose-100 border-2 border-rose-400 text-center w-full shadow-sm">
                            <span class="text-[10px] font-black uppercase text-rose-700 block">Hasil Keputusan (Aturan 1)</span>
                            <span class="text-sm font-black text-rose-950 block mt-0.5">Berisiko Terhambat</span>
                            <span class="text-[11px] text-rose-800 block mt-1">Pemicu: Presensi di bawah standar</span>
                        </div>
                    </div>

                    <!-- Cabang Kanan: Kehadiran > 72.50% -->
                    <div class="flex flex-col items-center space-y-3">
                        <div class="text-xs font-mono font-black text-emerald-800 bg-emerald-100 px-3.5 py-1.5 rounded-full border border-emerald-300">
                            Kehadiran > 72,50%
                        </div>
                        <div class="w-0.5 h-6 bg-emerald-300"></div>
                        
                        <!-- Internal Node 2: IPK -->
                        <div class="p-4 rounded-2xl bg-gradient-to-r from-amber-500 to-orange-500 text-white font-extrabold text-center shadow-md w-full border border-white/20">
                            <span class="text-[10px] uppercase tracking-wider block text-amber-100 font-bold">Langkah 2: Periksa Nilai Akademik</span>
                            <span class="text-sm">Rata-rata IPK</span>
                            <div class="mt-1 text-[11px] text-amber-100 font-medium">Ambang Batas = 2,995</div>
                        </div>

                        <!-- Sub Cabang IPK -->
                        <div class="w-full grid grid-cols-2 gap-3 pt-2">
                            <!-- IPK <= 2.995 -->
                            <div class="flex flex-col items-center space-y-2">
                                <div class="text-[10px] font-mono font-black text-amber-800 bg-amber-100 px-2 py-0.5 rounded border border-amber-300">
                                    IPK ≤ 2,995
                                </div>
                                <div class="p-3 rounded-xl bg-rose-100 border border-rose-300 text-center w-full">
                                    <span class="text-[9px] font-bold uppercase text-rose-700 block">Aturan 2</span>
                                    <span class="text-xs font-black text-rose-900 block">Berisiko Terhambat</span>
                                    <span class="text-[10px] text-rose-800 block">Perlu Remedial</span>
                                </div>
                            </div>

                            <!-- IPK > 2.995 -->
                            <div class="flex flex-col items-center space-y-2">
                                <div class="text-[10px] font-mono font-black text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded border border-emerald-300">
                                    IPK > 2,995
                                </div>
                                <div class="p-3 rounded-xl bg-emerald-100 border border-emerald-300 text-center w-full">
                                    <span class="text-[9px] font-bold uppercase text-emerald-800 block">Aturan 3</span>
                                    <span class="text-xs font-black text-emerald-950 block">Tepat Waktu</span>
                                    <span class="text-[10px] text-emerald-800 block">Jalur Aman</span>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>

        <!-- ======================================================================== -->
        <!-- BAGIAN 3: EVALUASI MODEL & HASIL PENGUJIAN (87 DATA TESTING)              -->
        <!-- ======================================================================== -->
        <div class="card-modern p-6 space-y-6 border-2 border-blue-200">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                <div>
                    <div class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-50 text-[#004b87] text-xs font-extrabold mb-1">
                        <span>🧪</span> Evaluasi Model & Uji Validasi
                    </div>
                    <h3 class="text-lg font-black text-slate-900">Hasil Pengujian Model EWS (87 Data Uji)</h3>
                    <p class="text-xs text-slate-500">
                        Hasil evaluasi pengujian empiris sistem menggunakan data mahasiswa FTII UHAMKA
                    </p>
                </div>
                <div class="text-right">
                    <span class="text-3xl font-black text-[#004b87] font-mono">94,25%</span>
                    <span class="text-xs text-slate-500 block font-semibold">Tingkat Akurasi Model</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                
                <!-- Tabel Confusion Matrix (5/12) -->
                <div class="lg:col-span-5 space-y-3">
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wide">
                        Matriks Konfusi Pengujian (87 Mahasiswa)
                    </h4>
                    
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 overflow-x-auto font-mono text-xs">
                        <table class="w-full text-center border-collapse">
                            <thead>
                                <tr>
                                    <th class="p-2"></th>
                                    <th colspan="2" class="p-2 border-b-2 border-slate-300 font-extrabold text-slate-700">Hasil Prediksi Model</th>
                                </tr>
                                <tr class="text-[11px] text-slate-500">
                                    <th class="p-2 text-left font-extrabold text-slate-700">Data Aktual</th>
                                    <th class="p-2 text-rose-700 font-bold bg-rose-50/50">Berisiko</th>
                                    <th class="p-2 text-emerald-700 font-bold bg-emerald-50/50">Tepat Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-t border-slate-200">
                                    <td class="p-2.5 text-left font-bold text-rose-700">Berisiko</td>
                                    <td class="p-2.5 bg-rose-100 font-black text-rose-900 border border-rose-200">
                                        Benar: 34
                                    </td>
                                    <td class="p-2.5 bg-amber-50 font-bold text-amber-900 border border-amber-200">
                                        Luput: 5
                                    </td>
                                </tr>
                                <tr class="border-t border-slate-200">
                                    <td class="p-2.5 text-left font-bold text-emerald-700">Tepat Waktu</td>
                                    <td class="p-2.5 bg-slate-100 font-bold text-slate-700 border border-slate-200">
                                        Alarm Palsu: 0
                                    </td>
                                    <td class="p-2.5 bg-emerald-100 font-black text-emerald-900 border border-emerald-200">
                                        Benar: 48
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-[11px] text-slate-600 leading-relaxed bg-white p-3.5 rounded-xl border border-slate-200 space-y-1">
                        <div>✅ Prediksi Tepat: <b>34 + 48 = 82 data (94,25%)</b></div>
                        <div>⚠️ Koreksi/Luput: <b>5 data (5,75%)</b></div>
                    </div>
                </div>

                <!-- Penjelasan Kinerja Sistem (7/12) -->
                <div class="lg:col-span-7 space-y-3.5">
                    
                    <!-- Kartu Error Alpha (α) -->
                    <div class="p-4 rounded-2xl bg-blue-50/80 border border-blue-200">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-[#004b87] uppercase tracking-wide">
                                1. Bebas Alarm Palsu (Error Alpha / α = 0,00%)
                            </span>
                            <span class="text-base font-black text-[#004b87] font-mono">0,00%</span>
                        </div>
                        <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                            Model memiliki tingkat kesalahan Tipe I sebesar <b>0%</b>. Artinya, sistem <b>tidak pernah memberikan alarm palsu</b> atau menuduh mahasiswa yang sebenarnya aman menjadi berisiko terhambat. DPA tidak perlu khawatir terbebani oleh peringatan yang salah.
                        </p>
                    </div>

                    <!-- Kartu Error Beta (β) -->
                    <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-amber-950 uppercase tracking-wide">
                                2. Tingkat Luput Deteksi (Error Beta / β = 12,82%)
                            </span>
                            <span class="text-base font-black text-amber-800 font-mono">12,82%</span>
                        </div>
                        <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                            Tingkat mahasiswa berisiko yang belum terjaring secara dini hanya <b>12,82%</b> (5 dari 39 mahasiswa), membuktikan model memiliki kepekaan yang sangat tinggi.
                        </p>
                    </div>

                    <!-- Kartu Power of Test / Sensitivitas -->
                    <div class="p-4 rounded-2xl bg-emerald-50/80 border border-emerald-200">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-emerald-950 uppercase tracking-wide">
                                3. Daya Deteksi Dini EWS (Sensitivitas = 87,18%)
                            </span>
                            <span class="text-base font-black text-emerald-800 font-mono">87,18%</span>
                        </div>
                        <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                            Kemampuan sistem dalam menjaring mahasiswa berisiko sejak awal mencapai <b>87,18%</b>. Ini membuktikan EWS sangat efektif sebagai sistem peringatan dini di Program Studi Teknik Informatika.
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </main>

<?php
require_once __DIR__ . '/../../components/footer.php';
?>
