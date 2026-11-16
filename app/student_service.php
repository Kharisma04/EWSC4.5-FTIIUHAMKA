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
 * FILE       : app/student_service.php
 * FUNGSI     : Layanan Pengelolaan Data Mahasiswa (CRUD, Persistence, & Integrasi C4.5):
 *              - Membaca data mahasiswa dari pangkalan data JSON skripsi.
 *              - Menyimpan data pengujian baru dari form uji coba.
 *              - Mengedit parameter mahasiswa & otomatis mengkalkulasi ulang C4.5.
 *              - Menghapus data & mereset pangkalan data ke kondisi awal skripsi.
 * ====================================================================================
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/c45_engine.php';

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
 * Mencari data mahasiswa berdasarkan NIM beserta detail analisis C4.5 lengkap
 * 
 * @param string $nim Nomor Induk Mahasiswa
 * @return array|null Data mahasiswa lengkap atau null jika tidak ditemukan
 */
function student_find_by_nim($nim) {
    $students = student_get_all();
    foreach ($students as $std) {
        if ($std['nim'] === $nim) {
            // Gabungkan dengan kalkulasi analisis C4.5
            $c45_res = c45_classify($std['kehadiran'], $std['ipk'], $std['sks']);
            return array_merge($std, ['c45' => $c45_res]);
        }
    }
    return null;
}

/**
 * Menyimpan data mahasiswa baru hasil uji coba ke dalam database JSON
 * 
 * @param array $input Data formulir (nim, nama, prodi, kehadiran, ipk, sks)
 * @return array Hasil penyimpanan dan status klasifikasi C4.5
 */
function student_save($input) {
    $students = student_get_all();

    $kehadiran = round((float)($input['kehadiran'] ?? 0), 1);
    $ipk = round((float)($input['ipk'] ?? 0), 2);
    $sks = (int)($input['sks'] ?? 0);

    // Hitung status menggunakan C4.5 Engine
    $c45_res = c45_classify($kehadiran, $ipk, $sks);

    $nim = trim((string)($input['nim'] ?? ''));
    if ($nim === '') {
        $nim = '2303015' . rand(100, 999);
    }

    $new_student = [
        'nim' => $nim,
        'nama' => trim($input['nama'] ?? 'Mahasiswa Uji Coba'),
        'prodi' => trim($input['prodi'] ?? 'Teknik Informatika'),
        'kehadiran' => $kehadiran,
        'ipk' => $ipk,
        'sks' => $sks,
        'status' => $c45_res['status'],
        'rule' => $c45_res['rule_number']
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
        'success' => true,
        'student' => $new_student,
        'c45' => $c45_res
    ];
}

/**
 * Memperbarui (Edit) data mahasiswa yang sudah ada dan langsung
 * menghitung ulang status kelulusan berdasarkan rumus Decision Tree C4.5
 * 
 * @param string $nim_target NIM mahasiswa yang akan diupdate
 * @param array  $input      Data pembaruan (nama, kehadiran, ipk, sks)
 * @return array Status keberhasilan update
 */
function student_update($nim_target, $input) {
    $students = student_get_all();
    $found = false;
    $updated_record = null;

    foreach ($students as &$std) {
        if ($std['nim'] === $nim_target) {
            $kehadiran = round((float)($input['kehadiran'] ?? $std['kehadiran']), 1);
            $ipk = round((float)($input['ipk'] ?? $std['ipk']), 2);
            $sks = (int)($input['sks'] ?? $std['sks']);

            // Kalkulasi ulang C4.5 dengan parameter yang baru diedit
            $c45_res = c45_classify($kehadiran, $ipk, $sks);

            $std['nama'] = trim($input['nama'] ?? $std['nama']);
            $std['kehadiran'] = $kehadiran;
            $std['ipk'] = $ipk;
            $std['sks'] = $sks;
            $std['status'] = $c45_res['status'];
            $std['rule'] = $c45_res['rule_number'];

            $updated_record = $std;
            $found = true;
            break;
        }
    }

    if ($found) {
        file_put_contents(STUDENT_DATA_FILE, json_encode($students, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return ['success' => true, 'student' => $updated_record];
    }

    return ['success' => false, 'message' => 'Data mahasiswa tidak ditemukan.'];
}

/**
 * Menghapus data mahasiswa dari pangkalan data berdasarkan NIM
 * 
 * @param string $nim NIM mahasiswa yang akan dihapus
 * @return bool True jika berhasil dihapus
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
 * Menghitung ringkasan KPI (Key Performance Indicator) data mahasiswa
 * 
 * @return array Total, Jumlah Tepat Waktu, dan Jumlah Berisiko
 */
function student_get_kpis() {
    $students = student_get_all();
    $total = count($students);
    $tepat_waktu = 0;
    $berisiko = 0;

    foreach ($students as $s) {
        if ($s['status'] === 'Berpotensi Tepat Waktu') {
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
        'pct_berisiko' => $total > 0 ? round(($berisiko / $total) * 100, 1) : 0
    ];
}
