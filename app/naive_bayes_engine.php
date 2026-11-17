<?php
/**
 * ====================================================================================
 * APLIKASI   : Early Warning System (EWS) Prediksi Keberhasilan Studi Mahasiswa
 * METODE     : Algoritma Naive Bayes (Gaussian Naive Bayes)
 * SKRIPSI    : Penerapan Algoritma Decision Tree C4.5 & Naive Bayes Sebagai EWS 
 *              Prediksi Keberhasilan Studi Mahasiswa FTII UHAMKA
 * PENELITI   : Kharisma Rahmawati Lubis (NIM: 2203015072)
 * INSTITUSI  : Program Studi Teknik Informatika, FTII UHAMKA Jakarta (2026)
 * ====================================================================================
 * FILE       : app/naive_bayes_engine.php
 * FUNGSI     : Mesin Logika Algoritma Gaussian Naive Bayes:
 *              1. Perhitungan Probabilitas Prior (P(Kelas)).
 *              2. Fungsi Kepadatan Peluang Normal / Gaussian (Likelihood P(Fitur|Kelas)).
 *              3. Perhitungan Probabilitas Posterior P(Kelas|X) & Normalisasi Persentase.
 *              4. Rekomendasi Akademik Dosen Pembimbing Akademik berbasis Peluang.
 * ====================================================================================
 */

require_once __DIR__ . '/config.php';

/**
 * Parameter Distribusi Gaussian Naive Bayes berdasarkan Pangkalan Data Mahasiswa FTII UHAMKA
 */
define('NB_PRIOR_TEPAT', 0.5515);     // 241 / 437 mahasiswa
define('NB_PRIOR_BERISIKO', 0.4485);  // 196 / 437 mahasiswa

// Parameter Kehadiran (Mean & Variance)
define('NB_KEHADIRAN_TEPAT_MEAN', 86.5145);
define('NB_KEHADIRAN_TEPAT_VAR', 75.9258);
define('NB_KEHADIRAN_BERISIKO_MEAN', 58.9388);
define('NB_KEHADIRAN_BERISIKO_VAR', 203.4116);

// Parameter IPK (Mean & Variance)
define('NB_IPK_TEPAT_MEAN', 3.3672);
define('NB_IPK_TEPAT_VAR', 0.0294);
define('NB_IPK_BERISIKO_MEAN', 3.2623);
define('NB_IPK_BERISIKO_VAR', 0.0788);

/**
 * Menghitung Gaussian Probability Density Function (PDF)
 * f(x | mu, var) = (1 / sqrt(2 * pi * var)) * exp(- (x - mu)^2 / (2 * var))
 *
 * @param float $x Nilai atribut
 * @param float $mean Rata-rata populasi kelas
 * @param float $var Varian populasi kelas
 * @return float Nilai peluang densitas Gaussian
 */
function nb_gaussian_pdf($x, $mean, $var) {
    if ($var <= 0.000001) {
        $var = 0.0001; // Cegah pembagian dengan nol
    }
    $pi = 3.141592653589793;
    $exponent = - (pow($x - $mean, 2) / (2 * $var));
    $coefficient = 1 / sqrt(2 * $pi * $var);
    $pdf = $coefficient * exp($exponent);

    // Batasi nilai minimum agar tidak underflow ke nol absolut
    return max($pdf, 1e-12);
}

/**
 * Melakukan klasifikasi kelulusan menggunakan algoritma Gaussian Naive Bayes
 *
 * @param float $kehadiran Nilai presensi mahasiswa (%)
 * @param float $ipk       Indeks Prestasi Kumulatif (0 - 4.00)
 * @param int   $sks       Total SKS (opsional / tidak mempengaruhi)
 * @return array Hasil prediksi Naive Bayes, probabilitas posterior, dan analisis
 */
function naive_bayes_classify($kehadiran, $ipk, $sks = 0) {
    $kehadiran = (float)$kehadiran;
    $ipk = (float)$ipk;

    // 1. Likelihood Kehadiran
    $like_hadir_tepat    = nb_gaussian_pdf($kehadiran, NB_KEHADIRAN_TEPAT_MEAN, NB_KEHADIRAN_TEPAT_VAR);
    $like_hadir_berisiko = nb_gaussian_pdf($kehadiran, NB_KEHADIRAN_BERISIKO_MEAN, NB_KEHADIRAN_BERISIKO_VAR);

    // 2. Likelihood IPK
    $like_ipk_tepat    = nb_gaussian_pdf($ipk, NB_IPK_TEPAT_MEAN, NB_IPK_TEPAT_VAR);
    $like_ipk_berisiko = nb_gaussian_pdf($ipk, NB_IPK_BERISIKO_MEAN, NB_IPK_BERISIKO_VAR);

    // 3. Raw Posterior: Prior * P(Hadir|C) * P(IPK|C)
    $posterior_tepat_raw    = NB_PRIOR_TEPAT * $like_hadir_tepat * $like_ipk_tepat;
    $posterior_berisiko_raw = NB_PRIOR_BERISIKO * $like_hadir_berisiko * $like_ipk_berisiko;

    // 4. Normalisasi menjadi persentase peluang total 100%
    $total_evidence = $posterior_tepat_raw + $posterior_berisiko_raw;
    if ($total_evidence > 0) {
        $pct_tepat    = round(($posterior_tepat_raw / $total_evidence) * 100, 2);
        $pct_berisiko = round(($posterior_berisiko_raw / $total_evidence) * 100, 2);
    } else {
        $pct_tepat    = 50.0;
        $pct_berisiko = 50.0;
    }

    // 5. Keputusan Klasifikasi
    $is_at_risk = ($pct_berisiko >= $pct_tepat);
    $status = $is_at_risk ? 'Berisiko Terhambat' : 'Berpotensi Tepat Waktu';

    // 6. Alasan Akademik & Rekomendasi
    if ($is_at_risk) {
        $rule_title = "Peluang Berisiko: {$pct_berisiko}% vs Tepat Waktu: {$pct_tepat}%";
        $reason = "Berdasarkan teorema Naive Bayes, mahasiswa ini memiliki probabilitas posterior Berisiko Terhambat sebesar {$pct_berisiko}%. Hal ini dipicu oleh nilai kehadiran {$kehadiran}% dan IPK {$ipk} yang lebih condong mendekati sebaran parameter mahasiswa terhambat (rata-rata kehadiran berisiko 58,94%).";
        $recommendation = "Prioritaskan bimbingan intensif DPA, pantau presensi perkuliahan setiap 2 minggu, serta berikan pendampingan akademik mata kuliah sulit untuk menaikkan indeks performa.";
        $badge_class = 'bg-rose-100 text-rose-800 border-rose-300';
    } else {
        $rule_title = "Peluang Tepat Waktu: {$pct_tepat}% vs Berisiko: {$pct_berisiko}%";
        $reason = "Berdasarkan teorema Naive Bayes, mahasiswa ini memiliki probabilitas posterior kelulusan Tepat Waktu sebesar {$pct_tepat}%. Karakteristik kehadiran {$kehadiran}% dan IPK {$ipk} sangat bersesuaian dengan distribusi statistik kelompok mahasiswa sukses tepat waktu di FTII UHAMKA (rata-rata kehadiran 86,51% dan IPK 3,37).";
        $recommendation = "Pertahankan konsistensi kehadiran kuliah di atas 85% dan dorong keterlibatan dalam skripsi serta program magang/studi independen MBKM.";
        $badge_class = 'bg-emerald-100 text-emerald-800 border-emerald-300';
    }

    return [
        'status'         => $status,
        'algorithm'      => 'naive_bayes',
        'algorithm_name' => 'Gaussian Naive Bayes',
        'rule_number'    => $is_at_risk ? 2 : 1,
        'rule_title'     => $rule_title,
        'coverage'       => "Distribusi Posterior Peluang: {$pct_tepat}% Tepat vs {$pct_berisiko}% Berisiko",
        'is_at_risk'     => $is_at_risk,
        'probability'    => [
            'tepat_waktu' => $pct_tepat,
            'berisiko'    => $pct_berisiko
        ],
        'details' => [
            'like_hadir_tepat'    => $like_hadir_tepat,
            'like_hadir_berisiko' => $like_hadir_berisiko,
            'like_ipk_tepat'      => $like_ipk_tepat,
            'like_ipk_berisiko'   => $like_ipk_berisiko,
        ],
        'reason'         => $reason,
        'recommendation' => $recommendation,
        'badge_class'    => $badge_class
    ];
}
