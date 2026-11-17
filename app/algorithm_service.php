<?php
/**
 * ====================================================================================
 * APLIKASI   : Early Warning System (EWS) Prediksi Keberhasilan Studi Mahasiswa
 * METODE     : Algoritma Decision Tree C4.5 & Gaussian Naive Bayes
 * SKRIPSI    : Penerapan Algoritma Decision Tree C4.5 Sebagai Early Warning System 
 *              Prediksi Keberhasilan Studi Mahasiswa FTII UHAMKA
 * PENELITI   : Kharisma Rahmawati Lubis (NIM: 2203015072)
 * INSTITUSI  : Program Studi Teknik Informatika, FTII UHAMKA Jakarta (2026)
 * ====================================================================================
 * FILE       : app/algorithm_service.php
 * FUNGSI     : Layanan Pemilihan Algoritma Dinamis (C4.5 vs Naive Bayes)
 * ====================================================================================
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/c45_engine.php';
require_once __DIR__ . '/naive_bayes_engine.php';

/**
 * Mengambil algoritma yang sedang aktif dipilih pengguna
 *
 * @return string 'c45' atau 'naive_bayes'
 */
function get_active_algorithm() {
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }

    // Jika ada pergantian via URL parameter ?algo=...
    if (isset($_GET['algo'])) {
        $requested = strtolower(trim($_GET['algo']));
        if (in_array($requested, ['c45', 'naive_bayes', 'nb'])) {
            $algo = ($requested === 'nb') ? 'naive_bayes' : $requested;
            $_SESSION['active_algorithm'] = $algo;
            return $algo;
        }
    }

    // Jika ada pergantian via POST
    if (isset($_POST['algo'])) {
        $requested = strtolower(trim($_POST['algo']));
        if (in_array($requested, ['c45', 'naive_bayes', 'nb'])) {
            $algo = ($requested === 'nb') ? 'naive_bayes' : $requested;
            $_SESSION['active_algorithm'] = $algo;
            return $algo;
        }
    }

    return $_SESSION['active_algorithm'] ?? 'c45';
}

/**
 * Mengatur algoritma aktif
 *
 * @param string $algo 'c45' atau 'naive_bayes'
 * @return void
 */
function set_active_algorithm($algo) {
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }
    if (in_array($algo, ['c45', 'naive_bayes'])) {
        $_SESSION['active_algorithm'] = $algo;
    }
}

/**
 * Mengambil metadata deskriptif tentang algoritma yang dipilih
 *
 * @param string|null $algo
 * @return array
 */
function get_algorithm_metadata($algo = null) {
    if ($algo === null) {
        $algo = get_active_algorithm();
    }

    if ($algo === 'naive_bayes') {
        return [
            'id'          => 'naive_bayes',
            'name'        => 'Naive Bayes (Gaussian)',
            'short_name'  => 'Naive Bayes',
            'type'        => 'Probabilistik',
            'badge'       => 'Gaussian Naive Bayes',
            'accuracy'    => '82,76%',
            'description' => 'Klasifikasi berbasis teorema probabilitas bersyarat Bayes dengan asumsi independensi fitur dan kurva sebaran normal (Gaussian). Akurasi pengujian RapidMiner: 82,76%.',
            'color'       => 'violet'
        ];
    }

    // Default: Decision Tree C4.5
    return [
        'id'          => 'c45',
        'name'        => 'Decision Tree C4.5',
        'short_name'  => 'C4.5',
        'type'        => 'Pohon Keputusan (Rule-Based)',
        'badge'       => 'Decision Tree C4.5',
        'accuracy'    => '94,25%',
        'description' => 'Klasifikasi berbasis pemisahan fitur dengan Information Gain tertinggi (Root Node Kehadiran 72.50% & Internal Node IPK 2.995).',
        'color'       => 'blue'
    ];
}

/**
 * Melakukan klasifikasi mahasiswa dengan algoritma yang sedang aktif
 *
 * @param float       $kehadiran
 * @param float       $ipk
 * @param int         $sks
 * @param string|null $algo
 * @return array
 */
function classify_student($kehadiran, $ipk, $sks = 0, $algo = null) {
    if ($algo === null) {
        $algo = get_active_algorithm();
    }

    if ($algo === 'naive_bayes') {
        return naive_bayes_classify($kehadiran, $ipk, $sks);
    }

    return c45_classify($kehadiran, $ipk, $sks);
}
