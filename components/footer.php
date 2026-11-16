<?php
/**
 * FILE       : components/footer.php
 * FUNGSI     : Footer, Modal Detail, Modal Edit, Script JS.
 */

if (!isset($root_path)) { $root_path = './'; }
?>
    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-auto text-xs text-slate-400 py-3 px-6 no-print" role="contentinfo">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-1">
            <span>© <?= APP_YEAR; ?> <b class="text-slate-600"><?= FAKULTAS_NAME; ?> UHAMKA</b> — <?= PRODI_NAME; ?></span>
            <span><?= RESEARCHER_NAME; ?> (<?= RESEARCHER_NIM; ?>)</span>
        </div>
    </footer>

</div><!-- /content wrapper -->

<!-- =====================================================================
     MODAL: INFO DETAIL MAHASISWA
     ===================================================================== -->
<div id="studentInfoModal"
     class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center p-4"
     role="dialog" aria-modal="true" aria-labelledby="infoModalTitle">
    <div class="bg-white rounded-2xl max-w-md w-full overflow-hidden shadow-2xl border border-slate-200">

        <!-- Header Modal -->
        <div id="infoModalHeader" class="p-5 border-b border-slate-100 bg-slate-50 flex items-start justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[10px] font-black uppercase tracking-widest text-[#004b87]">Status Studi</span>
                    <span id="infoStatusBadge" class="px-2.5 py-0.5 rounded-full font-bold text-[11px] border"></span>
                </div>
                <h3 id="infoModalTitle" class="text-base font-black text-slate-900 leading-tight"></h3>
                <div id="infoStudentNim" class="text-xs text-slate-400 mt-0.5"></div>
            </div>
            <button onclick="closeInfoModal()"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 transition shrink-0"
                    aria-label="Tutup modal">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Body Modal -->
        <div class="p-5 space-y-4 max-h-[70vh] overflow-y-auto">

            <!-- Statistik -->
            <div class="grid grid-cols-3 gap-2 text-center ">
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-0.5">Kehadiran</div>
                    <div id="infoKehadiranVal" class="text-sm font-black text-slate-800"></div>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-0.5">IPK</div>
                    <div id="infoIpkVal" class="text-sm font-black text-slate-800"></div>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-0.5">SKS</div>
                    <div id="infoSksVal" class="text-sm font-black text-slate-800"></div>
                </div>
            </div>

            <!-- Aturan yang terpicu -->
            <div class="p-3 rounded-xl bg-blue-50 border border-blue-200">
                <div class="text-[10px] font-bold text-[#004b87] uppercase tracking-wider mb-1">Aturan Keputusan Terpicu</div>
                <div id="infoRuleTitle" class="text-xs font-bold text-slate-900"></div>
                <div id="infoCoverage" class="text-[11px] text-[#004b87] mt-0.5"></div>
            </div>

            <!-- Analisis -->
            <div>
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Analisis Kondisi</div>
                <p id="infoReason" class="text-xs text-slate-600 bg-white p-3 rounded-xl border border-slate-200 leading-relaxed"></p>
            </div>

            <!-- Rekomendasi -->
            <div>
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Rekomendasi Dosen PA</div>
                <p id="infoRecommendation" class="text-xs text-slate-700 bg-blue-50 p-3 rounded-xl border border-blue-200 leading-relaxed font-medium"></p>
            </div>
        </div>

        <!-- Footer Modal -->
        <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button onclick="closeInfoModal()"
                    class="px-4 py-2 bg-[#004b87] hover:bg-[#003b6b] text-white font-bold text-xs rounded-lg transition">
                Tutup
            </button>
        </div>

    </div>
</div>

<!-- =====================================================================
     MODAL: EDIT DATA MAHASISWA
     ===================================================================== -->
<div id="studentEditModal"
     class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center p-4"
     role="dialog" aria-modal="true" aria-labelledby="editModalTitle">
    <div class="bg-white rounded-2xl max-w-md w-full overflow-hidden shadow-2xl border border-slate-200">

        <!-- Header Modal Edit -->
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <div>
                <h3 id="editModalTitle" class="text-sm font-black text-slate-900">Edit Data Mahasiswa</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Status prediksi akan dihitung ulang otomatis</p>
            </div>
            <button onclick="closeEditModal()"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 transition"
                    aria-label="Tutup modal edit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Form Edit -->
        <form id="editStudentForm" onsubmit="handleEditSubmit(event)" class="p-5 space-y-4">

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="editNim" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wide mb-1">NIM</label>
                    <input type="text" id="editNim" readonly
                           class="w-full text-xs bg-slate-100 border border-slate-200 rounded-lg px-3 py-2 text-slate-400 cursor-not-allowed">
                </div>
                <div>
                    <label for="editNama" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wide mb-1">Nama <span class="text-rose-500">*</span></label>
                    <input type="text" id="editNama" required
                           class="w-full text-xs bg-white border border-slate-300 rounded-lg px-3 py-2 text-slate-900 font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label for="editKehadiran" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wide mb-1">Kehadiran % <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.1" min="0" max="100" id="editKehadiran" required
                           class="w-full text-xs bg-white border border-slate-300 rounded-lg px-3 py-2 text-slate-900 font-bold focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <span class="text-[9px] text-slate-400 mt-0.5 block">Aman: &gt; 72,50%</span>
                </div>
                <div>
                    <label for="editIpk" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wide mb-1">IPK <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" min="0" max="4.00" id="editIpk" required
                           class="w-full text-xs bg-white border border-slate-300 rounded-lg px-3 py-2 text-slate-900 font-bold focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <span class="text-[9px] text-slate-400 mt-0.5 block">Aman: &gt; 2,995</span>
                </div>
                <div>
                    <label for="editSks" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wide mb-1">SKS <span class="text-rose-500">*</span></label>
                    <input type="number" min="0" max="160" id="editSks" required
                           class="w-full text-xs bg-white border border-slate-300 rounded-lg px-3 py-2 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-[#004b87] hover:bg-[#003b6b] text-white font-bold text-xs rounded-lg shadow transition">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</div>

<!-- Scripts -->
<script>window.APP_ROOT = '<?= $root_path; ?>';</script>
<script src="<?= $root_path; ?>assets/js/app.js"></script>

</body>
</html>
