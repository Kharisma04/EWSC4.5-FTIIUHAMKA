/**
 * ====================================================================================
 * Early Warning System (EWS) — FTII UHAMKA
 * FILE : assets/js/app.js
 * ====================================================================================
 */

// Global State
let allStudents = [];
let filteredStudents = [];
let currentPage = 1;
const pageSize = 15; // Jumlah data per halaman agar responsif dan cepat
let riskChartInstance = null;

// Ambang Batas Klasifikasi (Berdasarkan Skripsi Bab 4)
const C45_THRESHOLD_KEHADIRAN = 72.50;
const C45_THRESHOLD_IPK = 2.995;

/**
 * Logika Klasifikasi model prediksi Client-Side untuk Real-Time Preview
 */
function classifyC45Client(kehadiran, ipk) {
  kehadiran = parseFloat(kehadiran);
  ipk = parseFloat(ipk);

  // Aturan 1: Simpul Akar Kehadiran <= 72.50%
  if (kehadiran <= C45_THRESHOLD_KEHADIRAN) {
    return {
      status: 'Berisiko Terhambat',
      ruleNum: 1,
      ruleTitle: 'Aturan 1 (Root Node): Kehadiran ≤ 72,50%',
      coverage: 'Indikator Utama: Kedisiplinan Presensi Kuliah',
      reason: `Persentase kehadiran mahasiswa sebesar ${kehadiran.toFixed(1)}% (di bawah batas aman 72,50%). Dalam model keputusan model prediksi, kehadiran merupakan indikator penentu utama kedisiplinan belajar.`,
      recommendation: 'Panggilan konseling wajib oleh Dosen Pembimbing Akademik (DPA), pengecekan syarat minimal ujian 75%, dan identifikasi kendala kehadiran mahasiswa.',
      badgeClass: 'bg-rose-100 text-rose-800 border-rose-300'
    };
  }
  // Aturan 2: Simpul Internal IPK <= 2.995
  else if (ipk <= C45_THRESHOLD_IPK) {
    return {
      status: 'Berisiko Terhambat',
      ruleNum: 2,
      ruleTitle: 'Aturan 2 (Internal Node): Kehadiran > 72,50% & IPK ≤ 2,995',
      coverage: 'Indikator Akademik: Capaian Nilai IPK',
      reason: `Mahasiswa disiplin hadir (${kehadiran.toFixed(1)}%), namun capaian IPK (${ipk.toFixed(2)}) belum mencapai batas aman 2,995 untuk kelulusan tepat waktu.`,
      recommendation: 'Bimbingan belajar remedial, pendampingan tutor sebaya pada mata kuliah berbobot besar/prasyarat, dan peninjauan beban SKS semester berikutnya.',
      badgeClass: 'bg-amber-100 text-amber-900 border-amber-300'
    };
  }
  // Aturan 3: Jalur Aman
  else {
    return {
      status: 'Berpotensi Tepat Waktu',
      ruleNum: 3,
      ruleTitle: 'Aturan 3 (Kondisi Aman): Kehadiran > 72,50% & IPK > 2,995',
      coverage: 'Jalur Hijau: Memenuhi Syarat Studi Ideal',
      reason: `Kedisiplinan presensi (${kehadiran.toFixed(1)}%) dan capaian IPK (${ipk.toFixed(2)}) memenuhi standar aman kelulusan tepat waktu 4 tahun.`,
      recommendation: 'Pertahankan performa studi, arahkan penulisan topik proposal skripsi lebih awal, dan fasilitasi keikutsertaan program magang/MBKM.',
      badgeClass: 'bg-emerald-100 text-emerald-800 border-emerald-300'
    };
  }
}

/**
 * Probability Density Function Gaussian untuk Naive Bayes
 */
function nbGaussianPdf(x, mean, variance) {
  if (variance <= 0.000001) variance = 0.0001;
  const pi = 3.141592653589793;
  const exponent = -Math.pow(x - mean, 2) / (2 * variance);
  return (1 / Math.sqrt(2 * pi * variance)) * Math.exp(exponent);
}

/**
 * Logika Klasifikasi Gaussian Naive Bayes Client-Side
 */
function classifyNaiveBayesClient(kehadiran, ipk) {
  kehadiran = parseFloat(kehadiran);
  ipk = parseFloat(ipk);

  const priorTepat = 0.5515;
  const priorBerisiko = 0.4485;

  const likeHadirTepat = nbGaussianPdf(kehadiran, 86.5145, 75.9258);
  const likeHadirBerisiko = nbGaussianPdf(kehadiran, 58.9388, 203.4116);

  const likeIpkTepat = nbGaussianPdf(ipk, 3.3672, 0.0294);
  const likeIpkBerisiko = nbGaussianPdf(ipk, 3.2623, 0.0788);

  const postTepatRaw = priorTepat * likeHadirTepat * likeIpkTepat;
  const postBerisikoRaw = priorBerisiko * likeHadirBerisiko * likeIpkBerisiko;

  const sum = postTepatRaw + postBerisikoRaw;
  let pctTepat = 50.0;
  let pctBerisiko = 50.0;
  if (sum > 0) {
    pctTepat = Math.round((postTepatRaw / sum) * 1000) / 10;
    pctBerisiko = Math.round((postBerisikoRaw / sum) * 1000) / 10;
  }

  const isAtRisk = (pctBerisiko >= pctTepat);
  return {
    status: isAtRisk ? 'Berisiko Terhambat' : 'Berpotensi Tepat Waktu',
    ruleNum: isAtRisk ? 2 : 1,
    ruleTitle: `Peluang: ${pctTepat}% Tepat Waktu vs ${pctBerisiko}% Berisiko`,
    coverage: `Gaussian Bayes: Kehadiran (μ=${isAtRisk ? '58.9%' : '86.5%'}), IPK (μ=${isAtRisk ? '3.26' : '3.37'})`,
    pctTepat: pctTepat,
    pctBerisiko: pctBerisiko,
    isAtRisk: isAtRisk,
    reason: isAtRisk 
      ? `Berdasarkan teorema Naive Bayes, mahasiswa ini memiliki probabilitas risiko kelulusan terhambat sebesar ${pctBerisiko}%. Pola kehadiran (${kehadiran.toFixed(1)}%) dan IPK (${ipk.toFixed(2)}) condong mendekati karakteristik populasi mahasiswa terhambat.`
      : `Berdasarkan teorema Naive Bayes, probabilitas kelulusan tepat waktu mencapai ${pctTepat}%. Nilai presensi (${kehadiran.toFixed(1)}%) dan IPK (${ipk.toFixed(2)}) sangat sesuai dengan sebaran populasi mahasiswa sukses tepat waktu di FTII UHAMKA.`,
    recommendation: isAtRisk
      ? 'Prioritaskan bimbingan intensif DPA, monitor presensi berkala setiap 2 minggu, dan berikan pendampingan akademik pada mata kuliah sulit.'
      : 'Pertahankan performa studi yang baik, dorong penyusunan skripsi lebih awal, dan fasilitasi program magang industri MBKM.',
    badgeClass: isAtRisk ? 'bg-rose-100 text-rose-800 border-rose-300' : 'bg-emerald-100 text-emerald-800 border-emerald-300'
  };
}

/**
 * Helper terpadu untuk klasifikasi client-side berdasarkan algoritma aktif
 */
function classifyClient(kehadiran, ipk, algo) {
  const selectedAlgo = algo || window.ACTIVE_ALGORITHM || 'c45';
  if (selectedAlgo === 'naive_bayes') {
    return classifyNaiveBayesClient(kehadiran, ipk);
  }
  return classifyC45Client(kehadiran, ipk);
}

/**
 * Toggle Navigasi Sidebar Mobile
 */
function toggleSidebar() {
  const sidebar = document.getElementById('appSidebar');
  const backdrop = document.getElementById('sidebarBackdrop');
  if (sidebar && backdrop) {
    const isClosed = sidebar.classList.contains('-translate-x-full');
    if (isClosed) {
      sidebar.classList.remove('-translate-x-full');
      backdrop.classList.remove('hidden');
    } else {
      sidebar.classList.add('-translate-x-full');
      backdrop.classList.add('hidden');
    }
  }
}

/**
 * Render Visualisasi Donut Chart Distribusi Mahasiswa
 */
function initRiskChart(tepatCount, hambatCount) {
  const canvas = document.getElementById('riskChart');
  if (!canvas) return;

  if (riskChartInstance) {
    riskChartInstance.destroy();
  }

  riskChartInstance = new Chart(canvas, {
    type: 'doughnut',
    data: {
      labels: ['Berpotensi Tepat Waktu', 'Berisiko Terhambat'],
      datasets: [{
        data: [tepatCount, hambatCount],
        backgroundColor: ['#10b981', '#f43f5e'],
        borderWidth: 3,
        borderColor: '#ffffff',
        hoverOffset: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            boxWidth: 10,
            usePointStyle: true,
            font: { family: 'Plus Jakarta Sans', size: 12, weight: '700' }
          }
        },
        tooltip: {
          callbacks: {
            label: function(ctx) {
              const val = ctx.raw;
              const total = tepatCount + hambatCount;
              const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
              return ` ${ctx.label}: ${val} mhs (${pct}%)`;
            }
          }
        }
      },
      cutout: '68%'
    }
  });
}

/**
 * Menginisialisasi Pangkalan Data Mahasiswa pada Tabel Dashboard
 */
function initStudentTable(data) {
  allStudents = data;
  filteredStudents = [...allStudents];
  currentPage = 1;
  renderTableRows();
}

/**
 * Memfilter dan Merender Baris Tabel dengan Pagination
 */
function renderTableRows() {
  const tbody = document.getElementById('studentTableBody');
  if (!tbody) return;

  const total = filteredStudents.length;
  const startIdx = (currentPage - 1) * pageSize;
  const endIdx = Math.min(startIdx + pageSize, total);
  const pageItems = filteredStudents.slice(startIdx, endIdx);

  tbody.innerHTML = '';

  if (pageItems.length === 0) {
    const colCount = window.IS_ADMIN ? 8 : 7;
    tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center py-10 text-slate-400 font-medium">Tidak ada data mahasiswa yang cocok dengan kriteria pencarian.</td></tr>`;
    renderPagination(0);
    return;
  }

  pageItems.forEach((std, i) => {
    const isHambat = (std.status.indexOf('Berisiko') !== -1 || std.status.indexOf('Hambat') !== -1);
    const rowIdx = startIdx + i + 1;
    const stdJson = JSON.stringify(std).replace(/"/g, '&quot;');
    const activeAlgo = window.ACTIVE_ALGORITHM || 'c45';
    const isAdmin = window.IS_ADMIN === true;

    // Sub-teks status — hanya untuk admin
    let statusSubText = '';
    if (isAdmin) {
      if (activeAlgo === 'naive_bayes') {
        const probVal = isHambat ? (std.prob_berisiko || 50.0) : (std.prob_tepat || 50.0);
        statusSubText = `<span class="text-[9px] font-semibold opacity-90 block">(${probVal}% peluang)</span>`;
      } else {
        statusSubText = `<span class="text-[9px] font-semibold opacity-90 block">(Aturan ${std.rule || (isHambat ? '1/2' : '3')})</span>`;
      }
    }

    // Badge Beda Model — hanya untuk admin
    const isModelMismatch = isAdmin && (std.c45_status && std.nb_status && std.c45_status !== std.nb_status);
    const diffBadge = isModelMismatch
      ? `<span class="mt-1 inline-block text-[9px] px-1.5 py-0.5 rounded bg-purple-100 text-purple-700 font-bold border border-purple-200" title="Hasil C4.5 & Naive Bayes berbeda">Beda Model</span>`
      : '';

    // Kolom SKS — hanya untuk admin
    const sksCell = isAdmin
      ? `<td class="py-3 px-3 text-center text-slate-600">${std.sks}</td>`
      : '';

    // Kolom Aksi — edit/hapus untuk admin, hanya info untuk user
    const aksiCell = isAdmin
      ? `<td class="py-3 px-3 text-center">
          <div class="inline-flex items-center gap-1">
            <button onclick="openEditModalByNim('${std.nim}')"
                    type="button"
                    aria-label="Edit ${std.nama}"
                    class="px-2.5 py-1 text-[11px] font-bold text-slate-600 hover:text-[#004b87] hover:bg-blue-50 rounded-lg border border-slate-200 transition">
              Edit
            </button>
            <button onclick="deleteStudent('${std.nim}')"
                    type="button"
                    aria-label="Hapus ${std.nama}"
                    class="px-2.5 py-1 text-[11px] font-bold text-rose-600 hover:bg-rose-50 rounded-lg border border-rose-200 transition">
              Hapus
            </button>
          </div>
        </td>`
      : `<td class="py-3 px-3 text-center">
          <button onclick="openInfoModalByNim('${std.nim}')"
                  type="button"
                  aria-label="Lihat detail ${std.nama}"
                  class="px-2.5 py-1 text-[11px] font-bold text-[#004b87] hover:bg-blue-50 rounded-lg border border-blue-200 transition">
            Detail
          </button>
        </td>`;

    const tr = document.createElement('tr');
    tr.className = 'hover:bg-slate-50 transition-colors text-xs';
    tr.innerHTML = `
      <td class="py-3 px-3 text-[11px] text-slate-400 text-center">${rowIdx}</td>
      <td class="py-3 px-3 font-bold text-slate-700">${std.nim}</td>
      <td class="py-3 px-3">
        <div class="flex items-center gap-1.5">
          <span class="font-semibold text-slate-800">${std.nama}</span>
          <button onclick="openInfoModalByNim('${std.nim}')"
                  type="button"
                  aria-label="Lihat detail ${std.nama}"
                  class="w-5 h-5 rounded-full bg-blue-50 hover:bg-blue-100 text-[#004b87] flex items-center justify-center text-[10px] border border-blue-200 transition font-black shrink-0">
            ℹ
          </button>
        </div>
      </td>
      <td class="py-3 px-3 text-center">
        <span class="font-bold text-xs ${std.kehadiran <= 72.5 ? 'text-rose-700 bg-rose-50 px-2 py-0.5 rounded-lg border border-rose-200' : 'text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-200'}">${parseFloat(std.kehadiran).toFixed(1)}%</span>
      </td>
      <td class="py-3 px-3 text-center">
        <span class="font-bold text-xs ${std.ipk <= 2.995 ? 'text-amber-700 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-200' : 'text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-200'}">${parseFloat(std.ipk).toFixed(2)}</span>
      </td>
      ${sksCell}
      <td class="py-3 px-3 text-center">
        <span class="px-2.5 py-1 text-[11px] rounded-full font-bold border inline-block ${isHambat ? 'bg-rose-100 text-rose-800 border-rose-200' : 'bg-emerald-100 text-emerald-800 border-emerald-200'}">
          <span>${std.status}</span>
          ${statusSubText}
        </span>
        ${diffBadge}
      </td>
      ${aksiCell}
    `;
    tbody.appendChild(tr);
  });

  renderPagination(total);
}

/**
 * Komponen Navigasi Halaman (Pagination)
 */
function renderPagination(total) {
  const container = document.getElementById('tablePagination');
  if (!container) return;

  const totalPages = Math.ceil(total / pageSize);
  if (totalPages <= 1) {
    container.innerHTML = `<span class="text-xs text-slate-400">Menampilkan seluruh <b>${total}</b> mahasiswa</span>`;
    return;
  }

  container.innerHTML = `
    <div class="flex items-center justify-between w-full text-xs text-slate-500">
      <div>
        Halaman <b>${currentPage}</b> dari <b>${totalPages}</b> (Total: <b>${total}</b> mahasiswa)
      </div>
      <div class="inline-flex items-center gap-1.5">
        <button onclick="goToPage(${currentPage - 1})" ${currentPage <= 1 ? 'disabled class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-300 cursor-not-allowed"' : 'class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold transition"'}>
          ← Sebelumnya
        </button>
        <button onclick="goToPage(${currentPage + 1})" ${currentPage >= totalPages ? 'disabled class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-300 cursor-not-allowed"' : 'class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold transition"'}>
          Selanjutnya →
        </button>
      </div>
    </div>
  `;
}

function goToPage(page) {
  const totalPages = Math.ceil(filteredStudents.length / pageSize);
  if (page < 1 || page > totalPages) return;
  currentPage = page;
  renderTableRows();
}

/**
 * Filter dan Pencarian Tabel
 */
function filterTable(statusFilter) {
  const q = (document.getElementById('tableSearch')?.value || '').toLowerCase().trim();

  filteredStudents = allStudents.filter(s => {
    let matchStatus = true;
    if (statusFilter === 'hambat') {
      matchStatus = (s.status.indexOf('Berisiko') !== -1 || s.status.indexOf('Hambat') !== -1);
    }
    if (statusFilter === 'tepat') {
      matchStatus = (s.status.indexOf('Tepat') !== -1);
    }

    let matchSearch = true;
    if (q) {
      matchSearch = s.nama.toLowerCase().includes(q) || s.nim.toLowerCase().includes(q);
    }

    return matchStatus && matchSearch;
  });

  currentPage = 1;
  renderTableRows();
}

/**
 * ====================================================================================
 * MODAL 1: BUKA & TUTUP INFO DETAIL DIAGNOSIS MAHASISWA (ℹ️)
 * ====================================================================================
 */
function openInfoModalByNim(nim) {
  const student = allStudents.find(s => String(s.nim) === String(nim));
  if (student) {
    openInfoModal(student);
  }
}

function openInfoModal(student) {
  const modal = document.getElementById('studentInfoModal');
  const card = document.getElementById('infoModalCard');
  if (!modal) return;

  const activeAlgo = window.ACTIVE_ALGORITHM || 'c45';
  const c45Pred = classifyC45Client(student.kehadiran, student.ipk);
  const nbPred  = classifyNaiveBayesClient(student.kehadiran, student.ipk);
  const pred    = (activeAlgo === 'naive_bayes') ? nbPred : c45Pred;

  const nameEl = document.getElementById('infoStudentName') || document.getElementById('infoModalTitle');
  if (nameEl) nameEl.innerText = student.nama;

  const nimEl = document.getElementById('infoStudentNim');
  if (nimEl) nimEl.innerText = `NIM: ${student.nim} · ${student.prodi || 'Teknik Informatika'}`;
  
  const badge = document.getElementById('infoStatusBadge');
  if (badge) {
    badge.className = `px-2.5 py-0.5 rounded-full font-bold text-[11px] border ${pred.badgeClass}`;
    badge.innerText = pred.status;
  }

  const hEl = document.getElementById('infoKehadiranVal');
  if (hEl) hEl.innerText = `${parseFloat(student.kehadiran).toFixed(1)}%`;

  const ipkEl = document.getElementById('infoIpkVal');
  if (ipkEl) ipkEl.innerText = parseFloat(student.ipk).toFixed(2);

  // SKS hanya ada di modal admin
  const sksEl = document.getElementById('infoSksVal');
  if (sksEl) sksEl.innerText = `${student.sks} SKS`;

  const ruleEl = document.getElementById('infoRuleTitle');
  if (ruleEl) ruleEl.innerText = pred.ruleTitle;

  const covEl = document.getElementById('infoCoverage');
  if (covEl) covEl.innerText = pred.coverage;

  const reasonEl = document.getElementById('infoReason');
  if (reasonEl) reasonEl.innerText = pred.reason;

  const recEl = document.getElementById('infoRecommendation');
  if (recEl) recEl.innerText = pred.recommendation;

  // Komparasi C4.5 vs Naive Bayes di dalam Modal
  const compMatchBadge = document.getElementById('infoCompareMatchBadge');
  const compC45Status  = document.getElementById('infoCompareC45Status');
  const compC45Detail  = document.getElementById('infoCompareC45Detail');
  const compNbStatus   = document.getElementById('infoCompareNbStatus');
  const compNbDetail   = document.getElementById('infoCompareNbDetail');
  const compDiffNote   = document.getElementById('infoCompareDifferenceNote');

  if (compC45Status) {
    compC45Status.innerHTML = `<span class="px-1.5 py-0.5 rounded text-[10px] font-bold inline-block border ${c45Pred.badgeClass}">${c45Pred.status}</span>`;
    compC45Detail.innerText = `Aturan ${c45Pred.ruleNum}`;
  }
  if (compNbStatus) {
    compNbStatus.innerHTML = `<span class="px-1.5 py-0.5 rounded text-[10px] font-bold inline-block border ${nbPred.badgeClass}">${nbPred.status}</span>`;
    compNbDetail.innerText = `Peluang Tepat: ${nbPred.pctTepat}%`;
  }

  const isMatch = (c45Pred.status === nbPred.status);
  if (compMatchBadge) {
    if (isMatch) {
      compMatchBadge.className = 'text-[9px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200';
      compMatchBadge.innerText = 'Hasil Sepakat';
      if (compDiffNote) compDiffNote.classList.add('hidden');
    } else {
      compMatchBadge.className = 'text-[9px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-300';
      compMatchBadge.innerText = 'Hasil Berbeda';
      if (compDiffNote) {
        compDiffNote.classList.remove('hidden');
        compDiffNote.innerHTML = `⚠️ <b>Perbedaan Klasifikasi:</b> Decision Tree C4.5 memprediksi <b>${c45Pred.status}</b> (Aturan ${c45Pred.ruleNum}), sedangkan Gaussian Naive Bayes memprediksi <b>${nbPred.status}</b> (${nbPred.isAtRisk ? nbPred.pctBerisiko + '% risiko' : nbPred.pctTepat + '% tepat'}). Hal ini wajar karena C4.5 menerapkan batas tegas IPK 2,995 dan Presensi 72,5%, sedangkan Naive Bayes memperhitungkan sebaran probabilitas normal Gaussian.`;
      }
    }
  }

  modal.classList.remove('hidden');
  modal.classList.add('flex');
  if (card) {
    card.classList.remove('scale-95');
    card.classList.add('scale-100');
  }
}

function closeInfoModal() {
  const modal = document.getElementById('studentInfoModal');
  const card = document.getElementById('infoModalCard');
  if (!modal) return;

  if (card) {
    card.classList.remove('scale-100');
    card.classList.add('scale-95');
  }
  setTimeout(() => {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }, 150);
}

/**
 * ====================================================================================
 * MODAL 2: BUKA & TUTUP FORM EDIT DATA MAHASISWA (✏️)
 * ====================================================================================
 */
function openEditModalByNim(nim) {
  const student = allStudents.find(s => String(s.nim) === String(nim));
  if (student) {
    openEditModal(student);
  }
}

function openEditModal(student) {
  const modal = document.getElementById('studentEditModal');
  const card = document.getElementById('editModalCard');
  if (!modal) return;

  const nimEl = document.getElementById('editNim');
  if (nimEl) nimEl.value = student.nim;

  const namaEl = document.getElementById('editNama');
  if (namaEl) namaEl.value = student.nama;

  const hEl = document.getElementById('editKehadiran');
  if (hEl) hEl.value = student.kehadiran;

  const ipkEl = document.getElementById('editIpk');
  if (ipkEl) ipkEl.value = student.ipk;

  const sksEl = document.getElementById('editSks');
  if (sksEl) sksEl.value = student.sks;

  modal.classList.remove('hidden');
  modal.classList.add('flex');
  if (card) {
    card.classList.remove('scale-95');
    card.classList.add('scale-100');
  }
}

function closeEditModal() {
  const modal = document.getElementById('studentEditModal');
  const card = document.getElementById('editModalCard');
  if (!modal) return;

  if (card) {
    card.classList.remove('scale-100');
    card.classList.add('scale-95');
  }
  setTimeout(() => {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }, 150);
}

/**
 * Submit Pembaruan Data Mahasiswa via API
 */
async function handleEditSubmit(e) {
  e.preventDefault();
  const nim = document.getElementById('editNim').value;
  const nama = document.getElementById('editNama').value;
  const kehadiran = document.getElementById('editKehadiran').value;
  const ipk = document.getElementById('editIpk').value;
  const sks = document.getElementById('editSks').value;

  const apiUrl = (window.APP_ROOT || './') + 'app/api_student.php?action=update';

  const formData = new FormData();
  formData.append('nim', nim);
  formData.append('nama', nama);
  formData.append('kehadiran', kehadiran);
  formData.append('ipk', ipk);
  formData.append('sks', sks);

  try {
    const res = await fetch(apiUrl, { method: 'POST', body: formData });
    const json = await res.json();
    if (json.success) {
      alert(`✅ Data mahasiswa ${nama} (${nim}) berhasil diperbarui dan status model prediksi telah dihitung ulang!`);
      closeEditModal();
      window.location.reload();
    } else {
      alert('Gagal memperbarui data: ' + (json.message || 'Terjadi kesalahan.'));
    }
  } catch (err) {
    console.error(err);
    alert('Terjadi kesalahan koneksi ke server.');
  }
}

/**
 * Hapus Mahasiswa via API
 */
async function deleteStudent(nim) {
  if (!confirm(`Hapus data mahasiswa dengan NIM ${nim} dari pangkalan data?`)) return;

  const apiUrl = (window.APP_ROOT || './') + 'app/api_student.php?action=delete';
  const formData = new FormData();
  formData.append('nim', nim);

  try {
    const res = await fetch(apiUrl, { method: 'POST', body: formData });
    const json = await res.json();
    if (json.success) {
      alert(`Data mahasiswa NIM ${nim} berhasil dihapus.`);
      window.location.reload();
    } else {
      alert('Gagal menghapus data.');
    }
  } catch (err) {
    console.error(err);
    alert('Terjadi kesalahan jaringan.');
  }
}

/**
 * Reset Pangkalan Data ke Kondisi Awal File Skripsi
 */
async function resetStudents() {
  if (!confirm('Kembalikan pangkalan data ke 438 data awal dari file skripsi Excel?\n\nSemua perubahan dan data simulasi tambahan akan direset.')) return;

  const apiUrl = (window.APP_ROOT || './') + 'app/api_student.php?action=reset';
  try {
    const res = await fetch(apiUrl);
    const json = await res.json();
    if (json.success) {
      alert('✅ Pangkalan data berhasil dikembalikan ke dataset awal file skripsi!');
      window.location.reload();
    } else {
      alert('Gagal mereset data.');
    }
  } catch (err) {
    console.error(err);
    alert('Terjadi kesalahan koneksi.');
  }
}

/**
 * ====================================================================================
 * MODAL 3: UPLOAD DATA MAHASISWA (CSV / EXCEL)
 * ====================================================================================
 */
function openUploadModal() {
  const modal = document.getElementById('uploadDataModal');
  const card = document.getElementById('uploadModalCard');
  if (!modal) return;

  modal.classList.remove('hidden');
  modal.classList.add('flex');
  setTimeout(() => {
    card?.classList.remove('scale-95');
    card?.classList.add('scale-100');
  }, 10);

  const msg = document.getElementById('uploadStatusMsg');
  if (msg) {
    msg.className = 'hidden text-xs p-3 rounded-xl';
    msg.innerHTML = '';
  }
}

function closeUploadModal() {
  const modal = document.getElementById('uploadDataModal');
  const card = document.getElementById('uploadModalCard');
  if (!modal) return;

  card?.classList.remove('scale-100');
  card?.classList.add('scale-95');
  setTimeout(() => {
    modal.classList.remove('flex');
    modal.classList.add('hidden');
  }, 150);
}

function handleFileSelected(input) {
  const nameDisplay = document.getElementById('selectedFileName');
  if (input.files && input.files[0]) {
    const file = input.files[0];
    const sizeKB = Math.round(file.size / 1024);
    if (nameDisplay) {
      nameDisplay.innerHTML = `<span class="text-[#004b87] font-bold">${file.name}</span> <span class="text-slate-400 font-normal">(${sizeKB} KB)</span>`;
    }
  }
}

async function handleUploadSubmit(e) {
  e.preventDefault();
  const fileInput = document.getElementById('fileUploadInput');
  if (!fileInput || !fileInput.files || !fileInput.files[0]) {
    alert('Harap pilih berkas CSV atau Excel terlebih dahulu.');
    return;
  }

  const btn = document.getElementById('btnSubmitUpload');
  const origBtnText = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = `<svg class="w-4 h-4 animate-spin inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Mengimpor Data...`;

  const mode = document.querySelector('input[name="upload_mode"]:checked')?.value || 'merge';
  const fd = new FormData();
  fd.append('file_data', fileInput.files[0]);
  fd.append('mode', mode);

  const statusMsg = document.getElementById('uploadStatusMsg');

  try {
    const apiUrl = (window.APP_ROOT || './') + 'app/api_student.php?action=import';
    const response = await fetch(apiUrl, {
      method: 'POST',
      body: fd
    });
    const result = await response.json();

    if (result.success) {
      if (statusMsg) {
        statusMsg.className = 'text-xs p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold block';
        statusMsg.innerHTML = `✓ <b>${result.message}</b><br><span class="text-[11px] font-normal">Total baris diproses: ${result.total_imported} data (${result.inserted} data baru ditambahkan, ${result.updated} data diperbarui). Total di sistem: ${result.total_current} mahasiswa.</span>`;
      }
      setTimeout(() => {
        window.location.reload();
      }, 1200);
    } else {
      if (statusMsg) {
        statusMsg.className = 'text-xs p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 font-semibold block';
        statusMsg.innerHTML = `⚠ Gagal mengimpor: ${result.message}`;
      }
      btn.disabled = false;
      btn.innerHTML = origBtnText;
    }
  } catch (err) {
    if (statusMsg) {
      statusMsg.className = 'text-xs p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 font-semibold block';
      statusMsg.innerHTML = '⚠ Terjadi kesalahan koneksi saat mengunggah berkas.';
    }
    btn.disabled = false;
    btn.innerHTML = origBtnText;
  }
}

/**
 * Inisialisasi Event Listener saat DOM siap
 */
document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.getElementById('tableSearch');
  if (searchInput) {
    searchInput.addEventListener('input', () => {
      const activeFilter = document.querySelector('.filter-btn.active')?.dataset.filter || 'all';
      filterTable(activeFilter);
    });
  }

  document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.filter-btn').forEach(b => {
        b.classList.remove('active', 'bg-[#004b87]', 'bg-rose-600', 'text-white');
        b.classList.add('bg-white', 'text-slate-700');
      });
      btn.classList.add('active', 'bg-[#004b87]', 'text-white');
      btn.classList.remove('bg-white', 'text-slate-700');
      filterTable(btn.dataset.filter);
    });
  });

  // Event listener klik backdrop & keyboard Escape untuk menutup modal
  document.getElementById('studentInfoModal')?.addEventListener('click', (e) => {
    if (e.target.id === 'studentInfoModal') closeInfoModal();
  });
  document.getElementById('studentEditModal')?.addEventListener('click', (e) => {
    if (e.target.id === 'studentEditModal') closeEditModal();
  });
  document.getElementById('uploadDataModal')?.addEventListener('click', (e) => {
    if (e.target.id === 'uploadDataModal') closeUploadModal();
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeInfoModal();
      closeEditModal();
      closeUploadModal();
    }
  });
});

