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
 * FILE       : app/api_student.php
 * FUNGSI     : Endpoint REST/JSON API untuk operasi CRUD, Impor CSV/Excel, dan Klasifikasi.
 * ====================================================================================
 */

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/c45_engine.php';
require_once __DIR__ . '/naive_bayes_engine.php';
require_once __DIR__ . '/algorithm_service.php';
require_once __DIR__ . '/student_service.php';
require_once __DIR__ . '/data_importer.php';

// Validasi Sesi Pengguna
if (!auth_check()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Sesi Anda telah berakhir atau belum login. Silakan login kembali.'
    ]);
    exit;
}

$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'verify_nim':
        $nim = trim($_REQUEST['nim'] ?? '');
        $val = student_validate_uhamka_nim($nim);
        if (!$val['valid']) {
            echo json_encode([
                'success' => false,
                'valid'   => false,
                'message' => $val['message']
            ]);
            exit;
        }

        $existing = student_find_by_nim($nim);
        echo json_encode([
            'success'   => true,
            'valid'     => true,
            'message'   => $val['message'],
            'in_db'     => ($existing !== null),
            'student'   => $existing
        ]);
        break;

    case 'list':
        $algo = $_REQUEST['algo'] ?? get_active_algorithm();
        $students = student_get_all_classified($algo);
        $kpis = student_get_kpis($algo);
        echo json_encode([
            'success'  => true,
            'algo'     => $algo,
            'kpis'     => $kpis,
            'students' => $students
        ]);
        break;

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
        $nim       = $_POST['nim'] ?? '';
        $nama      = $_POST['nama'] ?? '';
        $kehadiran = $_POST['kehadiran'] ?? '';
        $ipk       = $_POST['ipk'] ?? '';
        $sks       = $_POST['sks'] ?? 100;
        $algo      = $_POST['algo'] ?? get_active_algorithm();

        if (empty($nim) || $kehadiran === '' || $ipk === '') {
            echo json_encode(['success' => false, 'message' => 'Parameter tidak lengkap (NIM, kehadiran, dan IPK wajib diisi).']);
            exit;
        }

        $res = student_update($nim, [
            'nama'      => $nama,
            'kehadiran' => $kehadiran,
            'ipk'       => $ipk,
            'sks'       => $sks
        ], $algo);

        echo json_encode($res);
        break;

    case 'add':
        $nim       = $_POST['nim'] ?? '';
        $nama      = $_POST['nama'] ?? '';
        $kehadiran = $_POST['kehadiran'] ?? '';
        $ipk       = $_POST['ipk'] ?? '';
        $sks       = $_POST['sks'] ?? 100;
        $algo      = $_POST['algo'] ?? get_active_algorithm();

        if ($kehadiran === '' || $ipk === '') {
            echo json_encode(['success' => false, 'message' => 'Harap isi nilai kehadiran dan IPK.']);
            exit;
        }

        $res = student_save([
            'nim'       => $nim,
            'nama'      => $nama,
            'kehadiran' => $kehadiran,
            'ipk'       => $ipk,
            'sks'       => $sks
        ], $algo);

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
        $ipk       = (float)($_REQUEST['ipk'] ?? 0);
        $sks       = (int)($_REQUEST['sks'] ?? 100);
        $algo      = $_REQUEST['algo'] ?? get_active_algorithm();

        $c45_res   = c45_classify($kehadiran, $ipk, $sks);
        $nb_res    = naive_bayes_classify($kehadiran, $ipk, $sks);
        $res       = ($algo === 'naive_bayes') ? $nb_res : $c45_res;

        echo json_encode([
            'success'     => true,
            'active_algo' => $algo,
            'result'      => $res,
            'c45'         => $c45_res,
            'naive_bayes' => $nb_res
        ]);
        break;

    case 'switch_algo':
        $new_algo = trim(strtolower($_POST['algo'] ?? $_GET['algo'] ?? ''));
        if (in_array($new_algo, ['c45', 'naive_bayes'])) {
            set_active_algorithm($new_algo);
            $reclassified = 0;
            if (isset($_REQUEST['reclassify']) && $_REQUEST['reclassify'] == '1') {
                $reclassified = student_reclassify_all($new_algo);
            }
            $meta = get_algorithm_metadata($new_algo);
            $kpis = student_get_kpis($new_algo);

            echo json_encode([
                'success'      => true,
                'active_algo'  => $new_algo,
                'metadata'     => $meta,
                'kpis'         => $kpis,
                'reclassified' => $reclassified
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Algoritma tidak dikenali. Pilih c45 atau naive_bayes.']);
        }
        break;

    case 'import':
        if (!isset($_FILES['file_data'])) {
            echo json_encode(['success' => false, 'message' => 'Berkas data belum dipilih.']);
            exit;
        }

        $mode = $_POST['mode'] ?? 'merge';
        $res = importer_process_file($_FILES['file_data'], $mode);
        echo json_encode($res);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Aksi tidak valid.']);
        break;
}
