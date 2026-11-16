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
 * FILE       : app/c45_engine.php
 * FUNGSI     : Mesin Logika Inti Perhitungan Rumus Algoritma Decision Tree C4.5:
 *              1. Perhitungan matematis Entropy & Information Gain.
 *              2. Pohon Keputusan (Root Node Kehadiran 72.50% & Internal Node IPK 2.995).
 *              3. Penjabaran alasan akademik mendalam & rekomendasi intervensi DPA.
 *              4. Evaluasi model: Matriks Konfusi, Error Alpha (α) & Beta (β).
 * ====================================================================================
 */

require_once __DIR__ . '/config.php';

/**
 * ------------------------------------------------------------------------------------
 * RUMUS 1: ENTROPY
 * Entropy(S) = - (p_positif * log2(p_positif)) - (p_negatif * log2(p_negatif))
 * Digunakan untuk mengukur heterogenitas / ketidakpastian data kelas sasaran.
 * ------------------------------------------------------------------------------------
 * @param int|float $c1 Jumlah sampel kelas 1 (misal: Berpotensi Tepat Waktu)
 * @param int|float $c2 Jumlah sampel kelas 2 (misal: Berisiko Terhambat)
 * @return float Nilai entropy dalam rentang 0.0 s/d 1.0
 */
function c45_calculate_entropy($c1, $c2) {
    $total = $c1 + $c2;
    if ($total == 0 || $c1 == 0 || $c2 == 0) {
        // Jika data homogen (hanya terdiri dari 1 kelas), entropy = 0 (kemurnian sempurna)
        return 0.0;
    }

    $p1 = $c1 / $total;
    $p2 = $c2 / $total;

    // Menghitung -p * log2(p) menggunakan basis logaritma 2
    $entropy = - ($p1 * log($p1, 2)) - ($p2 * log($p2, 2));
    return round($entropy, 4);
}

/**
 * ------------------------------------------------------------------------------------
 * RUMUS 2: INFORMATION GAIN
 * Gain(S, A) = Entropy(S) - SUM( (|S_i| / |S|) * Entropy(S_i) )
 * Digunakan untuk mengukur reduksi ketidakpastian saat data dipartisi oleh atribut A.
 * ------------------------------------------------------------------------------------
 * @param float $entropy_total Nilai entropy himpunan sebelum dipartisi
 * @param array $subsets Array berisi proporsi dan entropy tiap cabang partisi
 * @return float Nilai Information Gain
 */
function c45_calculate_gain($entropy_total, $subsets) {
    $split_entropy = 0.0;
    foreach ($subsets as $subset) {
        // bobot * entropy subset
        $split_entropy += ($subset['weight'] * $subset['entropy']);
    }
    return round($entropy_total - $split_entropy, 4);
}

/**
 * ------------------------------------------------------------------------------------
 * KLASIFIKASI KELULUSAN MAHASISWA BERBASIS POHON KEPUTUSAN C4.5
 * ------------------------------------------------------------------------------------
 * Pohon Keputusan Terbentuk Berdasarkan Riset Skripsi (Bab 4):
 * - Simpul Akar (Root Node)   : Kehadiran (Gain = 0.720, tertinggi di antara semua atribut)
 *   * Cabang 1: Kehadiran <= 72.50%  --> Daun: Berisiko Terhambat (Aturan 1)
 *   * Cabang 2: Kehadiran > 72.50%   --> Menuju Simpul Internal (Node 2: Rata-rata IPK)
 * - Simpul Cabang (Internal)  : Rata-rata IPK (Gain = 0.450 pada cabang hadir > 72.50%)
 *   * Cabang 2A: IPK <= 2.995        --> Daun: Berisiko Terhambat (Aturan 2)
 *   * Cabang 2B: IPK > 2.995         --> Daun: Berpotensi Tepat Waktu (Aturan 3)
 * - Atribut Total SKS         : Tidak masuk pohon karena Gain sangat kecil (0.006).
 * ------------------------------------------------------------------------------------
 * @param float $kehadiran Persentase presensi mahasiswa (0 - 100%)
 * @param float $ipk       Indeks Prestasi Kumulatif (0.00 - 4.00)
 * @param int   $sks       Total SKS lulus (sebagai parameter pemantauan pendukung)
 * @return array Hasil klasifikasi, aturan, analisis terperinci, dan saran intervensi DPA
 */
function c45_classify($kehadiran, $ipk, $sks = 0) {
    $kehadiran = (float)$kehadiran;
    $ipk = (float)$ipk;
    $sks = (int)$sks;

    // ATURAN 1: Cabang Simpul Akar (Presensi Kurang)
    // Berdasarkan Bab 4 Skripsi: Jika kehadiran <= 72.50%, data menghasilkan 138 Berisiko dan 0 Tepat Waktu
    if ($kehadiran <= C45_THRESHOLD_KEHADIRAN) {
        return [
            'status' => 'Berisiko Terhambat',
            'rule_number' => 1,
            'rule_title' => 'Aturan 1 (Root Node): Kehadiran ≤ 72,50%',
            'coverage' => 'Indikator Kritis: Kedisiplinan Presensi Kuliah',
            'is_at_risk' => true,
            'reason' => 'Mahasiswa ini berstatus Berisiko Terhambat karena persentase kehadiran kuliah hanya ' . number_format($kehadiran, 1, ',', '.') . '% (di bawah batas ambang 72,50%). Dalam pohon keputusan C4.5, kehadiran merupakan indikator penentu utama yang membuktikan penurunan presensi merupakan sinyal dini sebelum nilai akademik merosot.',
            'recommendation' => 'Panggilan konseling wajib oleh Dosen Pembimbing Akademik (DPA), peringatan batas syarat kehadiran ujian minimal 75%, serta investigasi kendala personal/sakit/pekerjaan yang dihadapi mahasiswa.',
            'badge_class' => 'bg-rose-100 text-rose-800 border-rose-300',
            'border_class' => 'border-rose-300',
            'bg_light' => 'bg-rose-50/80',
            'icon' => '🚨'
        ];
    }
    
    // ATURAN 2: Cabang Simpul Kedua (Presensi Aman, Namun Nilai IPK di Bawah Standar)
    // Berdasarkan Bab 4 Skripsi: Jika hadir > 72.50% & IPK <= 2.995, menghasilkan 25 Berisiko dan 0 Tepat Waktu
    elseif ($ipk <= C45_THRESHOLD_IPK) {
        return [
            'status' => 'Berisiko Terhambat',
            'rule_number' => 2,
            'rule_title' => 'Aturan 2 (Internal Node): Kehadiran > 72,50% & IPK ≤ 2,995',
            'coverage' => 'Indikator Kritis: Capaian Nilai Akademik (IPK)',
            'is_at_risk' => true,
            'reason' => 'Mahasiswa ini berstatus Berisiko Terhambat karena meskipun kedisiplinan hadir sudah baik (' . number_format($kehadiran, 1, ',', '.') . '%), rata-rata IPK yang diperoleh (' . number_format($ipk, 2, ',', '.') . ') belum mencapai batas aman standar kelulusan tepat waktu (2,995).',
            'recommendation' => 'Rekomendasikan bimbingan belajar remedial, pendampingan kelompok belajar (tutor sebaya) pada mata kuliah berbobot SKS besar atau berprasyarat, dan evaluasi strategi belajar mahasiswa oleh DPA.',
            'badge_class' => 'bg-amber-100 text-amber-900 border-amber-300',
            'border_class' => 'border-amber-300',
            'bg_light' => 'bg-amber-50/80',
            'icon' => '⚠️'
        ];
    }
    
    // ATURAN 3: Cabang Jalur Aman (Memenuhi Seluruh Standar Kelulusan Tepat Waktu)
    // Berdasarkan Bab 4 Skripsi: Jika hadir > 72.50% & IPK > 2.995, menghasilkan 241 Tepat Waktu dan 0 Berisiko
    else {
        return [
            'status' => 'Berpotensi Tepat Waktu',
            'rule_number' => 3,
            'rule_title' => 'Aturan 3 (Jalur Aman): Kehadiran > 72,50% & IPK > 2,995',
            'coverage' => 'Jalur Hijau: Memenuhi Standar Studi Ideal',
            'is_at_risk' => false,
            'reason' => 'Mahasiswa ini berstatus Berpotensi Tepat Waktu karena menunjukkan kedisiplinan kehadiran yang prima (' . number_format($kehadiran, 1, ',', '.') . '%) serta pencapaian IPK (' . number_format($ipk, 2, ',', '.') . ') yang telah melampaui batas ambang aman kelulusan 4 tahun.',
            'recommendation' => 'Pertahankan performa studi yang sudah baik, dorong mahasiswa untuk mulai merumuskan topik proposal skripsi lebih awal, dan fasilitasi keikutsertaan program MBKM / magang industri bersertifikat.',
            'badge_class' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'border_class' => 'border-emerald-300',
            'bg_light' => 'bg-emerald-50/80',
            'icon' => '✅'
        ];
    }
}

/**
 * ------------------------------------------------------------------------------------
 * EVALUASI MODEL & PERHITUNGAN ERROR ALPHA (α) & BETA (β)
 * ------------------------------------------------------------------------------------
 * Berdasarkan hasil pengujian 87 data testing skripsi menggunakan RapidMiner &
 * landasan teori metrik evaluasi machine learning (Rainio, 2024 / Bab 4.4 Skripsi):
 *
 * Perspektif Deteksi Early Warning System (Kelas Positif = 'Berisiko Terhambat'):
 * - True Positive  (TP) = 34 (Mahasiswa berisiko terdeteksi benar oleh sistem)
 * - False Negative (FN) = 5  (Mahasiswa berisiko terlewat / salah diprediksi tepat waktu)
 * - False Positive (FP) = 0  (Mahasiswa tepat waktu salah divonis berisiko -> ALARM PALSU)
 * - True Negative  (TN) = 48 (Mahasiswa tepat waktu diprediksi benar oleh sistem)
 *
 * 1. Error Alpha (α, Type I Error / False Alarm Rate):
 *    α = FP / (FP + TN) = 0 / (0 + 48) = 0.00%
 *    Artinya: Sistem EWS memiliki tingkat alarm palsu 0% (tidak pernah membebani DPA).
 *
 * 2. Error Beta (β, Type II Error / Missed Detection Rate):
 *    β = FN / (TP + FN) = 5 / (34 + 5) = 5 / 39 = 12.82%
 *    Artinya: Dari seluruh mahasiswa yang bermasalah, hanya 12.82% yang luput dari deteksi.
 *
 * 3. Daya Uji Deteksi / Power of Test (Sensitivitas = 1 - β):
 *    Power = 1 - β = 100% - 12.82% = 87.18%
 *    Artinya: Sensitivitas sistem dalam menjaring mahasiswa berisiko mencapai 87.18%.
 *
 * 4. Akurasi Global Model:
 *    Accuracy = (TP + TN) / Total = (34 + 48) / 87 = 82 / 87 = 94.25%
 * ------------------------------------------------------------------------------------
 * @return array Rincian metrik konfusi matriks, akurasi, alpha, beta, dan penjelasan ilmiah
 */
function c45_get_evaluation_metrics() {
    $total_testing = 87;

    // Perspektif 1: Fokus Deteksi Risiko (EWS Risk-Centric)
    $risk_tp = 34;
    $risk_fn = 5;
    $risk_fp = 0;
    $risk_tn = 48;

    $accuracy = round((($risk_tp + $risk_tn) / $total_testing) * 100, 2); // 94.25%
    
    // Perhitungan Alpha (Tipe I)
    $alpha = round(($risk_fp / ($risk_fp + $risk_tn)) * 100, 2);        // 0.00%
    $confidence = round(100.0 - $alpha, 2);                             // 100.00%

    // Perhitungan Beta (Tipe II)
    $beta = round(($risk_fn / ($risk_tp + $risk_fn)) * 100, 2);         // 12.82%
    $power_of_test = round(100.0 - $beta, 2);                           // 87.18% (Sensitivity/Recall Risiko)

    $risk_precision = round(($risk_tp / ($risk_tp + $risk_fp)) * 100, 2); // 100.00%
    $risk_f1 = round((2 * ($risk_precision * $power_of_test)) / ($risk_precision + $power_of_test), 2); // 93.15%

    // Perspektif 2: Standar RapidMiner Bab 4.4 Skripsi (Kelas Positif = 'Berpotensi Tepat Waktu')
    $std_tp = 48;
    $std_tn = 34;
    $std_fp = 5;
    $std_fn = 0;
    $std_precision = 90.57; // 48 / (48 + 5)
    $std_recall = 100.00;   // 48 / (48 + 0)
    $std_f1 = 95.05;

    return [
        'total_testing' => $total_testing,
        'accuracy' => $accuracy,
        // Error Alpha & Beta (Evaluasi EWS Deteksi Risiko)
        'alpha_error' => $alpha,
        'beta_error' => $beta,
        'power_of_test' => $power_of_test,
        'confidence_level' => $confidence,
        'risk_precision' => $risk_precision,
        'risk_f1_score' => $risk_f1,
        'confusion_matrix' => [
            'tp' => $risk_tp,
            'fn' => $risk_fn,
            'fp' => $risk_fp,
            'tn' => $risk_tn
        ],
        // Standar RapidMiner Bab 4 Skripsi
        'skripsi_eval' => [
            'tp' => $std_tp,
            'tn' => $std_tn,
            'fp' => $std_fp,
            'fn' => $std_fn,
            'precision' => $std_precision,
            'recall' => $std_recall,
            'f1_score' => $std_f1
        ],
        'gain_comparison' => [
            ['atribut' => 'Kehadiran (%)', 'gain' => 0.720, 'status' => 'Simpul Akar (Root Node)', 'terpilih' => true],
            ['atribut' => 'Rata-rata IPK', 'gain' => 0.095, 'status' => 'Simpul Cabang (Internal Node 2, Gain=0.450)', 'terpilih' => true],
            ['atribut' => 'Total SKS Lulus', 'gain' => 0.006, 'status' => 'Tereliminasi (Daya pembeda homogen)', 'terpilih' => false],
        ]
    ];
}
