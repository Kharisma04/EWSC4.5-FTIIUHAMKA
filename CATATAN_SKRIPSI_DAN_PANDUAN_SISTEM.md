# CATATAN PRIBADI & PANDUAN LENGKAP SISTEM
# EARLY WARNING SYSTEM (EWS) C4.5 - FTII UHAMKA

> **Dokumen Catatan & Panduan Komprehensif Persiapan Sidang Skripsi**  
> **Judul Skripsi**: *Penerapan Algoritma Decision Tree C4.5 sebagai Early Warning System Prediksi Keberhasilan Studi Mahasiswa FTII UHAMKA*  
> **Peneliti**: Kharisma Rahmawati Lubis (NIM: 2203015072)  
> **Program Studi**: Teknik Informatika, Fakultas Teknologi Industri dan Informatika (FTII), UHAMKA Jakarta  
> **Tahun**: 2026  

---

## DAFTAR ISI

1. [Bagian 1: Landasan Skripsi, Dataset, & Latar Belakang Masalah](#bagian-1-landasan-skripsi-dataset--latar-belakang-masalah)
2. [Bagian 2: Logika Matematis Algoritma Decision Tree C4.5](#bagian-2-logika-matematis-algoritma-decision-tree-c45)
3. [Bagian 3: Analisis Error Alpha (α), Beta (β), & Performa Model](#bagian-3-analisis-error-alpha-α-beta-β--performa-model)
4. [Bagian 4: Arsitektur & Cara Pembuatan Codingan (Bedah File & Fungsi)](#bagian-4-arsitektur--cara-pembuatan-codingan-bedah-file--fungsi)
5. [Bagian 5: Panduan Cara Penggunaan Fitur Demi Fitur](#bagian-5-panduan-cara-penggunaan-fitur-demi-fitur)
6. [Bagian 6: Tanya-Jawab Kunci (FAQ) untuk Menghadapi Dosen Penguji Sidang](#bagian-6-tanya-jawab-kunci-faq-untuk-menghadapi-dosen-penguji-sidang)

---

# BAGIAN 1: LANDASAN SKRIPSI, DATASET, & LATAR BELAKANG MASALAH

### 1.1 Latar Belakang Masalah
Keterlambatan masa studi mahasiswa merupakan salah satu permasalahan strategis di perguruan tinggi yang mempengaruhi efisiensi prodi, penilaian akreditasi institusi, dan biaya pendidikan mahasiswa. Pada umumnya, evaluasi studi baru dilakukan di semester akhir ketika mahasiswa sudah terancam Drop Out (DO) atau masa studi habis (terlambat untuk diperbaiki).

Oleh karena itu, dibutuhkan sebuah **Early Warning System (EWS)** atau Sistem Peringatan Dini yang mampu mendeteksi potensi keterlambatan studi sedini mungkin (semester 4 - 6), sehingga **Dosen Pembimbing Akademik (DPA)** dapat memberikan bimbingan dan intervensi yang tepat sebelum mahasiswa terlanjur gagal lulus tepat waktu 4 tahun (8 semester).

### 1.2 Dataset Penelitian
* **Sumber Data**: Pangkalan data akademik mahasiswa program studi Teknik Informatika FTII UHAMKA (`dataset_final_status_akademik.xlsx`).
* **Jumlah Data Riil**: **438 data mahasiswa**.
* **Variabel Input (Atribut)**:
  1. **Kehadiran (%)** [Numerik/Real]: Persentase kedisiplinan kehadiran mahasiswa dalam perkuliahan.
  2. **Rata-rata IPK** [Numerik/Real]: Rata-rata Indeks Prestasi Kumulatif yang dicapai.
  3. **Total SKS Lulus** [Integer]: Akumulasi beban SKS yang telah diselesaikan.
* **Variabel Sasaran (Label Target - Binomial)**:
  1. `Berpotensi Tepat Waktu` (Memenuhi standar studi ideal 4 tahun / 8 semester).
  2. `Berisiko Terhambat` (Memerlukan konseling akademik dan bimbingan khusus dari DPA).

---

# BAGIAN 2: LOGIKA MATEMATIS ALGORITMA DECISION TREE C4.5

Algoritma **Decision Tree C4.5** (pengembangan dari ID3 oleh Ross Quinlan) bekerja dengan membentuk struktur pohon keputusan hierarkis berdasarkan pemilihan atribut terbaik menggunakan konsep teori informasi: **Entropy** dan **Information Gain**.

### 2.1 Rumus 1: Entropy
Entropy digunakan untuk mengukur tingkat keberagaman (*ketidakmurnian/heterogenitas*) dari suatu himpunan data terhadap kelas sasaran:

$$\text{Entropy}(S) = \sum_{i=1}^{n} -p_i \cdot \log_2(p_i)$$

* **$S$**: Himpunan data mahasiswa yang sedang dievaluasi.
* **$n$**: Jumlah kelas target ($n = 2$: *Berpotensi Tepat Waktu* dan *Berisiko Terhambat*).
* **$p_i$**: Proporsi jumlah data kelas ke-$i$ terhadap total data pada himpunan $S$.
* **Sifat Nilai**:
  * $\text{Entropy} = 0$: Himpunan data homogen sempurna (semua sampel memiliki kelas yang sama, sudah murni).
  * $\text{Entropy} = 1$: Himpunan data heterogen maksimal (seimbang 50% kelas positif dan 50% kelas negatif).

### 2.2 Rumus 2: Information Gain
Information Gain digunakan untuk mengetahui seberapa besar pengaruh suatu atribut dalam memisahkan data ke dalam kelas target (mengukur penurunan nilai entropi):

$$\text{Gain}(S, A) = \text{Entropy}(S) - \sum_{i=1}^{n} \frac{|S_i|}{|S|} \cdot \text{Entropy}(S_i)$$

* **$A$**: Atribut yang diuji (*Kehadiran*, *Rata-rata IPK*, *Total SKS*).
* **$|S_i|$**: Jumlah data mahasiswa pada partisi cabang ke-$i$.
* **$|S|$**: Jumlah seluruh data mahasiswa pada himpunan $S$.

---

### 2.3 Hasil Perhitungan Data Skripsi (Bab 4.3)

#### A. Penentuan Simpul Akar (*Root Node*)
Berdasarkan perhitungan nilai Information Gain terhadap seluruh dataset (Tabel 4.5 naskah skripsi):
* **Kehadiran (%)**: $\text{Gain} = \mathbf{0{,}720}$ (Tertinggi Mutlak)
* **Rata-rata IPK**: $\text{Gain} = 0{,}095$
* **Total SKS**: $\text{Gain} = 0{,}006$

> **Kesimpulan Ilmiah Simpul Akar**:  
> Atribut **Kehadiran** terpilih sebagai **Simpul Akar (*Root Node*)** karena memiliki nilai Gain tertinggi (0,720). Secara akademis, kedisiplinan presensi merupakan *leading behavioral indicator* (indikator perilaku awal yang terjadi paling pertama sebelum nilai IPK anjlok). Mahasiswa yang mulai malas hadir kuliah hampir pasti nilainya akan turun.
>
> Ambang batas pemisahan yang diperoleh: **Kehadiran $\le 72{,}50\%$**.
> * **Cabang 1 (Kehadiran $\le 72{,}50\%$)**: Menghasilkan 138 data *Berisiko Terhambat* dan 0 data *Tepat Waktu* ($\text{Entropy} = 0$). Karena sudah homogen, cabang ini langsung menjadi **Daun Keputusan (Leaf Node: Berisiko Terhambat)**.
> * **Cabang 2 (Kehadiran $> 72{,}50\%$)**: Berisi 266 data (241 Tepat Waktu dan 25 Berisiko). Nilai $\text{Entropy} = 0{,}450$. Maka dilakukan perhitungan gain lanjutan untuk atribut yang tersisa.

#### B. Perhitungan Lanjutan pada Cabang Kehadiran $> 72{,}50\%$ (Simpul Internal)
Berdasarkan Tabel 4.6 naskah skripsi pada cabang ini:
* **Rata-rata IPK** (ambang batas $\le 2{,}995$): $\text{Gain} = \mathbf{0{,}450}$ (Tertinggi)
* **Total SKS** (ambang batas 93): $\text{Gain} = 0{,}028$
* **Kehadiran lanjutan**: $\text{Gain} = 0{,}012$

> **Kesimpulan Ilmiah Simpul Cabang**:  
> Atribut **Rata-rata IPK** terpilih sebagai **Simpul Internal (Node 2)** dengan ambang batas **$\le 2{,}995$**.
> * **Sub-Cabang A (IPK $\le 2{,}995$)**: Terdiri dari 25 data *Berisiko Terhambat* dan 0 data *Tepat Waktu* ($\text{Entropy} = 0$). Menjadi **Daun Keputusan: Berisiko Terhambat**.
> * **Sub-Cabang B (IPK $> 2{,}995$)**: Terdiri dari 241 data *Berpotensi Tepat Waktu* dan 0 data *Berisiko Terhambat* ($\text{Entropy} = 0$). Menjadi **Daun Keputusan: Berpotensi Tepat Waktu**.

#### C. Mengapa Total SKS Tereliminasi?
Atribut Total SKS hanya memiliki Gain sebesar 0,006 pada node awal dan 0,028 pada cabang kedua. Pada mahasiswa semester awal hingga pertengahan di sistem perkuliahan paket/reguler, beban SKS yang diambil mahasiswa cenderung seragam (sekitar 20 SKS per semester), sehingga tidak memiliki daya pembeda (*information gain*) yang signifikan dalam membedakan kelulusan.

---

### 2.4 Aturan Pohon Keputusan (Rule Base) yang Terbentuk

Dari hasil algoritma C4.5, terbentuk **3 Aturan Utama**:

```text
[ATURAN 1 (Root Node)]:
IF Kehadiran <= 72.50%
THEN Status = "Berisiko Terhambat"

[ATURAN 2 (Internal Node)]:
IF Kehadiran > 72.50% AND Rata-rata IPK <= 2.995
THEN Status = "Berisiko Terhambat"

[ATURAN 3 (Kondisi Aman / Jalur Hijau)]:
IF Kehadiran > 72.50% AND Rata-rata IPK > 2.995
THEN Status = "Berpotensi Tepat Waktu"
```

---

# BAGIAN 3: ANALISIS ERROR ALPHA (α), BETA (β), & PERFORMA MODEL

Evaluasi kinerja model klasifikasi pada skripsi dilakukan menggunakan **87 Data Testing** (proporsi 80% data latih dan 20% data uji) menggunakan operator Performance pada RapidMiner serta rujukan teori evaluasi statistik machine learning (*Rainio, 2024*).

### 3.1 Tabel Matriks Konfusi (Confusion Matrix) 87 Data Uji

Dalam perspektif **Sistem Peringatan Dini (EWS)** di mana kelas positif yang ingin dideteksi adalah **"Berisiko Terhambat"**:

| Kondisi Aktual | Prediksi: Berisiko Terhambat | Prediksi: Berpotensi Tepat Waktu | Total Aktual |
| :--- | :---: | :---: | :---: |
| **Aktual: Berisiko Terhambat** | **TP = 34** | **FN = 5** | 39 |
| **Aktual: Berpotensi Tepat Waktu** | **FP = 0** | **TN = 48** | 48 |
| **Total Prediksi** | 34 | 53 | **87** |

* **True Positive (TP) = 34**: Mahasiswa yang sebenarnya bermasalah dan berhasil dideteksi benar oleh sistem sebagai *Berisiko Terhambat*.
* **False Negative (FN) = 5**: Mahasiswa bermasalah yang terlewat (gagal dideteksi/diprediksi tepat waktu).
* **False Positive (FP) = 0**: Mahasiswa aman yang keliru divonis berisiko (**Alarm Palsu / False Alarm**).
* **True Negative (TN) = 48**: Mahasiswa aman yang diprediksi benar sebagai *Berpotensi Tepat Waktu*.

---

### 3.2 Perhitungan Error Alpha ($\alpha$) — Kesalahan Tipe I (*False Alarm Rate*)

Error Alpha ($\alpha$) mengukur probabilitas kesalahan sistem dalam memberikan peringatan palsu (*false positive rate*):

$$\alpha = \frac{FP}{FP + TN} = \frac{0}{0 + 48} = \frac{0}{48} = \mathbf{0{,}00\%}$$

* **Tingkat Spesifisitas / Kepercayaan ($1 - \alpha$)**:
  $$1 - \alpha = 100\% - 0{,}00\% = \mathbf{100{,}00\%}$$

> **Interpretasi Akademis untuk Sidang**:  
> Nilai $\alpha = 0{,}00\%$ membuktikan bahwa sistem EWS yang dibangun memiliki **keandalan maksimal tanpa alarm palsu**. Sistem tidak pernah salah memvonis mahasiswa yang sebenarnya rajin dan nilainya bagus sebagai mahasiswa berisiko. Hal ini sangat penting bagi Dosen PA agar tidak membuang waktu dan energi untuk memanggil mahasiswa yang sebenarnya tidak bermasalah.

---

### 3.3 Perhitungan Error Beta ($\beta$) — Kesalahan Tipe II (*Missed Detection Rate*)

Error Beta ($\beta$) mengukur probabilitas mahasiswa yang sebenarnya bermasalah tetapi gagal terdeteksi oleh sistem (*false negative rate*):

$$\beta = \frac{FN}{TP + FN} = \frac{5}{34 + 5} = \frac{5}{39} = \mathbf{12{,}82\%}$$

* **Daya Uji Deteksi / Sensitivitas / *Power of Test* ($1 - \beta$)**:
  $$\text{Power of Test} = 1 - \beta = 100\% - 12{,}82\% = \mathbf{87{,}18\%}$$

> **Interpretasi Akademis untuk Sidang**:  
> Nilai $\beta = 12{,}82\%$ berarti dari seluruh mahasiswa yang terhambat, hanya $12{,}82\%$ (5 orang) yang tidak tertangkap oleh radar sistem pada ambang batas saat ini. Sebaliknya, **daya deteksi EWS ($1 - \beta$) mencapai 87,18%**, membuktikan sistem memiliki sensitivitas yang sangat tinggi dalam mendeteksi potensi kegagalan studi secara dini.

---

### 3.4 Akurasi Global Model C4.5

$$\text{Accuracy} = \frac{TP + TN}{\text{Total Sampel Uji}} = \frac{34 + 48}{87} = \frac{82}{87} = \mathbf{94{,}25\%}$$

Model mampu memprediksi dengan benar sebanyak **82 dari 87 data mahasiswa** (tingkat ketepatan 94,25%).

---

# BAGIAN 4: ARSITEKTUR & CARA PEMBUATAN CODINGAN (BEDAH FILE & FUNGSI)

Aplikasi dibangun menggunakan arsitektur **PHP Modular Native** yang bersih, berstandar industri, tanpa ketergantungan framework yang rumit, sehingga sangat ringan, mudah dipahami, dan dapat langsung dijalankan pada server XAMPP (Apache).

### 4.1 Struktur Folder Proyek
```text
C:\xampp\htdocs\ews-uhamka\
├── index.php                      # Dashboard utama & pangkalan data mahasiswa
├── app/                           # Backend Engine & Layanan Data
│   ├── config.php                 # Konfigurasi konstanta, metadata skripsi, path helper
│   ├── c45_engine.php             # Logika matematis C4.5, Entropy, Gain, Alpha, Beta
│   ├── student_service.php        # Layanan CRUD (Create, Read, Update, Delete) data mahasiswa
│   ├── api_student.php            # Endpoint API JSON untuk AJAX
│   └── data/
│       ├── students.json          # Database aktif (438 data riil mahasiswa)
│       └── students_default.json  # Backup data skripsi untuk fitur Reset Data
├── components/                    # Potongan UI modular
│   ├── header.php                 # HTML Head, Tailwind CSS CDN, Chart.js CDN, Font
│   ├── navbar.php                 # Header atas (topbar), breadcrumb, badge status, cetak
│   ├── sidebar.php                # Navigasi kiri, identitas prodi & peneliti skripsi
│   └── footer.php                 # Footer hak cipta, Modal Info (ℹ️), Modal Edit (✏️)
├── pages/                         # Modul Fitur Berdasarkan Folder Khusus
│   ├── uji-coba/index.php         # Halaman simulasi uji mandiri data mahasiswa
│   └── rumus-c45/index.php        # Halaman dokumentasi rumus matematis & pohon C4.5
└── assets/                        # File Statis
    ├── css/styles.css             # CSS kustom (glassmorphism, scrollbar, print styling)
    └── js/app.js                  # Script controller (Chart.js, filter, modals, pagination)
```

---

### 4.2 Bedah Detail Kode & Fungsi Backend (`app/`)

#### 1. `app/config.php`
* **Fungsi**: Mendefinisikan konstanta penting skripsi secara terpusat agar tidak ada *hardcoding* di berbagai file:
  * `APP_NAME`, `RESEARCHER_NAME` ("Kharisma Rahmawati Lubis"), `RESEARCHER_NIM` ("2203015072").
  * Ambang batas C4.5: `C45_THRESHOLD_KEHADIRAN = 72.50` dan `C45_THRESHOLD_IPK = 2.995`.
  * Fungsi `get_root_path($current_dir)`: Secara cerdas menghitung kedalaman folder (`./` atau `../../`) agar seluruh file aset (CSS, JS, gambar) dan tautan menu tidak pernah rusak (*broken link*) baik di root maupun di dalam subfolder `pages/...`.

#### 2. `app/c45_engine.php` (Inti Algoritma)
* **Fungsi `c45_calculate_entropy($c1, $c2)`**:
  * Mengambil jumlah sampel kelas positif dan negatif.
  * Jika salah satu kelas 0 atau total 0, langsung mengembalikan `0.0` (homogen sempurna).
  * Menghitung $-p_1 \log_2(p_1) - p_2 \log_2(p_2)$ menggunakan fungsi `log($p, 2)` di PHP.
* **Fungsi `c45_calculate_gain($entropy_total, $subsets)`**:
  * Menghitung selisih antara entropi total dengan penjumlahan bobot entropi tiap partisi cabang subset.
* **Fungsi `c45_classify($kehadiran, $ipk, $sks)`**:
  * Mengimplementasikan pohon keputusan yang dihasilkan di Bab 4:
    * `if ($kehadiran <= 72.50)` $\to$ Mengembalikan `status = 'Berisiko Terhambat'`, `rule_number = 1`, beserta teks penjabaran analisis studi presensi dan tindakan panggilan konseling DPA.
    * `elseif ($ipk <= 2.995)` $\to$ Mengembalikan `status = 'Berisiko Terhambat'`, `rule_number = 2`, beserta teks analisis nilai akademik dan rekomendasi bimbingan remedial/tutor sebaya.
    * `else` $\to$ Mengembalikan `status = 'Berpotensi Tepat Waktu'`, `rule_number = 3`, beserta analisis kondisi studi aman dan rekomendasi persiapan skripsi/MBKM.
* **Fungsi `c45_get_evaluation_metrics()`**:
  * Mengembalikan array komprehensif berisi nilai konfusi matriks ($TP=34, FN=5, FP=0, TN=48$), nilai Akurasi ($94{,}25\%$), Error Alpha ($\alpha = 0{,}00\%$), Error Beta ($\beta = 12{,}82\%$), dan Power of Test ($87{,}18\%$).

#### 3. `app/student_service.php` (Layanan Data)
* Mengelola manipulasi data mahasiswa pada file `app/data/students.json`:
  * `student_get_all()`: Membaca seluruh array data mahasiswa dari JSON.
  * `student_find_by_nim($nim)`: Mencari data mahasiswa berdasarkan NIM dan langsung menggabungkannya dengan hasil kalkulasi `c45_classify()`.
  * `student_save($input)`: Menghitung status C4.5 mahasiswa baru dan menyisipkannya ke baris teratas array (`array_unshift`), lalu menulis kembali ke `students.json`.
  * `student_update($nim, $input)`: Memperbarui parameter mahasiswa yang diedit dan otomatis menghitung ulang status kelulusannya.
  * `student_delete($nim)`: Menghapus data mahasiswa dari file JSON.
  * `student_reset()`: Mengembalikan file `students.json` ke kondisi default awal (`students_default.json` berisi 438 data skripsi).
  * `student_get_kpis()`: Menghitung total mahasiswa terpantau, jumlah tepat waktu, jumlah berisiko, serta persentasenya untuk kartu ringkasan KPI.

#### 4. `app/api_student.php` (Endpoint REST API)
* Menangani pertukaran data secara asinkron (AJAX Fetch API) antara antarmuka JavaScript dan backend PHP untuk aksi: `get`, `update`, `add`, `delete`, `reset`, dan `classify`.

---

### 4.3 Bedah Detail Komponen Antarmuka (`components/`)
* **`components/header.php`**: Berisi meta tag skripsi, memuat font Google (*Plus Jakarta Sans* untuk UI modern dan *JetBrains Mono* untuk angka/NIM), library Tailwind CSS melalui CDN, library Chart.js, dan memanggil `styles.css`.
* **`components/navbar.php`**: Header atas berdesain *glassmorphism* (efek kaca transparan) yang memuat breadcrumb judul halaman, badge status EWS aktif, badge nilai akurasi 94,25%, badge error $\alpha = 0{,}00\%$, serta tombol Cetak Laporan.
* **`components/sidebar.php`**: Menu navigasi samping kiri yang responsif (dapat dibuka-tutup di layar ponsel). Menampilkan logo FTII UHAMKA, link menu aktif (Dashboard, Uji Coba, Rumus C4.5), dan kartu identitas peneliti (Kharisma Rahmawati Lubis - NIM 2203015072).
* **`components/footer.php`**: Memuat informasi hak cipta skripsi 2026, script pemanggil `app.js`, serta **2 Modal Dialog Utama**:
  1. **Modal Info Detail Mahasiswa (`#studentInfoModal`)**: Berisi kartu parameter, aturan C4.5 yang terpicu, paragraf penjabaran kondisi studi, dan paragraf rekomendasi tindakan DPA.
  2. **Modal Edit Data Mahasiswa (`#studentEditModal`)**: Formulir interaktif untuk mengedit NIM, Nama, Kehadiran, IPK, dan SKS mahasiswa lengkap dengan kotak *live preview status C4.5*.

---

### 4.4 Bedah Interaktivitas JavaScript (`assets/js/app.js`)
* **`initStudentTable(data)`**: Menginisialisasi array 438 data mahasiswa dan mengatur sistem *pagination* (15 baris per halaman) agar tabel dimuat super cepat dan ringan tanpa membebani browser.
* **`filterTable(status)` & Pencarian Instan**: Memfilter data secara *real-time* saat pengguna mengetik nama/NIM atau mengklik tombol filter (Semua / Berisiko / Tepat Waktu).
* **`initRiskChart(tepat, hambat)`**: Menggambar grafik visual Donut Chart interaktif menggunakan Chart.js dengan persentase otomatis.
* **`openInfoModal(student)`**: Mengambil data mahasiswa yang diklik dan menampilkan modal diagnosis lengkap beserta rekomendasi DPA.
* **`openEditModal(student)` & `previewEditCalculation()`**: Menampilkan modal edit dan secara *real-time* menghitung status baru saat pengguna mengubah nilai kehadiran atau IPK di formulir edit sebelum disimpan.
* **`handleEditSubmit(event)`**: Mengirim data yang telah diubah ke `app/api_student.php?action=update` via Fetch API.

---

# BAGIAN 5: PANDUAN CARA PENGGUNAAN FITUR DEMI FITUR

Aplikasi web EWS C4.5 ini dirancang sangat intuitif dan mudah digunakan oleh civitas akademika (Dosen PA, staf prodi, maupun penguji skripsi):

### 5.1 Fitur Halaman Dashboard (`index.php`)
1. **Melihat 4 Kartu KPI Strip**:
   * *Total Terpantau*: Menampilkan total mahasiswa aktif yang terdata dalam pangkalan data (438 mahasiswa).
   * *Berpotensi Tepat Waktu*: Menampilkan jumlah dan persentase mahasiswa di jalur aman.
   * *Berisiko Terhambat*: Menampilkan jumlah mahasiswa yang membutuhkan bimbingan khusus dari DPA.
   * *Performa C4.5*: Menampilkan akurasi model 94,25% dan nilai Error $\alpha = 0{,}00\%$.
2. **Melihat Grafik Donut Distribusi**:
   * Menampilkan visualisasi persentase perbandingan mahasiswa aman (warna hijau) vs berisiko (warna merah). Arahkan kursor (*hover*) pada grafik untuk melihat jumlah detail mahasiswa.
3. **Membaca 3 Kartu Panduan Ambang Batas EWS**:
   * Membantu dosen mengingat batas kritis evaluasi: Kehadiran $\le 72{,}50\%$ (Aturan 1), IPK $\le 2{,}995$ (Aturan 2), dan Jalur Aman (Aturan 3).
4. **Pencarian dan Filter Cepat**:
   * Ketikkan nama atau NIM pada kotak pencarian untuk mencari mahasiswa secara instan.
   * Klik tombol **Berisiko** untuk menyaring hanya mahasiswa yang butuh bimbingan DPA.
   * Klik tombol **Tepat Waktu** untuk menyaring mahasiswa yang aman.
   * Gunakan tombol **← Sebelumnya** dan **Selanjutnya →** di bawah tabel untuk berpindah halaman pagination.

---

### 5.2 Fitur Ikon Info Diagnosis Studi (ℹ️)
1. Pada tabel data mahasiswa di Dashboard, perhatikan tombol bulat biru **ℹ** di samping setiap nama mahasiswa.
2. Klik tombol **ℹ** tersebut.
3. Jendela **Modal Diagnosis Status EWS** akan langsung terbuka:
   * Menampilkan parameter riil mahasiswa (Kehadiran %, IPK, Total SKS).
   * Menampilkan aturan pohon keputusan C4.5 yang terpicu.
   * **Penjabaran Kondisi Studi**: Membaca paragraf analisis mengapa mahasiswa tersebut berisiko terhambat atau berpotensi tepat waktu.
   * **Rekomendasi DPA**: Membaca saran tindakan konkret yang harus diambil oleh Dosen Pembimbing Akademik (misal: pemanggilan konseling presensi ujian atau bimbingan tutor sebaya).
4. Klik tombol **Tutup Informasi** untuk kembali.

---

### 5.3 Fitur Ikon Edit Data Mahasiswa (✏️)
1. Pada kolom paling kanan (Aksi) di baris mahasiswa manapun, klik tombol **✏️ Edit**.
2. Jendela **Modal Perbarui Parameter Mahasiswa** akan terbuka.
3. Ubah nilai yang diinginkan, misalnya:
   * Mengubah *Kehadiran* dari 60% menjadi 85%.
   * Mengubah *IPK* dari 2.50 menjadi 3.40.
4. Perhatikan kotak **Prediksi Status Baru** di bagian bawah form: status dan badge akan **langsung berubah secara otomatis (*live preview*)** mengikuti aturan C4.5.
5. Klik **💾 Simpan Perubahan**. Data di file JSON akan diperbarui secara permanen dan tabel serta grafik KPI akan langsung ter-update otomatis.

---

### 5.4 Fitur Form Simulasi & Uji Mandiri Data Mahasiswa (`pages/uji-coba/`)
Menu ini digunakan ketika DPA atau staf prodi ingin menguji data mahasiswa baru di luar pangkalan data:
1. Klik menu **Form Simulasi & Uji C4.5** pada sidebar kiri.
2. Anda memiliki 2 opsi pengujian:
   * **Opsi A (Simulasi Cepat 1-Klik)**: Klik salah satu tombol preset:
     * *Presensi Rendah* (Kehadiran 60%, IPK 3.71) $\to$ Menguji Aturan 1.
     * *IPK Rendah* (Kehadiran 85%, IPK 2.70) $\to$ Menguji Aturan 2.
     * *Jalur Aman* (Kehadiran 94%, IPK 3.85) $\to$ Menguji Aturan 3.
   * **Opsi B (Input Manual)**: Masukkan NIM, Nama Mahasiswa, Kehadiran (%), IPK, dan Total SKS pada formulir yang disediakan, lalu klik **⚡ Hitung Prediksi C4.5**.
3. Di panel sebelah kanan, **Kartu Hasil Klasifikasi C4.5** akan langsung muncul:
   * Status kelulusan (Berisiko Terhambat / Berpotensi Tepat Waktu).
   * Aturan pohon keputusan yang terpicu.
   * Penjabaran analisis studi akademik.
   * Rekomendasi tindakan intervensi bagi Dosen PA.
4. Jika data uji coba tersebut ingin disimpan secara permanen ke dalam pangkalan data mahasiswa, klik tombol **💾 Simpan Hasil ke Pangkalan Data Mahasiswa**. Sistem akan menyimpannya ke database dan mengarahkan Anda kembali ke Dashboard.

---

### 5.5 Fitur Logika Rumus C4.5 & Evaluasi Alpha-Beta (`pages/rumus-c45/`)
Menu ini dirancang khusus untuk membedah transparansi akademis saat sidang skripsi:
1. Klik menu **Logika Rumus & Pohon C4.5** pada sidebar kiri.
2. Halaman ini memaparkan:
   * **Rumus Matematis**: Rumus Entropy dan Information Gain yang diformat rapi menggunakan notasi LaTeX MathJax.
   * **Tabel Perbandingan Gain Atribut**: Memperlihatkan mengapa Kehadiran (0,720) terpilih sebagai Simpul Akar, IPK (0,450) sebagai Simpul Cabang, dan SKS (0,006) tereliminasi.
   * **Visualisasi Pohon Keputusan**: Diagram hierarki simpul akar, cabang kondisi, dan daun keputusan.
   * **Evaluasi Model & Uji Statistik**: Tabel Matriks Konfusi 87 data uji, penjabaran rumus Error Alpha ($\alpha = 0{,}00\%$), Error Beta ($\beta = 12{,}82\%$), Power of Test ($87{,}18\%$), dan Akurasi ($94{,}25\%$).

---

### 5.6 Fitur Cetak Laporan Dokumen Ramah Kertas (`window.print()`)
1. Di halaman mana saja, klik tombol **🖨️ Cetak Laporan** pada navbar atas (atau tekan kombinasi tombol `Ctrl + P` di keyboard).
2. Sistem secara otomatis menerapkan mode cetak `@media print`:
   * Navigasi sidebar, tombol-tombol aksi, dan elemen kontrol peramban akan otomatis disembunyikan.
   * Tata letak dokumen diubah menjadi berlatar belakang putih bersih dengan format tabel formal yang siap dicetak ke kertas A4 atau disimpan sebagai file PDF untuk laporan sidang skripsi.

---

### 5.7 Fitur Reset Data Awal
1. Jika selama pengujian Anda menambahkan data uji coba atau mengedit data mahasiswa dan ingin mengembalikan pangkalan data ke kondisi asli file skripsi:
2. Klik tombol **🔄 Reset Data Awal** pada toolbar tabel Dashboard.
3. Muncul konfirmasi dialog peramban. Klik **OK**.
4. Sistem akan menyalin kembali file `students_default.json` ke `students.json`. Seluruh 438 data mahasiswa asli skripsi akan kembali pulih seperti sedia kala.

---

# BAGIAN 6: TANYA-JAWAB KUNCI (FAQ) UNTUK MENGHADAPI DOSEN PENGUJI SIDANG

Berikut adalah rangkuman pertanyaan teknis dan metodologis yang paling sering ditanyakan oleh dosen penguji skripsi beserta panduan jawaban ilmiah yang tepat:

### Q1: "Mengapa Anda memilih Algoritma Decision Tree C4.5, bukan algoritma lain seperti Naïve Bayes, KNN, atau SVM?"
* **Jawaban:**  
  *"Algoritma Decision Tree C4.5 dipilih karena sifatnya yang berupa **White-Box Model** (model transparan yang dapat diinterpretasikan secara jelas dalam bentuk aturan IF-THEN), berbeda dengan algoritma Black-Box seperti SVM atau Neural Network. Dalam konteks Early Warning System perguruan tinggi, Dosen Pembimbing Akademik (DPA) tidak hanya membutuhkan label prediksi lulus/terhambat, tetapi membutuhkan **alasan logis dan faktor pemicu** mengapa mahasiswa tersebut dikategorikan berisiko. Dengan C4.5, pohon keputusan dapat langsung diterjemahkan menjadi kebijakan intervensi bimbingan akademik yang konkret."*

---

### Q2: "Mengapa atribut Kehadiran terpilih sebagai Simpul Akar (Root Node) dan bukan IPK?"
* **Jawaban:**  
  *"Berdasarkan perhitungan Information Gain pada Bab 4 skripsi, atribut Kehadiran memiliki nilai **Gain tertinggi mutlak sebesar 0,720**, sedangkan IPK hanya 0,095 dan SKS 0,006. Secara empiris dan teoritis, kedisiplinan presensi merupakan **leading behavioral indicator** (indikator perilaku awal). Penurunan kehadiran mahasiswa kuliah terjadi lebih dahulu sebelum nilai ujian dan IPK-nya merosot. Oleh karena itu, kehadiran terbukti secara matematis dan substantif sebagai faktor pemilah paling krusial."*

---

### Q3: "Mengapa atribut Total SKS Lulus tidak muncul pada diagram pohon keputusan Anda?"
* **Jawaban:**  
  *"Karena pada perhitungan node awal, atribut Total SKS hanya memiliki Information Gain sebesar 0,006, dan pada cabang kedua hanya 0,028. Nilai gain yang sangat kecil ini menunjukkan bahwa Total SKS tidak memiliki daya pembeda yang signifikan. Hal ini terjadi karena pada semester awal hingga pertengahan, beban SKS yang diambil mahasiswa reguler cenderung seragam (paket perkuliahan sekitar 20 SKS per semester), sehingga distribusinya homogen di antara mahasiswa yang tepat waktu maupun berisiko."*

---

### Q4: "Berapa akurasi model Anda dan bagaimana interpretasi Error Alpha (α) serta Beta (β)-nya?"
* **Jawaban:**  
  *"Berdasarkan pengujian pada 87 data testing di Bab 4.4 skripsi:
  1. **Akurasi Global**: Mencapai **94,25%** (82 data benar diprediksi dari 87 data uji).
  2. **Error Alpha ($\alpha$ / Kesalahan Tipe I)**: Diperoleh **0,00%** (False Positive = 0). Artinya sistem **bebas dari alarm palsu** (*zero false alarm*), tidak pernah salah memvonis mahasiswa aman menjadi berisiko terhambat sehingga tidak menimbulkan kepanikan akademis bagi DPA.
  3. **Error Beta ($\beta$ / Kesalahan Tipe II)**: Sebesar **12,82%** (5 mahasiswa berisiko yang terlewat).
  4. **Daya Deteksi / Sensitivitas ($1 - \beta$)**: Mencapai **87,18%**, membuktikan model memiliki sensitivitas yang sangat baik dalam menangkap mahasiswa bermasalah sedini mungkin."*

---

### Q5: "Bagaimana arsitektur kodingan web ini dibangun dan bagaimana alur datanya?"
* **Jawaban:**  
  *"Aplikasi web ini dibangun dengan arsitektur **PHP Modular** yang memisahkan antara Presentation Layer (`components/` dan `pages/`), Business Logic Layer (`app/c45_engine.php`), dan Data Access Layer (`app/student_service.php` & `app/data/students.json`). Alur datanya adalah:
  1. Pengguna memasukkan parameter kehadiran dan IPK melalui antarmuka web.
  2. Data diproses oleh fungsi `c45_classify()` di `app/c45_engine.php` yang mengevaluasi ambang batas pohon keputusan (Kehadiran $\le 72{,}50\%$ dan IPK $\le 2{,}995$).
  3. Mesin klasifikasi mengembalikan status kelulusan, nomor aturan yang terpicu, analisis kondisi studi, dan rekomendasi bimbingan DPA.
  4. Hasilnya dirender ke antarmuka dan disimpan ke pangkalan data JSON melalui service backend secara permanen."*

---

### Q6: "Bagaimana sistem EWS ini memberikan manfaat nyata bagi prodi Teknik Informatika FTII UHAMKA?"
* **Jawaban:**  
  *"Sistem ini mengubah paradigma bimbingan akademik dari yang sebelumnya bersifat **reaktif** (baru bertindak saat mahasiswa sudah terancam DO di semester 7-8) menjadi **proaktif / preventif** (mendeteksi potensi masalah di semester 4-5). Dengan tombol Info (ℹ️) pada web, DPA langsung mendapatkan panduan diagnosis dan saran intervensi yang terarah, seperti pemanggilan bimbingan kehadiran minimal 75% atau penugasan tutor sebaya remedial untuk perbaikan IPK."*

---

*Catatan ini disusun khusus untuk Kharisma Rahmawati Lubis sebagai pegangan belajar mandiri dan persiapan komprehensif menghadapi Sidang Skripsi FTII UHAMKA 2026. Sukses selalu!*
