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
 * FILE       : components/footer.php
 * FUNGSI     : Komponen Footer modular, Modal Info Detail (ℹ️), Modal Edit (✏️), & Scripts.
 * ====================================================================================
 */

if (!isset($root_path)) {
    $root_path = './';
}
?>
    <!-- Footer Bawah Halaman -->
    <footer class="bg-white border-t border-slate-200/80 mt-auto text-xs text-slate-500 py-4 px-4 sm:px-8 no-print">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
            <div>
                © <?= APP_YEAR; ?> <b><?= FAKULTAS_NAME; ?> UHAMKA</b> • Program Studi <?= PRODI_NAME; ?>
            </div>
            <div>
                Riset Skripsi: <b><?= RESEARCHER_NAME; ?></b> (NIM: <?= RESEARCHER_NIM; ?>) • Algoritma Decision Tree C4.5
            </div>
        </div>
    </footer>

</div> <!-- Penutup Wrapper Konten Kanan -->

<!-- ================================================================================ -->
<!-- MODAL 1: INFO & DIAGNOSIS DETAIL MAHASISWA (ℹ️)                                     -->
<!-- Menjabarkan mengapa mahasiswa tersebut berisiko terhambat atau berpotensi tepat  -->
<!-- waktu berdasarkan parameter kehadiran, IPK, aturan C4.5, dan rekomendasi DPA.     -->
<!-- ================================================================================ -->
<div id="studentInfoModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4 transition-opacity">
    <div class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl border border-slate-200 transform transition-transform duration-200 scale-95" id="infoModalCard">
        
        <!-- Header Modal -->
        <div id="infoModalHeader" class="p-6 border-b border-slate-100 bg-gradient-to-r from-blue-50 to-slate-50 flex items-start justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-black uppercase tracking-wider text-[#004b87]">Diagnosis Status EWS</span>
                    <span id="infoStatusBadge" class="px-3 py-1 rounded-full font-extrabold text-[11px] border"></span>
                </div>
                <h3 id="infoStudentName" class="text-lg font-black text-slate-900 mt-1.5 leading-tight"></h3>
                <div id="infoStudentNim" class="text-xs text-slate-500 font-mono mt-0.5"></div>
            </div>
            <button onclick="closeInfoModal()" class="p-2 rounded-xl text-slate-400 hover:text-[#004b87] hover:bg-white transition border border-transparent hover:border-slate-200">
                ✕
            </button>
        </div>

        <!-- Body Modal -->
        <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
            
            <!-- Parameter Riil Mahasiswa -->
            <div class="grid grid-cols-3 gap-2.5 p-3 rounded-2xl bg-slate-50 border border-slate-200/80 text-center font-mono">
                <div class="p-2 bg-white rounded-xl border border-slate-100 shadow-sm">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Kehadiran</span>
                    <span id="infoKehadiranVal" class="text-sm font-black text-slate-800"></span>
                </div>
                <div class="p-2 bg-white rounded-xl border border-slate-100 shadow-sm">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Rata-rata IPK</span>
                    <span id="infoIpkVal" class="text-sm font-black text-slate-800"></span>
                </div>
                <div class="p-2 bg-white rounded-xl border border-slate-100 shadow-sm">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Total SKS</span>
                    <span id="infoSksVal" class="text-sm font-black text-slate-800"></span>
                </div>
            </div>

            <!-- Aturan Pohon Keputusan C4.5 yang Terpicu -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
                <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Aturan C4.5 yang Terpicu:</div>
                <div id="infoRuleTitle" class="text-xs font-black text-slate-900 mt-1"></div>
                <div id="infoCoverage" class="text-[11px] font-semibold text-[#004b87] mt-0.5"></div>
            </div>

            <!-- Penjabaran Penyebab / Alasan Status -->
            <div>
                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5 flex items-center gap-1.5">
                    <span>🔍</span> Penjabaran Kondisi Studi Mahasiswa:
                </h4>
                <p id="infoReason" class="text-xs text-slate-600 bg-white p-3.5 rounded-2xl border border-slate-200 leading-relaxed font-medium">
                </p>
            </div>

            <!-- Rekomendasi Tindakan DPA -->
            <div>
                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5 flex items-center gap-1.5">
                    <span>💡</span> Rekomendasi Tindakan Dosen Pembimbing Akademik (DPA):
                </h4>
                <p id="infoRecommendation" class="text-xs text-slate-800 bg-blue-50/70 p-3.5 rounded-2xl border border-blue-200/80 leading-relaxed font-semibold">
                </p>
            </div>

        </div>

        <!-- Footer Modal -->
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button onclick="closeInfoModal()" class="px-5 py-2.5 bg-[#004b87] hover:bg-[#003b6b] text-white font-bold text-xs rounded-xl shadow-md transition">
                Tutup Informasi
            </button>
        </div>

    </div>
</div>

<!-- ================================================================================ -->
<!-- MODAL 2: FORM EDIT DATA MAHASISWA (✏️)                                            -->
<!-- Memungkinkan pengguna mengubah parameter NIM, Nama, Kehadiran, IPK, SKS,         -->
<!-- dan otomatis menghitung ulang status C4.5 secara instan.                        -->
<!-- ================================================================================ -->
<div id="studentEditModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4 transition-opacity">
    <div class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl border border-slate-200 transform transition-transform duration-200 scale-95" id="editModalCard">
        
        <!-- Header Modal Edit -->
        <div class="p-5 border-b border-slate-100 bg-gradient-to-r from-blue-50 to-slate-50 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-100 text-[#004b87] flex items-center justify-center font-bold text-sm">
                    ✏️
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-900">Perbarui Parameter Mahasiswa</h3>
                    <p class="text-[11px] text-slate-500">Status C4.5 akan otomatis dikalkulasi ulang</p>
                </div>
            </div>
            <button onclick="closeEditModal()" class="p-2 rounded-xl text-slate-400 hover:text-[#004b87] hover:bg-white transition border border-transparent hover:border-slate-200">
                ✕
            </button>
        </div>

        <!-- Form Modal Edit -->
        <form id="editStudentForm" onsubmit="handleEditSubmit(event)" class="p-6 space-y-4">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1">NIM Mahasiswa</label>
                    <input type="text" id="editNim" readonly class="w-full text-xs font-mono bg-slate-100 border border-slate-200 rounded-xl px-3.5 py-2 text-slate-500 cursor-not-allowed font-bold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1">Nama Mahasiswa <span class="text-rose-600">*</span></label>
                    <input type="text" id="editNama" required class="w-full text-xs bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-slate-900 font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1">Kehadiran (%) <span class="text-rose-600">*</span></label>
                    <input type="number" step="0.1" min="0" max="100" id="editKehadiran" required class="w-full text-xs font-mono bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white">
                    <span class="text-[9px] text-slate-400 block mt-0.5">Batas: > 72,50%</span>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1">Rata-rata IPK <span class="text-rose-600">*</span></label>
                    <input type="number" step="0.01" min="0" max="4.00" id="editIpk" required class="w-full text-xs font-mono bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white">
                    <span class="text-[9px] text-slate-400 block mt-0.5">Batas: > 2,995</span>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1">Total SKS <span class="text-rose-600">*</span></label>
                    <input type="number" min="0" max="160" id="editSks" required class="w-full text-xs font-mono bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white">
                    <span class="text-[9px] text-slate-400 block mt-0.5">SKS Lulus</span>
                </div>
            </div>

            <!-- Informasi Pembaruan Prediksi C4.5 -->
            <div class="p-3.5 rounded-2xl bg-blue-50/70 border border-blue-200 text-xs text-slate-700 flex items-center gap-2.5">
                <span class="text-base">💡</span>
                <span class="text-[11px] leading-relaxed">Status kelulusan dan aturan pohon keputusan C4.5 akan otomatis dikalkulasi ulang dan diperbarui saat Anda menekan tombol <b>Simpan Perubahan</b>.</span>
            </div>

            <!-- Tombol Aksi Simpan / Batal -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-[#004b87] hover:bg-[#003b6b] text-white font-extrabold text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                    <span>💾</span> Simpan Perubahan
                </button>
            </div>

        </form>

    </div>
</div>

<!-- Global JavaScript Application Controller -->
<script>
    // Konfigurasi Path Root untuk akses API
    window.APP_ROOT = '<?= $root_path; ?>';
</script>
<script src="<?= $root_path; ?>assets/js/app.js"></script>

</body>
</html>
