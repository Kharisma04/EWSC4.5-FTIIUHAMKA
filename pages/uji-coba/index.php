<?php
/**
 * FILE       : pages/uji-coba/index.php
 * FUNGSI     : Form Simulasi & Uji Prediksi Mandiri.
 */

$root_path = '../../';
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/c45_engine.php';
require_once __DIR__ . '/../../app/student_service.php';

$page_title   = 'Form Simulasi';
$active_page  = 'uji-coba';
$nav_category = 'Simulasi & Uji Coba';
$nav_title    = 'Form Simulasi Prediksi';

$saved_message = null;
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['btn_save_manual'])) {
    $nim      = trim($_POST['nim'] ?? '');
    $nama     = trim($_POST['nama'] ?? '');
    $kehadiran = (float)($_POST['kehadiran'] ?? 0);
    $ipk      = (float)($_POST['ipk'] ?? 0);
    $sks      = (int)($_POST['sks'] ?? 0);
    if ($nim && $nama && $kehadiran > 0) {
        student_save(['nim'=>$nim,'nama'=>$nama,'kehadiran'=>$kehadiran,'ipk'=>$ipk,'sks'=>$sks]);
        $saved_message = "Data mahasiswa <b>{$nama} ({$nim})</b> berhasil disimpan.";
    }
}

require_once __DIR__ . '/../../components/header.php';
require_once __DIR__ . '/../../components/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0 md:ml-[260px] min-h-screen">

    <?php require_once __DIR__ . '/../../components/navbar.php'; ?>

    <main class="flex-1 p-4 sm:p-6 space-y-5">

        <?php if ($saved_message): ?>
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between gap-3">
            <span><?= $saved_message; ?></span>
            <a href="<?= $root_path; ?>index.php" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition shrink-0">Lihat Dashboard</a>
        </div>
        <?php endif; ?>

        <!-- Banner -->
        <div class="bg-gradient-to-r from-[#003366] to-[#0369a1] rounded-2xl p-6 text-white shadow-lg shadow-blue-900/20">
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/15 text-white text-[11px] font-bold border border-white/20 mb-3">
                Simulasi Real-Time
            </div>
            <h2 class="text-xl sm:text-2xl font-black tracking-tight">Form Simulasi Prediksi</h2>
            <p class="text-blue-200 text-xs sm:text-sm mt-1.5 max-w-xl leading-relaxed">
                Masukkan parameter akademik mahasiswa untuk melihat hasil prediksi dan rekomendasi intervensi.
            </p>
        </div>

        <!-- Layout Dua Kolom -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

            <!-- KIRI: Form -->
            <div class="lg:col-span-7 space-y-4">

                <!-- Preset Skenario -->
                <div class="card-modern p-4">
                    <div class="text-xs font-bold text-slate-700 mb-3">Skenario Cepat</div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="fillSimulationPreset(60.0, 3.71, 101, 'Budi Santoso (Simulasi)')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200 transition">
                            <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>Presensi Rendah
                        </button>
                        <button type="button" onclick="fillSimulationPreset(85.0, 2.70, 95, 'Siti Nurhaliza (Simulasi)')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 transition">
                            <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>IPK Rendah
                        </button>
                        <button type="button" onclick="fillSimulationPreset(94.0, 3.85, 103, 'Ahmad Fauzan (Simulasi)')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-900 border border-emerald-200 transition">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>Jalur Aman
                        </button>
                    </div>
                </div>

                <!-- Form Input -->
                <div class="card-modern p-5">
                    <h3 class="text-sm font-black text-slate-800 mb-4">Parameter Mahasiswa</h3>

                    <form id="simForm" onsubmit="handleLiveSimSubmit(event)" class="space-y-4">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="simNim" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    NIM <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="simNim" required placeholder="Contoh: 2303015099"
                                       class="w-full text-xs bg-white border border-slate-300 rounded-lg px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-400 text-slate-900 font-bold transition">
                            </div>
                            <div>
                                <label for="simNama" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    Nama Lengkap <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="simNama" required placeholder="Nama mahasiswa"
                                       class="w-full text-xs bg-white border border-slate-300 rounded-lg px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-400 text-slate-900 font-semibold transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label for="simKehadiran" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    Kehadiran % <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" step="0.1" min="0" max="100" id="simKehadiran" required placeholder="0–100"
                                       class="w-full text-xs bg-white border border-slate-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-400 text-slate-900 font-bold transition">
                                <span class="text-[10px] text-slate-400 mt-1 block">Aman: &gt; 72,50%</span>
                            </div>
                            <div>
                                <label for="simIpk" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    IPK <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" step="0.01" min="0" max="4.00" id="simIpk" required placeholder="0.00–4.00"
                                       class="w-full text-xs bg-white border border-slate-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-400 text-slate-900 font-bold transition">
                                <span class="text-[10px] text-slate-400 mt-1 block">Aman: &gt; 2,995</span>
                            </div>
                            <div>
                                <label for="simSks" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    SKS <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" min="0" max="160" id="simSks" required placeholder="0–160"
                                       class="w-full text-xs bg-white border border-slate-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-400 text-slate-900 transition">
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-1">
                            <button type="submit" id="btnHitungSim"
                                    class="flex-1 py-2.5 px-5 rounded-lg bg-[#004b87] hover:bg-[#003b6b] text-white font-bold text-xs sm:text-sm shadow transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Hitung Prediksi
                            </button>
                            <button type="button" onclick="resetSimForm()"
                                    class="py-2.5 px-4 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition border border-slate-300">
                                Reset
                            </button>
                        </div>

                    </form>
                </div>

            </div>

            <!-- KANAN: Hasil -->
            <div class="lg:col-span-5">

                <!-- Placeholder -->
                <div id="simPlaceholder" class="card-modern p-8 text-center border-2 border-dashed border-slate-200">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 text-[#004b87] flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <h4 id="simPlaceholderTitle" class="text-sm font-black text-slate-700">Belum Ada Prediksi</h4>
                    <p id="simPlaceholderDesc" class="text-xs text-slate-400 mt-1.5 leading-relaxed max-w-xs mx-auto">
                        Isi parameter mahasiswa di formulir sebelah kiri, lalu klik <b>Hitung Prediksi</b>.
                    </p>
                </div>

                <!-- Kartu Hasil -->
                <div id="simResultSection" class="hidden card-modern overflow-hidden border-2 border-blue-300 shadow-lg fade-in">

                    <div class="bg-slate-50 p-4 border-b border-slate-200">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="text-[10px] font-black uppercase tracking-widest text-[#004b87]">Hasil Prediksi</span>
                            <span id="resStatusBadge" class="px-2.5 py-1 rounded-full font-bold text-[11px] border"></span>
                        </div>
                        <h3 id="resStudentName" class="text-sm font-black text-slate-900"></h3>
                    </div>

                    <div class="p-4 space-y-3.5" role="alert" aria-live="polite">

                        <!-- Aturan terpicu -->
                        <div class="p-3 bg-blue-50 rounded-xl border border-blue-200">
                            <div class="text-[10px] font-bold text-[#004b87] uppercase tracking-wider mb-1">Aturan Keputusan</div>
                            <div id="resRuleTitle" class="text-xs font-bold text-slate-900"></div>
                            <div id="resCoverage" class="text-[11px] text-[#004b87] mt-0.5"></div>
                        </div>

                        <!-- Analisis -->
                        <div>
                            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Analisis Kondisi</div>
                            <p id="resReason" class="text-xs text-slate-600 bg-white p-3 rounded-xl border border-slate-200 leading-relaxed"></p>
                        </div>

                        <!-- Rekomendasi -->
                        <div>
                            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Rekomendasi Dosen PA</div>
                            <p id="resAction" class="text-xs text-slate-700 bg-blue-50 p-3 rounded-xl border border-blue-200 font-medium leading-relaxed"></p>
                        </div>

                        <!-- Simpan -->
                        <div class="pt-2 border-t border-slate-100">
                            <button type="button" onclick="saveSimResultToDatabase()"
                                    class="w-full py-2.5 px-4 rounded-lg bg-[#004b87] hover:bg-[#003b6b] text-white font-bold text-xs shadow transition flex items-center justify-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                </svg>
                                Simpan ke Pangkalan Data
                            </button>
                            <p class="text-[10px] text-slate-400 text-center mt-1.5">Data akan tampil di Dashboard setelah disimpan.</p>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </main>

    <script>
    let currentSimResult = null;

    function fillSimulationPreset(kehadiran, ipk, sks, nama) {
        document.getElementById('simNim').value   = '2303015' + Math.floor(100 + Math.random() * 900);
        document.getElementById('simNama').value  = nama;
        document.getElementById('simKehadiran').value = kehadiran;
        document.getElementById('simIpk').value   = ipk;
        document.getElementById('simSks').value   = sks;
        hideSimResult();
        const t = document.getElementById('simPlaceholderTitle');
        const d = document.getElementById('simPlaceholderDesc');
        if (t) t.innerText = 'Skenario Siap';
        if (d) d.innerHTML = `Parameter <b>${nama}</b> telah diisi. Klik <b>Hitung Prediksi</b>.`;
        const btn = document.getElementById('btnHitungSim');
        if (btn) { btn.focus(); }
    }

    function handleLiveSimSubmit(e) {
        e.preventDefault();
        const nim  = document.getElementById('simNim').value.trim();
        const nama = document.getElementById('simNama').value.trim();
        const h    = parseFloat(document.getElementById('simKehadiran').value);
        const i    = parseFloat(document.getElementById('simIpk').value);
        const s    = parseInt(document.getElementById('simSks').value);

        if (!nim || !nama) { alert('Harap isi NIM dan Nama mahasiswa.'); return; }
        if (isNaN(h) || h < 0 || h > 100) { alert('Kehadiran harus antara 0–100%.'); document.getElementById('simKehadiran').focus(); return; }
        if (isNaN(i) || i < 0 || i > 4.0) { alert('IPK harus antara 0,00–4,00.'); document.getElementById('simIpk').focus(); return; }
        if (isNaN(s) || s < 0) { alert('SKS harus berupa angka positif.'); document.getElementById('simSks').focus(); return; }

        const btn      = document.getElementById('btnHitungSim');
        const origHTML = btn.innerHTML;
        btn.innerHTML  = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Memproses...';
        btn.disabled   = true;

        setTimeout(() => {
            btn.innerHTML = origHTML;
            btn.disabled  = false;
            calculateAndDisplay(h, i, s, nama, nim);
            if (window.innerWidth < 1024) {
                document.getElementById('simResultSection')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }, 180);
    }

    function calculateAndDisplay(kehadiran, ipk, sks, nama, nim) {
        const res = classifyC45Client(kehadiran, ipk);
        currentSimResult = { nim, nama, kehadiran, ipk, sks, status: res.status, rule: res.ruleNum };

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
    }

    function hideSimResult() {
        currentSimResult = null;
        document.getElementById('simResultSection')?.classList.add('hidden');
        document.getElementById('simPlaceholder')?.classList.remove('hidden');
    }

    function resetSimForm() {
        document.getElementById('simForm').reset();
        hideSimResult();
        const t = document.getElementById('simPlaceholderTitle');
        const d = document.getElementById('simPlaceholderDesc');
        if (t) t.innerText = 'Belum Ada Prediksi';
        if (d) d.innerHTML = 'Isi parameter mahasiswa di formulir sebelah kiri, lalu klik <b>Hitung Prediksi</b>.';
    }

    document.addEventListener('DOMContentLoaded', () => {
        ['simNim','simNama','simKehadiran','simIpk','simSks'].forEach(id => {
            document.getElementById(id)?.addEventListener('input', () => {
                if (currentSimResult !== null) hideSimResult();
            });
        });
    });

    async function saveSimResultToDatabase() {
        if (!currentSimResult) { alert('Lakukan perhitungan prediksi terlebih dahulu.'); return; }
        const apiUrl = (window.APP_ROOT || '../../') + 'app/api_student.php?action=add';
        const fd = new FormData();
        Object.entries(currentSimResult).forEach(([k,v]) => fd.append(k,v));
        try {
            const r    = await fetch(apiUrl, { method: 'POST', body: fd });
            const json = await r.json();
            if (json.success) {
                alert(`Data ${currentSimResult.nama} berhasil disimpan.`);
                window.location.href = (window.APP_ROOT || '../../') + 'index.php';
            } else {
                alert('Gagal menyimpan: ' + (json.message || 'Kesalahan tidak diketahui.'));
            }
        } catch (err) {
            alert('Terjadi kesalahan koneksi.');
        }
    }
    </script>

<?php require_once __DIR__ . '/../../components/footer.php'; ?>
