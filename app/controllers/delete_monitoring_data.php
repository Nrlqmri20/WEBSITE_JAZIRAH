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

    // Cek apakah data exists
    $check_stmt = $pdo->prepare("SELECT id, created_by FROM monitoring_data WHERE id = ?");
    $check_stmt->execute([$id]);
    $data = $check_stmt->fetch();

    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
        exit();
    }

    // Cek permission (user hanya bisa hapus data yang dia buat, kecuali admin)
    if ($_SESSION['user_role'] !== 'admin' && $data['created_by'] != $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki izin untuk menghapus data ini']);
        exit();
    }

    // Delete data
    $stmt = $pdo->prepare("DELETE FROM monitoring_data WHERE id = ?");
    $result = $stmt->execute([$id]);

    if ($result && $stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Data berhasil dihapus'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus data']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}