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
    tbody.innerHTML = `<tr><td colspan="8" class="text-center py-10 text-slate-400 font-medium">Tidak ada data mahasiswa yang cocok dengan kriteria pencarian.</td></tr>`;
    renderPagination(0);
    return;
  }

  pageItems.forEach((std, i) => {
    const isHambat = std.status === 'Berisiko Terhambat';
    const rowIdx = startIdx + i + 1;
    const stdJson = JSON.stringify(std).replace(/"/g, '&quot;');

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
      <td class="py-3 px-3 text-center text-slate-600">${std.sks}</td>
      <td class="py-3 px-3 text-center">
        <span class="px-2.5 py-1 text-[11px] rounded-full font-bold border inline-block ${isHambat ? 'bg-rose-100 text-rose-800 border-rose-200' : 'bg-emerald-100 text-emerald-800 border-emerald-200'}">
          ${std.status}
        </span>
      </td>
      <td class="py-3 px-3 text-center">
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
      </td>
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
    if (statusFilter === 'hambat') matchStatus = s.status === 'Berisiko Terhambat';
    if (statusFilter === 'tepat') matchStatus = s.status === 'Berpotensi Tepat Waktu';

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

  const c45 = classifyC45Client(student.kehadiran, student.ipk);

  const nameEl = document.getElementById('infoStudentName') || document.getElementById('infoModalTitle');
  if (nameEl) nameEl.innerText = student.nama;

  const nimEl = document.getElementById('infoStudentNim');
  if (nimEl) nimEl.innerText = `NIM: ${student.nim} · ${student.prodi || 'Teknik Informatika'}`;
  
  const badge = document.getElementById('infoStatusBadge');
  if (badge) {
    badge.className = `px-2.5 py-0.5 rounded-full font-bold text-[11px] border ${c45.badgeClass}`;
    badge.innerText = c45.status;
  }

  const hEl = document.getElementById('infoKehadiranVal');
  if (hEl) hEl.innerText = `${parseFloat(student.kehadiran).toFixed(1)}%`;

  const ipkEl = document.getElementById('infoIpkVal');
  if (ipkEl) ipkEl.innerText = parseFloat(student.ipk).toFixed(2);

  const sksEl = document.getElementById('infoSksVal');
  if (sksEl) sksEl.innerText = `${student.sks} SKS`;

  const ruleEl = document.getElementById('infoRuleTitle');
  if (ruleEl) ruleEl.innerText = c45.ruleTitle;

  const covEl = document.getElementById('infoCoverage');
  if (covEl) covEl.innerText = c45.coverage;

  const reasonEl = document.getElementById('infoReason');
  if (reasonEl) reasonEl.innerText = c45.reason;

  const recEl = document.getElementById('infoRecommendation');
  if (recEl) recEl.innerText = c45.recommendation;

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
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeInfoModal();
      closeEditModal();
    }
  });
});

