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
 * FILE       : pages/uji-coba/index.php
 * FUNGSI     : Fitur Khusus Form Simulasi & Uji Coba Data Mahasiswa Mandiri C4.5.
 * ====================================================================================
 */

$root_path = '../../';
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/c45_engine.php';
require_once __DIR__ . '/../../app/student_service.php';

$page_title = 'Simulasi & Uji Prediksi C4.5';
$active_page = 'uji-coba';
$nav_category = 'Klasifikasi Real-Time';
$nav_title = 'Form Simulasi & Uji Prediksi Mandiri C4.5';

// Penanganan Form POST (Jika disubmit via standar PHP)
$saved_message = null;
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['btn_save_manual'])) {
    $nim = trim($_POST['nim'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $kehadiran = (float)($_POST['kehadiran'] ?? 0);
    $ipk = (float)($_POST['ipk'] ?? 0);
    $sks = (int)($_POST['sks'] ?? 0);

    if ($nim && $nama && $kehadiran > 0) {
        $save_res = student_save([
            'nim' => $nim,
            'nama' => $nama,
            'kehadiran' => $kehadiran,
            'ipk' => $ipk,
            'sks' => $sks
        ]);
        $saved_message = "✅ Data mahasiswa <b>{$nama} ({$nim})</b> berhasil disimpan ke database pangkalan data!";
    }
}

require_once __DIR__ . '/../../components/header.php';
require_once __DIR__ . '/../../components/sidebar.php';
?>

<!-- Wrapper Konten Utama Kanan -->
<div class="flex-1 flex flex-col min-w-0 md:ml-72 min-h-screen">
    
    <!-- Topbar Navigasi -->
    <?php require_once __DIR__ . '/../../components/navbar.php'; ?>

    <!-- Main Content Body -->
    <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6">

        <!-- Notifikasi jika tersimpan via POST -->
        <?php if ($saved_message): ?>
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs sm:text-sm font-semibold flex items-center justify-between shadow-sm">
                <span><?= $saved_message; ?></span>
                <a href="<?= $root_path; ?>index.php" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition">
                    Lihat di Dashboard →
                </a>
            </div>
        <?php endif; ?>

        <!-- Banner Info Fitur -->
        <div class="bg-gradient-to-r from-blue-50/90 via-slate-50 to-blue-50/90 p-6 sm:p-8 rounded-3xl border border-blue-200/80 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-100 text-[#004b87] text-xs font-extrabold border border-blue-300 mb-2">
                    <span>🎯</span> Fitur Uji Coba Mandiri
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900">Simulasi & Uji Prediksi C4.5</h2>
                <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-2xl leading-relaxed">
                    Uji parameter akademik mahasiswa baru secara langsung. Sistem secara otomatis menerapkan pohon keputusan C4.5 dari hasil riset skripsi dan menyusun rekomendasi tindakan intervensi bagi Dosen PA.
                </p>
            </div>
            <div class="text-xs font-bold text-slate-600 bg-white px-4 py-2.5 rounded-2xl border border-slate-200 shadow-sm self-start md:self-center">
                Penyimpanan: File JSON Database (Permanen)
            </div>
        </div>

        <!-- 2 Kolom Berdampingan (Split Screen Layout) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- KOLOM KIRI (7/12): Formulir Input & Preset 1-Klik -->
            <div class="lg:col-span-7 space-y-5">
                
                <!-- Preset Cepat 1-Klik -->
                <div class="card-modern p-5">
                    <div class="text-xs font-bold text-slate-800 mb-3 flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <span>⚡</span> <b>Simulasi Cepat 1-Klik:</b>
                        </span>
                        <span class="text-[11px] text-[#004b87] font-semibold">Klik salah satu skenario</span>
                    </div>
                    <div class="flex flex-wrap gap-2.5 text-xs">
                        <!-- Skenario 1: Presensi Rendah -->
                        <button type="button" onclick="fillSimulationPreset(60.0, 3.71, 101, 'Budi Santoso (Simulasi Presensi)')" class="px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-900 font-bold rounded-xl border border-rose-300 transition flex items-center gap-1.5 shadow-sm">
                            <span>🔴</span> Presensi Rendah (60% | IPK 3.71)
                        </button>
                        <!-- Skenario 2: IPK Rendah -->
                        <button type="button" onclick="fillSimulationPreset(85.0, 2.70, 95, 'Siti Nurhaliza (Simulasi IPK)')" class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-950 font-bold rounded-xl border border-amber-300 transition flex items-center gap-1.5 shadow-sm">
                            <span>🟡</span> IPK Rendah (85% | IPK 2.70)
                        </button>
                        <!-- Skenario 3: Kondisi Aman -->
                        <button type="button" onclick="fillSimulationPreset(94.0, 3.85, 103, 'Ahmad Fauzan (Simulasi Aman)')" class="px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-950 font-bold rounded-xl border border-emerald-300 transition flex items-center gap-1.5 shadow-sm">
                            <span>🟢</span> Jalur Aman (94% | IPK 3.85)
                        </button>
                    </div>
                </div>

                <!-- Form Isian Parameter Mahasiswa -->
                <div class="card-modern p-6">
                    <h3 class="text-base font-extrabold text-slate-900 mb-4 flex items-center gap-2">
                        <span>📝</span> Parameter Data Mahasiswa yang Diuji
                    </h3>

                    <form id="simForm" onsubmit="handleLiveSimSubmit(event)" class="space-y-4">
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                                    Nomor Induk Mahasiswa (NIM) <span class="text-rose-600">*</span>
                                </label>
                                <input type="text" id="simNim" required placeholder="Contoh: 2303015099" class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-slate-900 font-mono font-bold transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                                    Nama Lengkap Mahasiswa <span class="text-rose-600">*</span>
                                </label>
                                <input type="text" id="simNama" required placeholder="Contoh: Fathur Rahman" class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-slate-900 font-semibold transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                                    Kehadiran (%) <span class="text-rose-600">*</span>
                                </label>
                                <input type="number" step="0.1" min="0" max="100" id="simKehadiran" required placeholder="0 - 100" class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-slate-900 font-mono font-bold transition">
                                <span class="text-[10px] text-slate-400 mt-1 block font-medium">Batas Aman: > 72,50%</span>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                                    Rata-rata IPK <span class="text-rose-600">*</span>
                                </label>
                                <input type="number" step="0.01" min="0" max="4.00" id="simIpk" required placeholder="0.00 - 4.00" class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-slate-900 font-mono font-bold transition">
                                <span class="text-[10px] text-slate-400 mt-1 block font-medium">Batas Aman: > 2,995</span>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                                    Total SKS Lulus <span class="text-rose-600">*</span>
                                </label>
                                <input type="number" min="0" max="160" id="simSks" required placeholder="Contoh: 102" class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-slate-900 font-mono font-medium transition">
                                <span class="text-[10px] text-slate-400 mt-1 block font-medium">Total SKS Terambil</span>
                            </div>
                        </div>

                        <div class="pt-3 flex flex-wrap items-center gap-3">
                            <button type="submit" id="btnHitungSim" class="flex-1 py-3 px-6 rounded-xl bg-gradient-to-r from-[#004b87] to-blue-600 hover:from-[#003b6b] hover:to-blue-700 text-white font-extrabold text-sm shadow-md shadow-blue-900/20 transition flex items-center justify-center gap-2">
                                <span>⚡</span> Hitung Prediksi C4.5
                            </button>
                            <button type="button" onclick="resetSimForm()" class="py-3 px-5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm transition border border-slate-300">
                                🔄 Bersihkan
                            </button>
                        </div>
                    </form>
                </div>

            </div>

            <!-- KOLOM KANAN (5/12): Hasil Prediksi Real-Time & Rekomendasi DPA -->
            <div class="lg:col-span-5 space-y-4">
                
                <!-- Placeholder saat belum ada data input -->
                <div id="simPlaceholder" class="card-modern p-8 text-center border-dashed border-2 border-slate-200">
                    <div class="w-16 h-16 rounded-3xl bg-blue-50 text-[#004b87] flex items-center justify-center text-3xl mx-auto mb-3 shadow-inner">
                        🎯
                    </div>
                    <h4 id="simPlaceholderTitle" class="text-base font-extrabold text-slate-800">Menunggu Eksekusi Prediksi</h4>
                    <p id="simPlaceholderDesc" class="text-xs text-slate-500 mt-1.5 leading-relaxed max-w-sm mx-auto">
                        Silakan isi data mahasiswa dan parameter akademik pada formulir di sebelah kiri (atau klik salah satu skenario preset), kemudian klik tombol <b>Hitung Prediksi C4.5</b> untuk menampilkan hasil analisis dan rekomendasi tindakan DPA.
                    </p>
                    <div class="mt-5 pt-4 border-t border-slate-100 text-[11px] text-slate-400 font-medium flex items-center justify-center gap-1.5">
                        <span>🔒 Klasifikasi Berbasis Algoritma Decision Tree C4.5</span>
                    </div>
                </div>

                <!-- Kartu Hasil Prediksi Live C4.5 -->
                <div id="simResultSection" class="hidden card-modern overflow-hidden border-2 border-blue-300 shadow-xl">
                    
                    <div class="bg-gradient-to-r from-blue-50 to-slate-50 p-5 border-b border-blue-200">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-black uppercase tracking-wider text-[#004b87]">Hasil Klasifikasi C4.5</span>
                            <span id="resStatusBadge" class="px-3.5 py-1.5 rounded-full font-extrabold text-xs border inline-flex items-center gap-1.5"></span>
                        </div>
                        <h3 id="resStudentName" class="text-lg font-black text-slate-900 mt-2"></h3>
                    </div>

                    <div class="p-5 space-y-4">
                        
                        <!-- Aturan Pohon yang Terpicu -->
                        <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200">
                            <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Aturan Pohon Keputusan:</div>
                            <div id="resRuleTitle" class="text-xs font-extrabold text-slate-900 mt-0.5"></div>
                            <div id="resCoverage" class="text-[11px] text-[#004b87] font-semibold mt-0.5"></div>
                        </div>

                        <!-- Analisis Kondisi Akademik -->
                        <div>
                            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wide mb-1 flex items-center gap-1.5">
                                <span>🔍</span> Analisis Kondisi Studi Mahasiswa:
                            </h4>
                            <p id="resReason" class="text-xs text-slate-600 bg-white p-3.5 rounded-2xl border border-slate-200 leading-relaxed font-medium">
                            </p>
                        </div>

                        <!-- Rekomendasi Tindakan Intervensi DPA -->
                        <div>
                            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wide mb-1 flex items-center gap-1.5">
                                <span>💡</span> Rekomendasi Tindakan Dosen Pembimbing Akademik (DPA):
                            </h4>
                            <p id="resAction" class="text-xs text-slate-800 bg-blue-50/70 p-3.5 rounded-2xl border border-blue-200 font-semibold leading-relaxed">
                            </p>
                        </div>

                        <!-- Tombol Simpan Hasil ke Pangkalan Data -->
                        <div class="pt-2 border-t border-slate-100">
                            <button type="button" id="btnSaveSimToDb" onclick="saveSimResultToDatabase()" class="w-full py-3 px-4 rounded-xl bg-[#004b87] hover:bg-[#003b6b] text-white font-extrabold text-xs sm:text-sm shadow-md transition flex items-center justify-center gap-2">
                                <span>💾</span> Simpan Hasil ke Pangkalan Data Mahasiswa
                            </button>
                            <p class="text-[10px] text-slate-400 text-center mt-2">
                                Data tersimpan ke pangkalan data dan dapat dilihat langsung di Dashboard.
                            </p>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </main>

    <!-- Script Khusus Halaman Uji Coba -->
    <script>
        let currentSimResult = null;

        // Memuat skenario simulasi preset tanpa otomatis memunculkan hasil prediksi
        function fillSimulationPreset(kehadiran, ipk, sks, nama) {
            document.getElementById('simNim').value = '2303015' + Math.floor(100 + Math.random() * 900);
            document.getElementById('simNama').value = nama;
            document.getElementById('simKehadiran').value = kehadiran;
            document.getElementById('simIpk').value = ipk;
            document.getElementById('simSks').value = sks;

            // Sembunyikan hasil lama & arahkan user untuk menekan tombol
            hideSimResult();
            
            const titleEl = document.getElementById('simPlaceholderTitle');
            const descEl = document.getElementById('simPlaceholderDesc');
            if (titleEl && descEl) {
                titleEl.innerText = 'Skenario Siap Dihitung';
                descEl.innerHTML = `Parameter simulasi <b>${nama}</b> telah dimasukkan ke formulir. Silakan klik tombol <b>Hitung Prediksi C4.5</b> untuk melihat hasil analisis.`;
            }

            // Beri fokus ke tombol hitung agar intuitif
            const btn = document.getElementById('btnHitungSim');
            if (btn) {
                btn.focus();
                btn.classList.add('ring-4', 'ring-blue-300');
                setTimeout(() => btn.classList.remove('ring-4', 'ring-blue-300'), 1000);
            }
        }

        // Penanganan Submit Form: Hasil HANYA muncul setelah user klik tombol
        function handleLiveSimSubmit(e) {
            e.preventDefault();
            const nim = document.getElementById('simNim').value.trim();
            const nama = document.getElementById('simNama').value.trim();
            const h = parseFloat(document.getElementById('simKehadiran').value);
            const i = parseFloat(document.getElementById('simIpk').value);
            const s = parseInt(document.getElementById('simSks').value);

            if (!nim || !nama) {
                alert('Harap lengkapi Nomor Induk Mahasiswa (NIM) dan Nama Mahasiswa terlebih dahulu!');
                return;
            }

            if (isNaN(h) || h < 0 || h > 100) {
                alert('Harap isi persentase kehadiran dengan angka valid antara 0% hingga 100%!');
                document.getElementById('simKehadiran').focus();
                return;
            }

            if (isNaN(i) || i < 0 || i > 4.00) {
                alert('Harap isi nilai IPK dengan angka valid antara 0,00 hingga 4,00!');
                document.getElementById('simIpk').focus();
                return;
            }

            if (isNaN(s) || s < 0) {
                alert('Harap isi total SKS dengan angka bulat yang valid!');
                document.getElementById('simSks').focus();
                return;
            }

            // Tampilkan efek loading sejenak pada tombol agar responsif & profesional
            const btn = document.getElementById('btnHitungSim');
            const origText = btn.innerHTML;
            btn.innerHTML = '<span>⏳</span> Memproses Prediksi C4.5...';
            btn.disabled = true;

            setTimeout(() => {
                btn.innerHTML = origText;
                btn.disabled = false;
                calculateAndDisplay(h, i, s, nama, nim);

                // Scroll halus ke kartu hasil pada layar mobile/tablet
                if (window.innerWidth < 1024) {
                    const sec = document.getElementById('simResultSection');
                    if (sec) {
                        sec.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            }, 200);
        }

        // Mengkalkulasi dan menampilkan kartu hasil prediksi C4.5
        function calculateAndDisplay(kehadiran, ipk, sks, nama, nim) {
            const res = classifyC45Client(kehadiran, ipk);
            currentSimResult = {
                nim: nim,
                nama: nama,
                kehadiran: kehadiran,
                ipk: ipk,
                sks: sks,
                status: res.status,
                rule: res.ruleNum
            };

            document.getElementById('simPlaceholder').classList.add('hidden');
            const sec = document.getElementById('simResultSection');
            sec.classList.remove('hidden');
            sec.classList.add('fade-in');

            document.getElementById('resStudentName').innerText = `${nama} (${nim})`;
            const badge = document.getElementById('resStatusBadge');
            badge.className = `px-3.5 py-1.5 rounded-full font-extrabold text-xs border ${res.badgeClass}`;
            badge.innerText = res.status;

            document.getElementById('resRuleTitle').innerText = res.ruleTitle;
            document.getElementById('resCoverage').innerText = res.coverage;
            document.getElementById('resReason').innerText = res.reason;
            document.getElementById('resAction').innerText = res.recommendation;
        }

        // Menyembunyikan hasil prediksi & menampilkan kembali placeholder awal
        function hideSimResult() {
            currentSimResult = null;
            const sec = document.getElementById('simResultSection');
            const placeholder = document.getElementById('simPlaceholder');
            if (sec) sec.classList.add('hidden');
            if (placeholder) placeholder.classList.remove('hidden');
        }

        function resetSimForm() {
            document.getElementById('simForm').reset();
            hideSimResult();
            const titleEl = document.getElementById('simPlaceholderTitle');
            const descEl = document.getElementById('simPlaceholderDesc');
            if (titleEl && descEl) {
                titleEl.innerText = 'Menunggu Eksekusi Prediksi';
                descEl.innerHTML = 'Silakan isi data mahasiswa dan parameter akademik pada formulir di sebelah kiri (atau klik salah satu skenario preset), kemudian klik tombol <b>Hitung Prediksi C4.5</b> untuk menampilkan hasil analisis dan rekomendasi tindakan DPA.';
            }
        }

        // Jika user mengubah nilai input setelah prediksi muncul, sembunyikan hasil lama agar tidak rancu
        document.addEventListener('DOMContentLoaded', () => {
            const formInputs = ['simNim', 'simNama', 'simKehadiran', 'simIpk', 'simSks'];
            formInputs.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('input', () => {
                        if (currentSimResult !== null) {
                            hideSimResult();
                            const titleEl = document.getElementById('simPlaceholderTitle');
                            const descEl = document.getElementById('simPlaceholderDesc');
                            if (titleEl && descEl) {
                                titleEl.innerText = 'Parameter Diubah';
                                descEl.innerHTML = 'Data pada formulir telah diubah. Silakan klik kembali tombol <b>Hitung Prediksi C4.5</b> untuk memperbarui hasil prediksi.';
                            }
                        }
                    });
                }
            });
        });

        async function saveSimResultToDatabase() {
            if (!currentSimResult) {
                alert('Silakan lakukan perhitungan prediksi terlebih dahulu dengan menekan tombol Hitung Prediksi C4.5.');
                return;
            }

            const apiUrl = (window.APP_ROOT || '../../') + 'app/api_student.php?action=add';
            const formData = new FormData();
            formData.append('nim', currentSimResult.nim);
            formData.append('nama', currentSimResult.nama);
            formData.append('kehadiran', currentSimResult.kehadiran);
            formData.append('ipk', currentSimResult.ipk);
            formData.append('sks', currentSimResult.sks);

            try {
                const res = await fetch(apiUrl, { method: 'POST', body: formData });
                const json = await res.json();
                if (json.success) {
                    alert(`✅ Data mahasiswa ${currentSimResult.nama} (${currentSimResult.nim}) berhasil disimpan ke database pangkalan data!`);
                    window.location.href = (window.APP_ROOT || '../../') + 'index.php';
                } else {
                    alert('Gagal menyimpan data: ' + (json.message || 'Terjadi kesalahan.'));
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan koneksi saat menyimpan data.');
            }
        }
    </script>

<?php
require_once __DIR__ . '/../../components/footer.php';
?>
