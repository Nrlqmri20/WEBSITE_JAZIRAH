<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Memberitahu browser bahwa kita akan mengirimkan data dalam format JSON
header('Content-Type: application/json');
require_once '../../config/database.php';
require_once '../core/auth.php';

// Pastikan user sudah login
if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Pastikan method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    // Tangkap dan bersihkan input
    $full_id_raw = $_POST['id_deskripsi'] ?? '';
    $id_deskripsi = trim($_POST['id_deskripsi']);
    $sub_deskripsi = trim($_POST['sub_deskripsi']);

    // Log untuk debug
    error_log("🔍 POST id_deskripsi = [$id_deskripsi]");
    error_log("🔍 POST sub_deskripsi = [$sub_deskripsi]");

    if (!preg_match('/^[A-Za-z0-9\.\-]+$/', $id_deskripsi)) {
        echo json_encode(['success' => false, 'message' => 'Format id_deskripsi tidak valid: ' . $id_deskripsi]);
        exit();
    }

    if (!preg_match('/^[a-zA-Z0-9]+$/', $sub_deskripsi)) {
        echo json_encode([
            'success' => false,
            'message' => 'Format sub_deskripsi tidak valid',
            'debug' => [
                'sub_deskripsi_raw' => $sub_deskripsi,
                'ascii_codes' => array_map('ord', str_split($sub_deskripsi))
            ]
        ]);
        exit();
    }

    error_log("✅ id_deskripsi = [$id_deskripsi]");
    error_log("✅ sub_deskripsi = [$sub_deskripsi]");

    // Validasi isi ID
    if (!preg_match('/^[A-Za-z0-9\.\-]+$/', $id_deskripsi)) {
        echo json_encode([
            'success' => false,
            'message' => 'Format id_deskripsi tidak valid',
            'debug' => ['id_deskripsi' => $id_deskripsi]
        ]);
        exit();
    }

    if (!preg_match('/^[a-zA-Z0-9]+$/', $sub_deskripsi)) {
        echo json_encode([
            'success' => false,
            'message' => 'Format sub_deskripsi tidak valid',
            'debug' => [
                'sub_deskripsi_raw' => $sub_deskripsi,
                'ascii_codes' => array_map('ord', str_split($sub_deskripsi))
            ]
        ]);
        exit();
    }

    // Validasi input lain
    $required_fields = ['rencanaKerja', 'rencanaAksi', 'output', 'pjk', 'target_bulan', 'progress'];

    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            echo json_encode(['success' => false, 'message' => 'Field ' . $field . ' wajib diisi']);
            exit();
        }
    }

    // Validasi progress
    $progress = (int) $_POST['progress'];
    if ($progress < 0 || $progress > 100) {
        echo json_encode(['success' => false, 'message' => 'Progress harus antara 0-100']);
        exit();
    }

    // Validasi target bulan
    $target_bulan = $_POST['target_bulan'] . '-01'; // Tambah tanggal agar bisa diparse
    if (!strtotime($target_bulan)) {
        echo json_encode(['success' => false, 'message' => 'Format target bulan tidak valid']);
        exit();
    }

    // Cek duplikasi
    $check = $pdo->prepare("SELECT COUNT(*) FROM deskripsi WHERE id_deskripsi = ? AND sub_deskripsi = ?");
    $check->execute([$id_deskripsi, $sub_deskripsi]);
    if ($check->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'ID deskripsi dan sub deskripsi sudah digunakan']);
        exit();
    }

    // Tentukan status otomatis
    $status = 'pending';
    if ($progress > 0 && $progress < 100) {
        $status = 'in_progress';
    } elseif ($progress == 100) {
        $status = 'completed';
    }

    // Simpan ke tabel deskripsi
    $stmt1 = $pdo->prepare("
        INSERT INTO deskripsi 
        (id_deskripsi, sub_deskripsi, deskripsi_rencana_kinerja, deskripsi_rencana_aksi, deskripsi_rencana_output) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt1->execute([
        $id_deskripsi,
        $sub_deskripsi,
        clean_input($_POST['rencanaKerja']),
        clean_input($_POST['rencanaAksi']),
        clean_input($_POST['output'])
    ]);

    // Simpan ke tabel bukti_dukung
    $stmt2 = $pdo->prepare("
        INSERT INTO bukti_dukung 
        (id_deskripsi, pjk, target_bulan, link, keterangan, progress, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt2->execute([
        $id_deskripsi,
        clean_input($_POST['pjk']),
        $target_bulan,
        clean_input($_POST['bukti_link'] ?? ''),
        '',
        $progress,
        $status,
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Data berhasil ditambahkan',
        'id_deskripsi' => $id_deskripsi,
        'sub_deskripsi' => $sub_deskripsi
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
