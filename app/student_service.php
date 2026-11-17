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
 * FILE       : app/student_service.php
 * FUNGSI     : Layanan Pengelolaan Data Mahasiswa (CRUD, Persistence, & Multi-Algoritma):
 *              - Membaca data mahasiswa dari pangkalan data JSON skripsi.
 *              - Menyimpan data pengujian baru dari form simulasi.
 *              - Mengedit parameter mahasiswa & otomatis kalkulasi ulang C4.5 / Naive Bayes.
 *              - Menghapus data & mereset pangkalan data ke kondisi awal skripsi.
 * ====================================================================================
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/c45_engine.php';
require_once __DIR__ . '/naive_bayes_engine.php';
require_once __DIR__ . '/algorithm_service.php';

/**
 * Membaca seluruh data mahasiswa dari file database JSON.
 * Jika file belum ada, otomatis inisialisasi dari file default skripsi.
 * 
 * @return array Kumpulan data mahasiswa
 */
function student_get_all() {
    if (!file_exists(STUDENT_DATA_FILE)) {
        student_reset();
    }

    $json_content = @file_get_contents(STUDENT_DATA_FILE);
    if ($json_content === false) {
        return [];
    }

    $data = json_decode($json_content, true);
    return is_array($data) ? $data : [];
}

/**
 * Mencari data mahasiswa berdasarkan NIM beserta kalkulasi C4.5 dan Naive Bayes
 * 
 * @param string $nim Nomor Induk Mahasiswa
 * @return array|null Data mahasiswa lengkap atau null jika tidak ditemukan
 */
function student_find_by_nim($nim) {
    $students = student_get_all();
    $active_algo = get_active_algorithm();

    foreach ($students as $std) {
        if ($std['nim'] === $nim) {
            $c45_res = c45_classify($std['kehadiran'], $std['ipk'], $std['sks'] ?? 0);
            $nb_res  = naive_bayes_classify($std['kehadiran'], $std['ipk'], $std['sks'] ?? 0);
            $active_pred = ($active_algo === 'naive_bayes') ? $nb_res : $c45_res;

            return array_merge($std, [
                'c45'         => $c45_res,
                'naive_bayes' => $nb_res,
                'prediction'  => $active_pred,
                'active_algo' => $active_algo
            ]);
        }
    }
    return null;
}

/**
 * Memvalidasi apakah NIM berasal dari Program Studi Teknik Informatika FTII UHAMKA
 * Format baku UHAMKA: 10 digit, dengan kode fakultas FTII (03) dan prodi TI (015)
 * Contoh: 2203015072, 2203015001, 2303015010
 *
 * @param string $nim
 * @return array ['valid' => bool, 'message' => string, 'angkatan' => string|null]
 */
function student_validate_uhamka_nim($nim) {
    $nim = trim((string)$nim);

    if ($nim === '') {
        return [
            'valid' => false,
            'message' => 'NIM wajib diisi.'
        ];
    }

    // Cek apakah angka 10 digit
    if (!preg_match('/^\d{10}$/', $nim)) {
        return [
            'valid' => false,
            'message' => 'NIM [' . htmlspecialchars($nim) . '] tidak sesuai! NIM mahasiswa FTII UHAMKA wajib terdiri dari 10 digit angka.'
        ];
    }

    // Cek kodefikasi fakultas FTII (03) dan prodi:
    // 015 = S1 Teknik Informatika (Reguler)
    // 019 = S1 Teknik Informatika (Pindahan/Ekstensi)
    // 025 = S1 Teknik Elektro
    // 035 = S1 Teknik Mesin
    // 045 = S1 Sistem dan Teknologi Informasi
    if (!preg_match('/^(\d{2})03(015|019|025|035|045)(\d{3})$/', $nim, $matches)) {
        return [
            'valid' => false,
            'message' => 'Pencarian Error: NIM [' . htmlspecialchars($nim) . '] bukan mahasiswa FTII UHAMKA! Data dari kampus lain ditolak oleh sistem EWS.'
        ];
    }

    $angkatan = '20' . $matches[1];
    $prodi_map = [
        '015' => 'Teknik Informatika',
        '019' => 'Teknik Informatika (Pindahan)',
        '025' => 'Teknik Elektro',
        '035' => 'Teknik Mesin',
        '045' => 'Sistem dan Teknologi Informasi'
    ];
    $prodi_nama = $prodi_map[$matches[2]] ?? 'FTII UHAMKA';

    return [
        'valid' => true,
        'message' => 'NIM terverifikasi sebagai mahasiswa ' . $prodi_nama . ' (Angkatan ' . $angkatan . ').',
        'angkatan' => $angkatan,
        'prodi' => $prodi_nama
    ];
}

/**
 * Menyimpan data mahasiswa baru hasil simulasi ke dalam database JSON
 * 
 * @param array       $input Data formulir (nim, nama, prodi, kehadiran, ipk, sks)
 * @param string|null $algo  Algoritma yang digunakan (default: algoritma aktif)
 * @return array Hasil penyimpanan dan status klasifikasi
 */
function student_save($input, $algo = null) {
    $students = student_get_all();

    $nim = trim((string)($input['nim'] ?? ''));
    
    // Validasi ketat format NIM FTII UHAMKA
    $val = student_validate_uhamka_nim($nim);
    if (!$val['valid']) {
        return [
            'success' => false,
            'message' => $val['message']
        ];
    }

    $kehadiran = round((float)($input['kehadiran'] ?? 0), 1);
    $ipk = round((float)($input['ipk'] ?? 0), 2);
    $sks = (int)($input['sks'] ?? 100);

    // Hitung status menggunakan algoritma aktif
    $prediction = classify_student($kehadiran, $ipk, $sks, $algo);
    $c45_res    = c45_classify($kehadiran, $ipk, $sks);
    $nb_res     = naive_bayes_classify($kehadiran, $ipk, $sks);

    $new_student = [
        'nim'       => $nim,
        'nama'      => trim($input['nama'] ?? 'Mahasiswa Simulasi'),
        'prodi'     => trim($input['prodi'] ?? 'Teknik Informatika'),
        'kehadiran' => $kehadiran,
        'ipk'       => $ipk,
        'sks'       => $sks,
        'status'    => $prediction['status'],
        'rule'      => $prediction['rule_number']
    ];

    // Hapus data dengan NIM yang sama jika sudah ada sebelumnya agar tidak ganda
    $students = array_values(array_filter($students, function($s) use ($nim) {
        return trim((string)$s['nim']) !== $nim;
    }));

    // Sisipkan di posisi paling atas agar langsung terlihat oleh user
    array_unshift($students, $new_student);

    // Tulis kembali ke file JSON
    file_put_contents(STUDENT_DATA_FILE, json_encode($students, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    return [
        'success'     => true,
        'student'     => $new_student,
        'prediction'  => $prediction,
        'c45'         => $c45_res,
        'naive_bayes' => $nb_res
    ];
}

/**
 * Memperbarui (Edit) data mahasiswa yang sudah ada dan langsung
 * menghitung ulang status kelulusan berdasarkan algoritma aktif
 * 
 * @param string      $nim_target NIM mahasiswa yang akan diupdate
 * @param array       $input      Data pembaruan (nama, kehadiran, ipk, sks)
 * @param string|null $algo       Algoritma yang digunakan
 * @return array Status keberhasilan update
 */
function student_update($nim_target, $input, $algo = null) {
    $students = student_get_all();
    $found = false;
    $updated_record = null;

    foreach ($students as &$std) {
        if ($std['nim'] === $nim_target) {
            $kehadiran = round((float)($input['kehadiran'] ?? $std['kehadiran']), 1);
            $ipk = round((float)($input['ipk'] ?? $std['ipk']), 2);
            $sks = (int)($input['sks'] ?? $std['sks']);

            $prediction = classify_student($kehadiran, $ipk, $sks, $algo);

            $std['nama']      = trim($input['nama'] ?? $std['nama']);
            $std['kehadiran'] = $kehadiran;
            $std['ipk']       = $ipk;
            $std['sks']       = $sks;
            $std['status']    = $prediction['status'];
            $std['rule']      = $prediction['rule_number'];

            $updated_record = $std;
            $found = true;
            break;
        }
    }

    if ($found) {
        file_put_contents(STUDENT_DATA_FILE, json_encode($students, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return [
            'success' => true,
            'student' => $updated_record
        ];
    }

    return [
        'success' => false,
        'message' => 'Data mahasiswa dengan NIM tersebut tidak ditemukan.'
    ];
}

/**
 * Menghapus data mahasiswa berdasarkan NIM
 * 
 * @param string $nim
 * @return bool True jika data ditemukan dan dihapus
 */
function student_delete($nim) {
    $students = student_get_all();
    $initial_count = count($students);
    $target_nim = trim((string)$nim);

    $filtered = array_values(array_filter($students, function($s) use ($target_nim) {
        return trim((string)$s['nim']) !== $target_nim;
    }));

    if (count($filtered) !== $initial_count) {
        file_put_contents(STUDENT_DATA_FILE, json_encode($filtered, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return true;
    }
    return false;
}

/**
 * Mengembalikan pangkalan data ke kondisi awal file dataset skripsi
 * 
 * @return bool True jika berhasil direset
 */
function student_reset() {
    if (file_exists(STUDENT_DEFAULT_FILE)) {
        copy(STUDENT_DEFAULT_FILE, STUDENT_DATA_FILE);
        return true;
    }
    return false;
}

/**
 * Menghitung ulang seluruh status mahasiswa di database menggunakan algoritma tertentu
 *
 * @param string $algo 'c45' atau 'naive_bayes'
 * @return int Jumlah data yang diperbarui
 */
function student_reclassify_all($algo) {
    $students = student_get_all();
    $count = 0;

    foreach ($students as &$s) {
        $pred = classify_student($s['kehadiran'], $s['ipk'], $s['sks'] ?? 0, $algo);
        $s['status'] = $pred['status'];
        $s['rule'] = $pred['rule_number'];
        $count++;
    }

    file_put_contents(STUDENT_DATA_FILE, json_encode($students, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    return $count;
}

/**
 * Mengambil seluruh data mahasiswa dengan kalkulasi dinamis berdasarkan algoritma yang dipilih.
 * Menghasilkan status, nomor rule, probabilitas (jika Naive Bayes), serta kalkulasi dari kedua algoritma.
 * 
 * @param string|null $algo 'c45' atau 'naive_bayes' (default: algoritma aktif)
 * @return array
 */
function student_get_all_classified($algo = null) {
    if ($algo === null) {
        $algo = get_active_algorithm();
    }
    
    $students = student_get_all();
    $classified = [];
    
    foreach ($students as $s) {
        $kehadiran = (float)$s['kehadiran'];
        $ipk       = (float)$s['ipk'];
        $sks       = (int)($s['sks'] ?? 100);
        
        $c45_pred = c45_classify($kehadiran, $ipk, $sks);
        $nb_pred  = naive_bayes_classify($kehadiran, $ipk, $sks);
        
        $active_pred = ($algo === 'naive_bayes') ? $nb_pred : $c45_pred;
        
        $classified[] = array_merge($s, [
            'status'        => $active_pred['status'],
            'rule'          => $active_pred['rule_number'],
            'rule_title'    => $active_pred['rule_title'] ?? '',
            'active_algo'   => $algo,
            'prob_tepat'    => $nb_pred['probability']['tepat_waktu'],
            'prob_berisiko' => $nb_pred['probability']['berisiko'],
            'c45_status'    => $c45_pred['status'],
            'c45_rule'      => $c45_pred['rule_number'],
            'nb_status'     => $nb_pred['status'],
            'is_at_risk'    => $active_pred['is_at_risk']
        ]);
    }
    
    return $classified;
}

/**
 * Menghitung ringkasan KPI (Key Performance Indicator) data mahasiswa secara dinamis
 * sesuai algoritma yang sedang aktif (C4.5 vs Naive Bayes).
 * 
 * @param string|null $algo 'c45' atau 'naive_bayes' (default: algoritma aktif)
 * @return array Total, Jumlah Tepat Waktu, dan Jumlah Berisiko
 */
function student_get_kpis($algo = null) {
    if ($algo === null) {
        $algo = get_active_algorithm();
    }
    
    $students = student_get_all();
    $total = count($students);
    $tepat_waktu = 0;
    $berisiko = 0;

    foreach ($students as $s) {
        $kehadiran = (float)$s['kehadiran'];
        $ipk       = (float)$s['ipk'];
        $sks       = (int)($s['sks'] ?? 100);
        
        if ($algo === 'naive_bayes') {
            $pred = naive_bayes_classify($kehadiran, $ipk, $sks);
        } else {
            $pred = c45_classify($kehadiran, $ipk, $sks);
        }
        
        if (stripos($pred['status'], 'Tepat') !== false) {
            $tepat_waktu++;
        } else {
            $berisiko++;
        }
    }

    return [
        'total' => $total,
        'tepat_waktu' => $tepat_waktu,
        'berisiko' => $berisiko,
        'pct_tepat' => $total > 0 ? round(($tepat_waktu / $total) * 100, 1) : 0,
        'pct_berisiko' => $total > 0 ? round(($berisiko / $total) * 100, 1) : 0,
        'algo' => $algo
    ];
}
