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
 * FILE       : logout.php
 * FUNGSI     : Endpoint Pembersihan Sesi dan Keluar dari Sistem EWS
 * ====================================================================================
 */

require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/auth.php';

// Akhiri sesi pengguna
auth_logout();

// Alihkan kembali ke halaman login dengan parameter status logout
header("Location: login.php?logout=1");
exit;
