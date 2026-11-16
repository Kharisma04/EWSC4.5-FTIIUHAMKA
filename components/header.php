<?php
/**
 * ====================================================================================
 * APLIKASI   : Early Warning System (EWS) Prediksi Keberhasilan Studi Mahasiswa
 * METODE     : Algoritma Decision Tree model prediksi
 * SKRIPSI    : Penerapan Algoritma Decision Tree model prediksi Sebagai Early Warning System 
 *              Prediksi Keberhasilan Studi Mahasiswa FTII UHAMKA
 * PENELITI   : Kharisma Rahmawati Lubis (NIM: 2203015072)
 * INSTITUSI  : Program Studi Teknik Informatika, FTII UHAMKA Jakarta (2026)
 * ====================================================================================
 * FILE       : components/header.php
 * FUNGSI     : Komponen Header HTML modular (Head, Meta, Font, CDN CSS & JS).
 * ====================================================================================
 */

// Pastikan konfigurasi global sudah dimuat
require_once __DIR__ . '/../app/config.php';

// Menentukan prefix root path jika belum didefinisikan di halaman pemanggil
if (!isset($root_path)) {
    $root_path = './';
}

$page_title = isset($page_title) ? $page_title . ' - ' . APP_NAME : APP_NAME . ' - Early Warning System';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title); ?></title>
    
    <!-- Meta Tag Akademik & Peneliti -->
    <meta name="author" content="<?= RESEARCHER_NAME; ?> (NIM: <?= RESEARCHER_NIM; ?>)">
    <meta name="description" content="Early Warning System Prediksi Keberhasilan Studi Mahasiswa FTII UHAMKA berbasis Algoritma Decision Tree model prediksi">
    
    <!-- Favicon Resmi UHAMKA -->
    <link rel="icon" type="image/webp" href="<?= $root_path; ?>assets/images/uhamka-logo-emblem.webp">
    <link rel="apple-touch-icon" href="<?= $root_path; ?>assets/images/uhamka-logo-emblem.webp">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- Stylesheet Kustom EWS -->
    <link rel="stylesheet" href="<?= $root_path; ?>assets/css/styles.css">
</head>
<body class="bg-[#f8fafc] text-slate-900 antialiased min-h-screen flex">

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div id="sidebarBackdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden md:hidden transition-opacity"></div>

