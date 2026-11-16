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
 * FILE       : app/api_student.php
 * FUNGSI     : Endpoint REST/JSON API untuk operasi Edit, Hapus, Simpan, dan Reset data.
 * ====================================================================================
 */

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/c45_engine.php';
require_once __DIR__ . '/student_service.php';

$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'get':
        $nim = $_GET['nim'] ?? '';
        $std = student_find_by_nim($nim);
        if ($std) {
            echo json_encode(['success' => true, 'data' => $std]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Mahasiswa tidak ditemukan.']);
        }
        break;

    case 'update':
        $nim = $_POST['nim'] ?? '';
        $nama = $_POST['nama'] ?? '';
        $kehadiran = $_POST['kehadiran'] ?? '';
        $ipk = $_POST['ipk'] ?? '';
        $sks = $_POST['sks'] ?? '';

        if (empty($nim) || $kehadiran === '' || $ipk === '' || $sks === '') {
            echo json_encode(['success' => false, 'message' => 'Parameter tidak lengkap.']);
            exit;
        }

        $res = student_update($nim, [
            'nama' => $nama,
            'kehadiran' => $kehadiran,
            'ipk' => $ipk,
            'sks' => $sks
        ]);

        echo json_encode($res);
        break;

    case 'add':
        $nim = $_POST['nim'] ?? '';
        $nama = $_POST['nama'] ?? '';
        $kehadiran = $_POST['kehadiran'] ?? '';
        $ipk = $_POST['ipk'] ?? '';
        $sks = $_POST['sks'] ?? '';

        if ($kehadiran === '' || $ipk === '' || $sks === '') {
            echo json_encode(['success' => false, 'message' => 'Harap isi nilai kehadiran, IPK, dan SKS.']);
            exit;
        }

        $res = student_save([
            'nim' => $nim,
            'nama' => $nama,
            'kehadiran' => $kehadiran,
            'ipk' => $ipk,
            'sks' => $sks
        ]);

        echo json_encode($res);
        break;

    case 'delete':
        $nim = $_POST['nim'] ?? '';
        if (empty($nim)) {
            echo json_encode(['success' => false, 'message' => 'NIM tidak boleh kosong.']);
            exit;
        }

        $deleted = student_delete($nim);
        echo json_encode(['success' => $deleted]);
        break;

    case 'reset':
        $reset = student_reset();
        echo json_encode(['success' => $reset]);
        break;

    case 'classify':
        $kehadiran = (float)($_REQUEST['kehadiran'] ?? 0);
        $ipk = (float)($_REQUEST['ipk'] ?? 0);
        $sks = (int)($_REQUEST['sks'] ?? 0);

        $res = c45_classify($kehadiran, $ipk, $sks);
        echo json_encode(['success' => true, 'result' => $res]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Aksi tidak valid.']);
        break;
}

