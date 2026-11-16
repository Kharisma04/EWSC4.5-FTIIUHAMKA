# Early Warning System (EWS) C4.5 - FTII UHAMKA

Aplikasi web sistem peringatan dini (*Early Warning System*) berbasis PHP modular untuk memprediksi keberhasilan studi mahasiswa FTII UHAMKA menggunakan Algoritma **Decision Tree C4.5**, dibangun khusus berdasarkan hasil riset skripsi:

* **Judul Skripsi**: *Penerapan Algoritma Decision Tree C4.5 sebagai Early Warning System Prediksi Keberhasilan Studi Mahasiswa FTII UHAMKA*
* **Peneliti**: Kharisma Rahmawati Lubis (NIM: 2203015072)
* **Program Studi**: Teknik Informatika, Fakultas Teknologi Industri dan Informatika (FTII), UHAMKA Jakarta (2026)

---

## 🚀 Cara Menjalankan Aplikasi di XAMPP

1. Pastikan folder aplikasi berada di direktori htdocs XAMPP:
   `C:\xampp\htdocs\ews-uhamka`
2. Buka aplikasi **XAMPP Control Panel**, lalu klik tombol **Start** pada modul **Apache**.
3. Buka peramban (Chrome / Edge / Firefox) dan kunjungi alamat:
   👉 **`http://localhost/ews-uhamka/`*
---
http://localhost/ews-uhamka/
## 📁 Struktur Direktori Modular & Profesional

Aplikasi telah disusun rapi sesuai standar arsitektur profesional dan kaidah pemrograman bersih:

```text
C:\xampp\htdocs\ews-uhamka\
├── index.php                      # Halaman Utama (Dashboard & Pangkalan Data Mahasiswa)
├── app/                           # Core Logic, Algoritma, & Layanan Data
│   ├── config.php                 # Konfigurasi global & metadata akademik skripsi
│   ├── c45_engine.php             # Logika inti perhitungan rumus C4.5, Entropy, Gain, Alpha & Beta
│   ├── student_service.php        # Service CRUD & manipulasi data mahasiswa (JSON storage)
│   ├── api_student.php            # Endpoint API untuk interaksi edit, hapus, dan simpan data
│   └── data/
│       ├── students.json          # Database 438 data riil mahasiswa dari file Excel skripsi
│       └── students_default.json  # Salinan data cadangan asli untuk fitur Reset Data
├── components/                    # Komponen Tampilan Antarmuka Terpisah (Modular)
│   ├── header.php                 # Tag <head>, konfigurasi CDN Tailwind, Chart.js, & fonts
│   ├── navbar.php                 # Topbar navigasi atas, breadcrumb, status sistem, tombol cetak
│   ├── sidebar.php                # Sidebar navigasi samping, logo FTII UHAMKA, identitas peneliti
│   └── footer.php                 # Footer hak cipta, Modal Info Detail (ℹ️), & Modal Edit (✏️)
├── pages/                         # Direktori Fitur Khusus Berdasarkan Modul
│   ├── uji-coba/                  # Fitur Khusus: Form Simulasi & Uji Coba Data Mahasiswa Mandiri
│   │   └── index.php
│   └── rumus-c45/                 # Fitur Khusus: Dokumentasi Rumus C4.5, Pohon Keputusan, & Evaluasi
│       └── index.php
└── assets/                        # Aset Statis
    ├── css/
    │   └── styles.css             # Stylesheet kustom modern (glassmorphism & print view)
    └── js/
        └── app.js                 # Pengendali client-side (Chart.js, filter, pencarian, modals)
```

---

## 📐 Di Mana Letak Logika Perhitungan Rumusnya?

Seluruh logika perhitungan matematis C4.5 terpusat di **`app/c45_engine.php`** dan divisualisasikan secara interaktif pada halaman **`pages/rumus-c45/index.php`**:

### 1. Rumus Entropy
$$\text{Entropy}(S) = \sum_{i=1}^{n} -p_i \cdot \log_2(p_i)$$
Mengukur tingkat keberagaman/ketidakpastian data kelas target (*Berpotensi Tepat Waktu* vs *Berisiko Terhambat*).

### 2. Rumus Information Gain
$$\text{Gain}(S, A) = \text{Entropy}(S) - \sum_{i=1}^{n} \frac{|S_i|}{|S|} \cdot \text{Entropy}(S_i)$$
Mengukur seberapa besar atribut $A$ mereduksi ketidakpastian data untuk memilih simpul pohon terbaik.

### 3. Hasil Perhitungan Riset Skripsi (Bab 4)
* **Simpul Akar (*Root Node*)**: **Kehadiran** terpilih sebagai akar utama karena memiliki **Gain tertinggi = 0,720** (dibanding IPK = 0,095 dan SKS = 0,006).
  * **Aturan 1**: `Jika Kehadiran ≤ 72,50% maka Berisiko Terhambat`.
* **Simpul Cabang (*Internal Node*)**: Pada cabang Kehadiran $> 72,50\%$, dilakukan perhitungan gain lanjutan dan atribut **Rata-rata IPK** memiliki **Gain tertinggi = 0,450**.
  * **Aturan 2**: `Jika Kehadiran > 72,50% dan IPK ≤ 2,995 maka Berisiko Terhambat`.
  * **Aturan 3**: `Jika Kehadiran > 72,50% dan IPK > 2,995 maka Berpotensi Tepat Waktu`.
* **Atribut Total SKS Lulus**: Tereliminasi dari pohon karena nilai Gain sangat rendah (0,006) dan beban SKS semester berjalan relatif seragam.

---

## 🧪 Perhitungan Error Alpha (α) dan Beta (β) Evaluasi Model EWS

Berdasarkan pengujian 87 data *testing* skripsi di RapidMiner dan landasan teori metrik evaluasi machine learning (*Rainio, 2024*):

### Matriks Konfusi (Perspektif Deteksi Risiko EWS)
* **True Positive ($TP$) = 34**: Mahasiswa berisiko yang terdeteksi secara tepat.
* **False Negative ($FN$) = 5**: Mahasiswa berisiko yang gagal terdeteksi (lolos).
* **False Positive ($FP$) = 0**: Mahasiswa aman yang keliru divonis berisiko (*Alarm Palsu*).
* **True Negative ($TN$) = 48**: Mahasiswa aman yang diprediksi benar.

### 1. Error Alpha ($\alpha$) — *Type I Error / False Alarm Rate*
$$\alpha = \frac{FP}{FP + TN} = \frac{0}{0 + 48} = \mathbf{0{,}00\%}$$
* **Interpretasi**: Sistem EWS memiliki nilai **Alpha = 0%**, yang berarti **bebas dari alarm palsu** (*zero false alarm*). Dosen PA tidak dibebani oleh pemanggilan mahasiswa yang sebenarnya tidak bermasalah.

### 2. Error Beta ($\beta$) — *Type II Error / Missed Detection Rate*
$$\beta = \frac{FN}{TP + FN} = \frac{5}{34 + 5} = \frac{5}{39} = \mathbf{12{,}82\%}$$
* **Interpretasi**: Tingkat risiko mahasiswa bermasalah yang luput dari deteksi sistem hanya sebesar **12,82%** (5 dari 39 mahasiswa).

### 3. Daya Uji Deteksi / *Power of Test* (Sensitivitas = $1 - \beta$)
$$\text{Power} = 1 - \beta = 100\% - 12{,}82\% = \mathbf{87{,}18\%}$$
* **Interpretasi**: Sensitivitas sistem EWS dalam menjaring mahasiswa yang berpotensi mengalami keterlambatan studi mencapai **87,18%**.

### 4. Akurasi Global Model
$$\text{Accuracy} = \frac{TP + TN}{\text{Total Data Uji}} = \frac{34 + 48}{87} = \mathbf{94{,}25\%}$$

---

## 🌟 Fitur Utama Sistem

1. **Pangkalan Data 438 Mahasiswa Riil**:
   * Data diimpor langsung dari file Excel penelitian: `dataset_final_status_akademik.xlsx`.
2. **Ikon Info (ℹ️) di Setiap Nama Mahasiswa**:
   * Ketika diklik, membuka modal yang **menjabarkan secara komprehensif kondisi studi mahasiswa**: mengapa dia berstatus berisiko/tepat waktu berdasarkan ambang batas kehadiran dan IPK, aturan C4.5 yang terpicu, serta rekomendasi intervensi konkret bagi Dosen PA.
3. **Ikon Edit (✏️) di Kolom Aksi**:
   * Membuka modal formulir untuk mengedit parameter mahasiswa (Nama, Kehadiran, IPK, SKS) dan sistem **langsung mengkalkulasi ulang status C4.5** serta memperbarui grafik dan tabel.
4. **Form Simulasi & Uji Coba Mandiri C4.5 (`pages/uji-coba/`)**:
   * Dilengkapi tombol preset 1-klik (Presensi Rendah, IPK Rendah, Jalur Aman), kalkulasi real-time, dan tombol simpan permanen ke pangkalan data.
5. **Dokumentasi Rumus & Evaluasi Alpha-Beta (`pages/rumus-c45/`)**:
   * Dilengkapi visualisasi diagram pohon keputusan dan matriks konfusi interaktif untuk presentasi sidang skripsi.
6. **Format Cetak Dokumen Laporan**:
   * Tombol cetak langsung memformat tampilan dokumen bebas dari navigasi sidebar agar rapi di atas kertas A4/PDF.
