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
 * FILE       : app/config.php
 * FUNGSI     : Konfigurasi global sistem, metadata akademik, dan helper path aplikasi.
 * ====================================================================================
 */

// Mengatur zona waktu lokal
date_default_timezone_set('Asia/Jakarta');

// Metadata Skripsi & Aplikasi
define('APP_NAME', 'EWS C4.5 FTII UHAMKA');
define('APP_VERSION', '2.0-Modular');
define('APP_YEAR', '2026');

define('RESEARCHER_NAME', 'Kharisma Rahmawati Lubis');
define('RESEARCHER_NIM', '2203015072');
define('PRODI_NAME', 'Teknik Informatika');
define('FAKULTAS_NAME', 'Fakultas Teknologi Industri dan Informatika (FTII)');
define('CAMPUS_NAME', 'Universitas Muhammadiyah Prof. DR. HAMKA (UHAMKA)');

// Ambang Batas Pohon Keputusan C4.5 (Berdasarkan Hasil Riset Bab 4 Skripsi)
define('C45_THRESHOLD_KEHADIRAN', 72.50); // Simpul Akar (Root Node) Kehadiran <= 72.50%
define('C45_THRESHOLD_IPK', 2.995);       // Simpul Cabang (Internal Node) IPK <= 2.995

// Path file data JSON
define('STUDENT_DATA_FILE', __DIR__ . '/data/students.json');
define('STUDENT_DEFAULT_FILE', __DIR__ . '/data/students_default.json');

/**
 * Helper untuk menentukan path relatif root aplikasi agar CSS, JS, dan link 
 * selalu tepat, baik saat diakses dari root (index.php) maupun subfolder (pages/...)
 * 
 * @param string $current_dir Direktori file pemanggil (__DIR__)
 * @return string Prefix path kembali ke root (misal: './' atau '../../')
 */
function get_root_path($current_dir) {
    $app_root = str_replace('\\', '/', realpath(__DIR__ . '/..'));
    $curr = str_replace('\\', '/', realpath($current_dir));

    if ($curr === $app_root) {
        return './';
    }

    $rel = substr($curr, strlen($app_root) + 1);
    $depth = count(explode('/', $rel));
    return str_repeat('../', $depth);
}
