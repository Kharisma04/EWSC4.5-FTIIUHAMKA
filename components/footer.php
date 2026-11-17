<?php
/**
 * FILE       : components/footer.php
 * FUNGSI     : Footer, Modal Detail, Modal Edit, Modal Upload CSV/Excel, Script JS.
 */

if (!isset($root_path)) { $root_path = './'; }
$current_active_algo = function_exists('get_active_algorithm') ? get_active_algorithm() : 'c45';
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
     role="dialog" aria-modal="true" aria-labelledby="infoStudentName">
    <div id="infoModalCard" class="bg-white rounded-2xl max-w-md w-full overflow-hidden shadow-2xl border border-slate-200 transform transition-transform duration-200 scale-95">

        <!-- Header Modal -->
        <div id="infoModalHeader" class="p-5 border-b border-slate-100 bg-slate-50 flex items-start justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[10px] font-black uppercase tracking-widest text-[#004b87]">Status Studi</span>
                    <span id="infoStatusBadge" class="px-2.5 py-0.5 rounded-full font-bold text-[11px] border"></span>
                </div>
                <h3 id="infoStudentName" class="text-base font-black text-slate-900 leading-tight"></h3>
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

            <!-- Model & Aturan yang terpicu -->
            <div class="p-3 rounded-xl bg-blue-50 border border-blue-200">
                <div class="text-[10px] font-bold text-[#004b87] uppercase tracking-wider mb-1">Model &amp; Kriteria Terpicu</div>
                <div id="infoRuleTitle" class="text-xs font-bold text-slate-900"></div>
                <div id="infoCoverage" class="text-[11px] text-[#004b87] mt-0.5"></div>
            </div>

            <!-- Komparasi Kedua Algoritma (C4.5 vs Naive Bayes) -->
            <div id="infoCompareCard" class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center justify-between">
                    <span>Komparasi C4.5 vs Naive Bayes</span>
                    <span id="infoCompareMatchBadge" class="text-[9px] font-bold px-2 py-0.5 rounded-full"></span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="p-2 bg-white rounded-lg border border-slate-200">
                        <div class="text-[10px] text-slate-500 font-semibold">Decision Tree C4.5</div>
                        <div id="infoCompareC45Status" class="font-bold text-slate-800 text-[11px] mt-0.5"></div>
                        <div id="infoCompareC45Detail" class="text-[10px] text-slate-500 mt-0.5"></div>
                    </div>
                    <div class="p-2 bg-white rounded-lg border border-purple-100">
                        <div class="text-[10px] text-purple-700 font-semibold">Gaussian Naive Bayes</div>
                        <div id="infoCompareNbStatus" class="font-bold text-purple-900 text-[11px] mt-0.5"></div>
                        <div id="infoCompareNbDetail" class="text-[10px] text-purple-600 mt-0.5"></div>
                    </div>
                </div>
                <div id="infoCompareDifferenceNote" class="hidden mt-2 p-2 bg-amber-50 border border-amber-200 rounded-lg text-[11px] text-amber-900 leading-relaxed font-medium"></div>
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
    <div id="editModalCard" class="bg-white rounded-2xl max-w-md w-full overflow-hidden shadow-2xl border border-slate-200 transform transition-transform duration-200 scale-95">

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
                    <label for="editSks" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wide mb-1">SKS</label>
                    <input type="number" min="0" max="160" id="editSks"
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

<!-- =====================================================================
     MODAL: UPLOAD DATA MAHASISWA (CSV / EXCEL)
     ===================================================================== -->
<div id="uploadDataModal"
     class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center p-4"
     role="dialog" aria-modal="true" aria-labelledby="uploadModalTitle">
    <div id="uploadModalCard" class="bg-white rounded-2xl max-w-lg w-full overflow-hidden shadow-2xl border border-slate-200 transform transition-transform duration-200 scale-95">

        <!-- Header Modal Upload -->
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#004b87] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                </div>
                <div>
                    <h3 id="uploadModalTitle" class="text-sm font-black text-slate-900">Upload Data Mahasiswa</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Dukungan format CSV (.csv) dan Excel (.xlsx)</p>
                </div>
            </div>
            <button onclick="closeUploadModal()"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 transition"
                    aria-label="Tutup modal upload">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Form Upload -->
        <form id="uploadDataForm" onsubmit="handleUploadSubmit(event)" class="p-5 space-y-4">

            <!-- Area Dropzone Berkas -->
            <div>
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                    Pilih Berkas Data <span class="text-rose-500">*</span>
                </label>
                <div id="dropzoneArea" 
                     onclick="document.getElementById('fileUploadInput').click()"
                     class="border-2 border-dashed border-slate-300 hover:border-[#004b87] bg-slate-50 hover:bg-blue-50/50 rounded-2xl p-6 text-center cursor-pointer transition">
                    <input type="file" id="fileUploadInput" name="file_data" accept=".csv, .xlsx, .xls, text/csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required class="hidden" onchange="handleFileSelected(this)">
                    <div class="w-12 h-12 rounded-2xl bg-white shadow-xs border border-slate-200 text-[#004b87] flex items-center justify-center mx-auto mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div id="selectedFileName" class="text-xs font-bold text-slate-800">
                        Klik untuk memilih berkas atau seret berkas ke sini
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Format: <b>.csv</b> atau <b>.xlsx</b> (Maksimal 10 MB)</p>
                </div>
            </div>

            <!-- Opsi Penggabungan Data -->
            <div>
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                    Mode Penggabungan
                </label>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <label class="p-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 flex items-start gap-2 cursor-pointer">
                        <input type="radio" name="upload_mode" value="merge" checked class="mt-0.5 text-blue-600 focus:ring-blue-500">
                        <div>
                            <span class="font-bold text-slate-800 block">Gabungkan (Merge)</span>
                            <span class="text-[10px] text-slate-400 leading-tight block mt-0.5">Perbarui NIM yang cocok &amp; tambahkan data baru</span>
                        </div>
                    </label>
                    <label class="p-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 flex items-start gap-2 cursor-pointer">
                        <input type="radio" name="upload_mode" value="replace" class="mt-0.5 text-blue-600 focus:ring-blue-500">
                        <div>
                            <span class="font-bold text-slate-800 block">Gantikan Semua</span>
                            <span class="text-[10px] text-slate-400 leading-tight block mt-0.5">Ganti seluruh isi database saat ini</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Template Unduhan & Keterangan Format -->
            <div class="p-3 bg-blue-50/70 rounded-xl border border-blue-200/80 flex items-center justify-between text-xs">
                <div class="text-[11px] text-slate-600">
                    <span class="font-bold text-slate-800 block">Format Kolom:</span>
                    <span class="text-slate-500 font-mono text-[10px]">NIM, Nama, Prodi, Kehadiran, IPK, (SKS)</span>
                </div>
                <a href="<?= $root_path; ?>assets/template_mahasiswa.csv" download
                   class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-white border border-blue-300 text-[#004b87] font-bold text-[11px] hover:bg-blue-100/50 transition shrink-0 shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Unduh Contoh CSV</span>
                </a>
            </div>

            <!-- Pesan Status Upload -->
            <div id="uploadStatusMsg" class="hidden text-xs p-3 rounded-xl"></div>

            <!-- Tombol Aksi -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeUploadModal()"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition">
                    Batal
                </button>
                <button type="submit" id="btnSubmitUpload"
                        class="px-4 py-2 bg-[#004b87] hover:bg-[#003b6b] text-white font-bold text-xs rounded-lg shadow transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span>Impor Data Sekarang</span>
                </button>
            </div>

        </form>
    </div>
</div>

<!-- Scripts Global -->
<script>
    window.APP_ROOT = '<?= $root_path; ?>';
    window.ACTIVE_ALGORITHM = '<?= $current_active_algo; ?>';
</script>
<script src="<?= $root_path; ?>assets/js/app.js"></script>

</body>
</html>
