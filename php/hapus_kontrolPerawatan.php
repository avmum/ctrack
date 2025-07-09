<?php
require_once '../config/Database.php';
session_start();
$pdo = (new Database())->getConnection();

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM perawatan WHERE perawatanId = ?");
    $stmt->execute([$id]);
}
header("Location: perawatan.php");
exit();
?>
