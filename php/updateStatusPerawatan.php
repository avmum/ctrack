<?php
ob_clean();
header('Content-Type: application/json');

require_once '../config/database.php';
session_start();
$pdo = (new Database())->getConnection();

$jenis = $_GET['jenis'] ?? '';
$id = $_GET['id'] ?? '';

if (!$jenis || !$id) {
    echo json_encode(['success' => false, 'message' => 'Parameter tidak lengkap']);
    exit;
}

if ($jenis === 'obat') {
    // Cek stok sekarang
    $stmt = $pdo->prepare("SELECT stok FROM obat WHERE obatId = ?");
    $stmt->execute([$id]);
    $obat = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$obat) {
        echo json_encode(['success' => false, 'message' => 'Obat tidak ditemukan']);
        exit;
    }

    $stokBaru = max(0, $obat['stok'] - 1); // Kurangi stok, tidak boleh negatif

    $stmt = $pdo->prepare("UPDATE obat SET status = 'selesai', stok = ? WHERE obatId = ?");
    $success = $stmt->execute([$stokBaru, $id]);

} elseif ($jenis === 'kontrol') {
    $stmt = $pdo->prepare("UPDATE perawatan SET status = 'selesai' WHERE perawatanId = ?");
    $success = $stmt->execute([$id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Jenis tidak valid']);
    exit;
}

echo json_encode(['success' => $success]);
exit;
?>