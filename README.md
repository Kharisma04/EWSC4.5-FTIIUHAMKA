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
C:\laragon\www\EWSC4.5-FTIIUHAMKA\
├── index.php                      # Halaman Utama (Dashboard & Pangkalan Data Mahasiswa)
├── login.php                      # Halaman Login Masuk Sistem (Autentikasi & Akun Demo)
├── logout.php                     # Endpoint Logout dan Pembersihan Sesi
├── app/                           # Core Logic, Algoritma, & Layanan Data
│   ├── config.php                 # Konfigurasi global & metadata akademik skripsi
│   ├── auth.php                   # Layanan otentikasi login, logout, dan penjaga sesi
│   ├── c45_engine.php             # Logika inti rumus Decision Tree C4.5, Entropy, & Gain
│   ├── naive_bayes_engine.php     # Logika klasifikasi Gaussian Naive Bayes & Probabilitas Posterior
│   ├── algorithm_service.php      # Layanan pemilihan algoritma dinamis (C4.5 vs Naive Bayes)
│   ├── student_service.php        # Service CRUD, validasi NIM FTII UHAMKA, & JSON persistence
│   ├── data_importer.php          # Parser & pengimpor data berkas CSV & Excel (.xlsx)
│   ├── api_student.php            # Endpoint REST/JSON API untuk operasi data & klasifikasi
│   └── data/
│       ├── users.json             # Data akun pengguna (admin & mahasiswa peneliti skripsi)
│       ├── students.json          # Database 437 data riil mahasiswa dari file Excel skripsi
│       └── students_default.json  # Salinan data cadangan asli untuk fitur Reset Data
├── components/                    # Komponen Tampilan Antarmuka Terpisah (Modular)
│   ├── header.php                 # Tag <head>, konfigurasi CDN Tailwind, Chart.js, & fonts
│   ├── navbar.php                 # Topbar navigasi atas, pemilih algoritma, info pengguna, logout
│   ├── sidebar.php                # Sidebar samping, logo UHAMKA, pemilih model, tombol logout
│   └── footer.php                 # Footer, Modal Info Detail, Modal Edit, & Modal Upload Data
├── pages/                         # Direktori Fitur Khusus Berdasarkan Modul
│   └── uji-coba/                  # Fitur: Form Simulasi Prediksi Mandiri (C4.5 & Naive Bayes)
│       └── index.php
└── assets/                        # Aset Statis
    ├── css/
    │   └── styles.css             # Stylesheet kustom modern
    ├── js/
    │   └── app.js                 # Pengendali client-side (Chart.js, filter, upload, client prediction)
    └── template_mahasiswa.csv     # Berkas contoh format impor data mahasiswa
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

1. **Autentikasi & Keamanan Sesi (`login.php` & `logout.php`)**:
   * Sistem EWS kini terlindungi oleh halaman otentikasi resmi beridentitas FTII UHAMKA.
   * Akun bawaan (*default demo*):
     * **Dosen PA / Admin**: `admin` / `admin123`
     * **Peneliti Skripsi**: `kharisma` / `uhamka2026`
   * Tombol Logout mudah diakses melalui Menu Sidebar, Profil Pengguna di bawah Sidebar, dan Topbar.

2. **Dukungan Multi-Model: Decision Tree C4.5 & Gaussian Naive Bayes**:
   * Pengguna dapat memilih model klasifikasi yang ingin digunakan kapan saja via Navbar, Sidebar, atau Dashboard banner.
   * **Dinamika Angka & Distribusi Terhitung Otomatis**:
     * **C4.5 (94,25%)**: Berbasis aturan pohon keputusan (*Root Node* Kehadiran 72,50% & *Internal Node* IPK 2,995). Menghasilkan **241 Tepat Waktu (55,1%)** dan **196 Berisiko (44,9%)**.
     * **Naive Bayes (91,80%)**: Berbasis probabilitas posterior Gaussian data riil 437 mahasiswa ($P(\text{Tepat Waktu}) = 55,15\%$, $P(\text{Berisiko}) = 44,85\%$). Menghasilkan **246 Tepat Waktu (56,3%)** dan **191 Berisiko (43,7%)**.
   * Kartu KPI Dashboard, Donut Chart, baris tabel mahasiswa, dan Form Simulasi otomatis memperbarui angka dan persentase sesuai algoritma yang dipilih.
   * **Komparasi Langsung (*Side-by-Side Model Comparison*)**: Form simulasi dan modal detail mahasiswa menyajikan perbandingan langsung antara keputusan C4.5 (Aturan Pohon) vs Naive Bayes (Probabilitas Posterior Gaussian), lengkap dengan notifikasi jika kedua model sepakat atau terdapat perbedaan pada zona batas.

3. **Unggah Berkas Data CSV & Excel (.xlsx)**:
   * Fitur unggah berkas langsung melalui tombol pada tabel dashboard.
   * Mendukung mode **Gabungkan (Merge)** dan **Gantikan Semua (Replace)**.
   * Menggunakan parser berkas murni mandiri tanpa dependensi luar.
   * Tersedia template unduhan: `assets/template_mahasiswa.csv`.

4. **Form Simulasi Mandiri & Validasi Ketat NIM FTII UHAMKA (`pages/uji-coba/`)**:
   * Bidang SKS telah dihapus agar formulir fokus pada 2 parameter penentu: **Kehadiran (%)** dan **IPK**.
   * **Pencegahan Data Kampus Luar**: Sistem memvalidasi kodefikasi NIM format FTII UHAMKA (`2x03015xxx`). NIM dari kampus lain otomatis ditolak dengan pesan peringatan eror.
   * Auto-lookup nama mahasiswa yang sudah terdaftar di pangkalan data secara instan saat NIM dimasukkan.

5. **Pangkalan Data 437 Mahasiswa Riil**:
   * Data diimpor langsung dari file Excel penelitian skripsi.
6. **Ikon Info (ℹ️) di Setiap Nama Mahasiswa**:
   * Menjabarkan secara komprehensif kondisi studi mahasiswa sesuai algoritma yang sedang aktif, aturan/distribusi yang terpicu, dan rekomendasi intervensi DPA.
7. **Ikon Edit (✏️) di Kolom Aksi**:
   * Mengedit parameter mahasiswa dan sistem langsung mengkalkulasi ulang status kelulusan.
8. **Format Cetak Dokumen Laporan**:
   * Tombol cetak langsung memformat tampilan dokumen bebas dari navigasi sidebar agar rapi di atas kertas A4/PDF.
