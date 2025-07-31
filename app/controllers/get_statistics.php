<?php
header('Content-Type: application/json');
require_once '../../config/database.php';
require_once '../core/auth.php';

// Pastikan user sudah login
if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

try {
    // Hitung total kegiatan
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM bukti_dukung");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Hitung kegiatan yang sedang berjalan (progress < 100)
    $stmt = $pdo->query("SELECT COUNT(*) as ongoing FROM bukti_dukung WHERE progress < 100");
    $ongoing = $stmt->fetch(PDO::FETCH_ASSOC)['ongoing'];

    // Hitung kegiatan yang selesai (progress = 100)
    $stmt = $pdo->query("SELECT COUNT(*) as completed FROM bukti_dukung WHERE progress = 100");
    $completed = $stmt->fetch(PDO::FETCH_ASSOC)['completed'];

    // Hitung rata-rata progress
    $stmt = $pdo->query("SELECT AVG(progress) as avg_progress FROM bukti_dukung");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $avgProgress = $result['avg_progress'] ? round($result['avg_progress'], 1) : 0;

    echo json_encode([
        'success' => true,
        'stats' => [
            'total' => (int)$total,
            'ongoing' => (int)$ongoing,
            'completed' => (int)$completed,
            'avgProgress' => (float)$avgProgress
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
