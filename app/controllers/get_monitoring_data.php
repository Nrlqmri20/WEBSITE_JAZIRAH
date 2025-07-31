<?php
// Aktifkan error reporting untuk debug
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Set header
header('Content-Type: application/json');

// Coba include file yang diperlukan
try {
    require_once '../../config/database.php';
    require_once '../core/auth.php';
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to include required files: ' . $e->getMessage()
    ]);
    exit();
}

// Debug: Cek apakah fungsi auth tersedia
if (!function_exists('is_logged_in')) {
    echo json_encode([
        'success' => false, 
        'message' => 'Auth function not available'
    ]);
    exit();
}

// Pastikan user sudah login
if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized - Please login first']);
    exit();
}

// Debug: Cek koneksi database
if (!isset($pdo)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Database connection not available'
    ]);
    exit();
}

try {
    // Jika ada parameter ID, ambil data spesifik
    if (isset($_GET['id'])) {
        $id = (int) $_GET['id'];
        $stmt = $pdo->prepare("
            SELECT 
                bd.id_pendukung as id,
                bd.link as bukti_link,
                bd.keterangan,
                bd.pjk,
                bd.target_bulan,
                bd.progress,
                d.deskripsi_rencana_kinerja AS rencana_kerja,
                d.deskripsi_rencana_aksi AS rencana_aksi,
                d.deskripsi_rencana_output AS output,
                bd.created_at
            FROM bukti_dukung bd
            LEFT JOIN deskripsi d ON bd.id_deskripsi = d.id_deskripsi
            WHERE bd.id_pendukung = ?
        ");
        $stmt->execute([$id]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Ambil semua data
        $stmt = $pdo->prepare("
            SELECT 
                bd.id_pendukung as id,
                bd.link as bukti_link,
                bd.keterangan,
                bd.pjk,
                bd.target_bulan,
                bd.progress,
                d.deskripsi_rencana_kinerja AS rencana_kerja,
                d.deskripsi_rencana_aksi AS rencana_aksi,
                d.deskripsi_rencana_output AS output,
                bd.created_at
            FROM bukti_dukung bd
            LEFT JOIN deskripsi d ON bd.id_deskripsi = d.id_deskripsi
            ORDER BY bd.created_at DESC
        ");
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Debug: Log jumlah data yang ditemukan
    error_log("Found " . count($data) . " records");

    echo json_encode([
        'success' => true,
        'data' => $data,
        'debug' => [
            'total_records' => count($data),
            'query_executed' => true,
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ]);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'debug' => [
            'error_code' => $e->getCode(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine()
        ]
    ]);
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage(),
        'debug' => [
            'error_type' => get_class($e),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine()
        ]
    ]);
}