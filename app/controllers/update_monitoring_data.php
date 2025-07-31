<?php
// Tampilkan semua error
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    // Validasi input
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
        exit();
    }

    $id = (int) $_POST['id'];
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
    $target_bulan = $_POST['target_bulan'] . '-01'; // Tambah tanggal 1
    if (!strtotime($target_bulan)) {
        echo json_encode(['success' => false, 'message' => 'Format target bulan tidak valid']);
        exit();
    }

    // Cek apakah data exists
    $check_stmt = $pdo->prepare("SELECT id FROM bukti_dukung WHERE id = ?");
    $check_stmt->execute([$id]);

    if (!$check_stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
        exit();
    }

    // Update data
    $stmt = $pdo->prepare("
        UPDATE bukti_dukung 
        SET rencana_kerja = ?, rencana_aksi = ?, output = ?, pjk = ?, 
            target_bulan = ?, bukti_link = ?, progress = ?, status = ?, updated_at = CURRENT_TIMESTAMP
        WHERE id = ?
    ");

    $result = $stmt->execute([
        clean_input($_POST['rencanaKerja']),
        clean_input($_POST['rencanaAksi']),
        clean_input($_POST['output']),
        clean_input($_POST['pjk']),
        $target_bulan,
        clean_input($_POST['bukti_link']),
        $progress,
        $status,
        $id
    ]);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Data berhasil diupdate'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Gagal mengupdate data'
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}