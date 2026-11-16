<?php
/**
 * FILE       : components/header.php
 * FUNGSI     : HTML Head, Meta, CDN.
 */

require_once __DIR__ . '/../app/config.php';

if (!isset($root_path)) { $root_path = './'; }

$page_title = isset($page_title)
    ? htmlspecialchars($page_title) . ' — EWS FTII UHAMKA'
    : 'Early Warning System — FTII UHAMKA';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title; ?></title>
    <meta name="description" content="Sistem Peringatan Dini (EWS) Prediksi Keberhasilan Studi Mahasiswa FTII UHAMKA.">
    <meta name="author" content="<?= RESEARCHER_NAME; ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/webp" href="<?= $root_path; ?>assets/images/uhamka-logo-emblem.webp">
    <link rel="apple-touch-icon" href="<?= $root_path; ?>assets/images/uhamka-logo-emblem.webp">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $root_path; ?>assets/css/styles.css">
</head>
<body class="bg-[#f1f5f9] text-slate-900 antialiased min-h-screen flex">

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebarBackdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 hidden md:hidden"></div>
