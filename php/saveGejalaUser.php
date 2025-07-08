<?php
session_start();
require_once '../config/Database.php';
require_once '../config/function.php';

// Set response sebagai JSON
header('Content-Type: application/json');

if (!isset($_SESSION['user']['userId'])) {
    $_SESSION['gejala_error'] = "Silakan login terlebih dahulu.";
    header("Location: pemantauan.php");
    exit;
}

$database = new Database();
$pdo = $database->getConnection();

$userId = $_SESSION['user']['userId'];
$tglGejala = $_POST['tglGejala'] ?? '';
$gejala = $_POST['gejala'] ?? '';
$intensitas = $_POST['intensitas'] ?? '';

// Validasi sederhana
if (!empty($tglGejala) && !empty($gejala) && !empty($intensitas)) {
    try {
        $stmt = $pdo->prepare("INSERT INTO gejalauser (userId, tglGejala, gejala, intensitas) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([$userId, $tglGejala, $gejala, $intensitas]);

        if ($result) {
            $_SESSION['gejala_success'] = "Catatan berhasil disimpan.";
        } else {
            $_SESSION['gejala_error'] = "Gagal menyimpan catatan.";
        }
    } catch (PDOException $e) {
        $_SESSION['gejala_error'] = "Error: " . $e->getMessage();
        error_log("Gagal insert: " . $e->getMessage());
    }
} else {
    $_SESSION['gejala_error'] = "Semua field wajib diisi.";
}

echo json_encode([
    'status' => 'success',
    'message' => $_SESSION['gejala_success'] ?? 'Berhasil'
]);
exit;
?>
