<?php
/**
 * ====================================================================================
 * APLIKASI   : Early Warning System (EWS) Prediksi Keberhasilan Studi Mahasiswa
 * METODE     : Algoritma Decision Tree C4.5 & Naive Bayes
 * SKRIPSI    : Penerapan Algoritma Decision Tree C4.5 Sebagai Early Warning System 
 *              Prediksi Keberhasilan Studi Mahasiswa FTII UHAMKA
 * PENELITI   : Kharisma Rahmawati Lubis (NIM: 2203015072)
 * INSTITUSI  : Program Studi Teknik Informatika, FTII UHAMKA Jakarta (2026)
 * ====================================================================================
 * FILE       : app/data_importer.php
 * FUNGSI     : Layanan Impor Data Mahasiswa dari Berkas CSV dan Excel (.xlsx)
 * ====================================================================================
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/student_service.php';

/**
 * Mendeteksi delimiter CSV (koma, titik koma, atau tab)
 *
 * @param string $filepath Path file CSV
 * @return string
 */
function importer_detect_csv_delimiter($filepath) {
    $delimiters = [',', ';', "\t", '|'];
    $handle = @fopen($filepath, 'r');
    if (!$handle) return ',';

    $first_line = fgets($handle);
    fclose($handle);

    if (!$first_line) return ',';

    $best_delimiter = ',';
    $max_count = 0;

    foreach ($delimiters as $delim) {
        $count = count(str_getcsv($first_line, $delim));
        if ($count > $max_count) {
            $max_count = $count;
            $best_delimiter = $delim;
        }
    }

    return $best_delimiter;
}

/**
 * Membaca data dari file CSV menjadi array 2D
 *
 * @param string $filepath
 * @return array
 */
function importer_read_csv($filepath) {
    $delimiter = importer_detect_csv_delimiter($filepath);
    $rows = [];

    if (($handle = @fopen($filepath, 'r')) !== false) {
        // Hapus BOM UTF-8 jika ada
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        while (($data = fgetcsv($handle, 4096, $delimiter)) !== false) {
            // Bersihkan spasi tiap sel
            $cleaned = array_map(function($v) {
                return trim((string)$v);
            }, $data);

            // Lewati baris kosong
            if (count(array_filter($cleaned)) > 0) {
                $rows[] = $cleaned;
            }
        }
        fclose($handle);
    }

    return $rows;
}

/**
 * Membaca data dari file Excel (.xlsx) murni menggunakan ZipArchive & SimpleXML
 * Tanpa ketergantungan library eksternal (100% portabel di Laragon / XAMPP).
 *
 * @param string $filepath Path berkas .xlsx
 * @return array
 */
function importer_read_xlsx($filepath) {
    if (!class_exists('ZipArchive')) {
        return [];
    }

    $zip = new ZipArchive();
    if ($zip->open($filepath) !== true) {
        return [];
    }

    // 1. Baca shared strings (kumpulan teks Excel)
    $shared_strings = [];
    $shared_strings_xml = $zip->getFromName('xl/sharedStrings.xml');
    if ($shared_strings_xml) {
        $xml = @simplexml_load_string($shared_strings_xml);
        if ($xml && isset($xml->si)) {
            foreach ($xml->si as $si) {
                if (isset($si->t)) {
                    $shared_strings[] = (string)$si->t;
                } elseif (isset($si->r)) {
                    $text = '';
                    foreach ($si->r as $r) {
                        $text .= (string)$r->t;
                    }
                    $shared_strings[] = $text;
                } else {
                    $shared_strings[] = '';
                }
            }
        }
    }

    // 2. Baca sheet1.xml (lembar kerja utama)
    $sheet_xml = $zip->getFromName('xl/worksheets/sheet1.xml');
    if (!$sheet_xml) {
        // Coba cari nama sheet pertama dari manifest
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (preg_match('#xl/worksheets/sheet\d+\.xml#', $name)) {
                $sheet_xml = $zip->getFromName($name);
                break;
            }
        }
    }

    $zip->close();

    if (!$sheet_xml) {
        return [];
    }

    $xml = @simplexml_load_string($sheet_xml);
    if (!$xml || !isset($xml->sheetData->row)) {
        return [];
    }

    $rows = [];
    foreach ($xml->sheetData->row as $row) {
        $row_cells = [];
        $col_idx = 0;

        foreach ($row->c as $cell) {
            // Tentukan indeks kolom dari atribut 'r' (misal: 'A1', 'B1')
            $cell_ref = (string)$cell['r'];
            preg_match('/^([A-Z]+)(\d+)$/', $cell_ref, $matches);
            $col_letters = $matches[1] ?? '';
            
            // Konversi huruf kolom ke angka (A=0, B=1, dst)
            $target_col = 0;
            for ($l = 0; $l < strlen($col_letters); $l++) {
                $target_col = $target_col * 26 + (ord($col_letters[$l]) - ord('A') + 1);
            }
            $target_col -= 1;

            // Isi sel kosong di antaranya jika ada kolom yang dilewati
            while ($col_idx < $target_col) {
                $row_cells[] = '';
                $col_idx++;
            }

            // Ambil nilai sel
            $cell_type = (string)$cell['t'];
            $val = isset($cell->v) ? (string)$cell->v : '';

            if ($cell_type === 's') {
                // Tipe shared string
                $str_index = (int)$val;
                $val = $shared_strings[$str_index] ?? '';
            } elseif ($cell_type === 'inlineStr' && isset($cell->is->t)) {
                $val = (string)$cell->is->t;
            }

            $row_cells[] = trim($val);
            $col_idx++;
        }

        if (count(array_filter($row_cells)) > 0) {
            $rows[] = $row_cells;
        }
    }

    return $rows;
}

/**
 * Mencari indeks kolom berdasarkan daftar nama alias yang mungkin digunakan
 *
 * @param array $headers Header baris pertama
 * @param array $possible_names Nama-nama yang dicari
 * @return int|null Indeks kolom atau null
 */
function importer_find_col_index($headers, $possible_names) {
    foreach ($headers as $idx => $h) {
        $clean = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$h));
        foreach ($possible_names as $p) {
            $p_clean = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $p));
            if ($clean === $p_clean || strpos($clean, $p_clean) !== false) {
                return $idx;
            }
        }
    }
    return null;
}

/**
 * Memproses parsing dan penyimpanan berkas unggahan (CSV atau XLSX)
 *
 * @param array  $file_upload Array dari $_FILES['file_data']
 * @param string $mode 'merge' (tambahkan/perbarui) atau 'replace' (timpa seluruh data)
 * @return array
 */
function importer_process_file($file_upload, $mode = 'merge') {
    if (!isset($file_upload['tmp_name']) || empty($file_upload['tmp_name'])) {
        return ['success' => false, 'message' => 'Tidak ada berkas yang diunggah.'];
    }

    $ext = strtolower(pathinfo($file_upload['name'], PATHINFO_EXTENSION));
    $tmp_path = $file_upload['tmp_name'];

    $raw_rows = [];
    if ($ext === 'csv' || $ext === 'txt') {
        $raw_rows = importer_read_csv($tmp_path);
    } elseif ($ext === 'xlsx' || $ext === 'xls') {
        $raw_rows = importer_read_xlsx($tmp_path);
    } else {
        return [
            'success' => false,
            'message' => 'Format berkas tidak didukung. Silakan gunakan format CSV (.csv) atau Excel (.xlsx).'
        ];
    }

    if (empty($raw_rows) || count($raw_rows) < 2) {
        return [
            'success' => false,
            'message' => 'Berkas kosong atau hanya berisi baris judul tanpa data.'
        ];
    }

    // Baris pertama sebagai header
    $headers = array_shift($raw_rows);

    // Identifikasi kolom secara cerdas
    $nim_idx       = importer_find_col_index($headers, ['nim', 'nomorinduk', 'nomor_mhs', 'no_mhs', 'id']);
    $nama_idx      = importer_find_col_index($headers, ['nama', 'nama_mahasiswa', 'namalengkap', 'mahasiswa', 'name']);
    $kehadiran_idx = importer_find_col_index($headers, ['kehadiran', 'presensi', 'absensi', 'hadir', 'kehadiran%']);
    $ipk_idx       = importer_find_col_index($headers, ['ipk', 'gpa', 'indeksprestasi', 'ip']);
    $sks_idx       = importer_find_col_index($headers, ['sks', 'totalsks', 'skslulus']);
    $prodi_idx     = importer_find_col_index($headers, ['prodi', 'programstudi', 'jurusan']);

    if ($kehadiran_idx === null || $ipk_idx === null) {
        return [
            'success' => false,
            'message' => 'Kolom wajib (Kehadiran dan IPK) tidak ditemukan di dalam berkas. Pastikan judul kolom sesuai.'
        ];
    }

    // Ambil data yang sudah ada jika mode 'merge'
    $existing_students = ($mode === 'replace') ? [] : student_get_all();
    $students_map = [];
    foreach ($existing_students as $s) {
        $students_map[trim((string)$s['nim'])] = $s;
    }

    $inserted = 0;
    $updated = 0;
    $skipped_external = 0;

    foreach ($raw_rows as $row) {
        // Ambil nilai per baris
        $kehadiran_raw = str_replace(',', '.', (string)($row[$kehadiran_idx] ?? '0'));
        $kehadiran = round((float)preg_replace('/[^0-9.]/', '', $kehadiran_raw), 1);

        $ipk_raw = str_replace(',', '.', (string)($row[$ipk_idx] ?? '0'));
        $ipk = round((float)preg_replace('/[^0-9.]/', '', $ipk_raw), 2);

        $sks = ($sks_idx !== null && isset($row[$sks_idx])) ? (int)preg_replace('/[^0-9]/', '', (string)$row[$sks_idx]) : 100;
        $prodi = ($prodi_idx !== null && !empty($row[$prodi_idx])) ? trim((string)$row[$prodi_idx]) : 'Teknik Informatika';

        $nama = ($nama_idx !== null && !empty($row[$nama_idx])) ? trim((string)$row[$nama_idx]) : 'Mahasiswa Impor';
        $nim = ($nim_idx !== null && !empty($row[$nim_idx])) ? trim((string)$row[$nim_idx]) : '';

        // Validasi NIM: Hanya menerima format mahasiswa FTII UHAMKA
        if ($nim !== '') {
            $val = student_validate_uhamka_nim($nim);
            if (!$val['valid']) {
                $skipped_external++;
                continue; // Tolak baris data dari kampus lain
            }
        } else {
            $nim = '2203015' . str_pad((string)rand(100, 999), 3, '0', STR_PAD_LEFT);
        }

        // Klasifikasikan dengan Decision Tree C4.5
        $c45 = c45_classify($kehadiran, $ipk, $sks);

        $student_entry = [
            'nim'       => $nim,
            'nama'      => $nama,
            'prodi'     => $prodi,
            'kehadiran' => $kehadiran,
            'ipk'       => $ipk,
            'sks'       => $sks,
            'status'    => $c45['status'],
            'rule'      => $c45['rule_number']
        ];

        if (isset($students_map[$nim])) {
            $updated++;
        } else {
            $inserted++;
        }

        $students_map[$nim] = $student_entry;
    }

    $final_students = array_values($students_map);

    // Simpan ke file JSON pangkalan data
    file_put_contents(
        STUDENT_DATA_FILE,
        json_encode($final_students, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );

    $msg = 'Data berhasil diimpor ke sistem!';
    if ($skipped_external > 0) {
        $msg .= " Catatan: {$skipped_external} baris data dilewati karena bukan NIM mahasiswa FTII UHAMKA.";
    }

    return [
        'success'          => true,
        'message'          => $msg,
        'total_imported'   => count($raw_rows),
        'inserted'         => $inserted,
        'updated'          => $updated,
        'skipped_external' => $skipped_external,
        'total_current'    => count($final_students)
    ];
}
