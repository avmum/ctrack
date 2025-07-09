<?php
require_once '../config/Database.php';
session_start();

$database = new Database();
$pdo = $database->getConnection();

$userId = $_SESSION['user']['userId'] ?? null;

if (!$userId) {
    echo json_encode(['status' => 'error', 'message' => 'User belum login']);
    exit;
}

// Ambil data gejala per hari
$sql = "SELECT DAYNAME(tglGejala) AS hari, AVG(CASE 
            WHEN intensitas = 'ringan' THEN 2
            WHEN intensitas = 'sedang' THEN 4
            WHEN intensitas = 'berat' THEN 6
            ELSE 0 END) AS rata_intensitas
        FROM gejalaUser 
        WHERE userId = ?
        GROUP BY hari
        ORDER BY FIELD(hari, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')";

$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format ulang data
$response = [
    'status' => 'success',
    'labels' => [],
    'values' => []
];

foreach ($data as $row) {
    $response['labels'][] = ucfirst($row['hari']); // Contoh: Senin
    $response['values'][] = (float) $row['rata_intensitas'];
}

header('Content-Type: application/json');
echo json_encode($response);
