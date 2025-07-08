<?php
require_once '../config/database.php';
session_start();
$pdo = (new Database())->getConnection();

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM obat WHERE obatId = ?");
    $stmt->execute([$id]);
}
header("Location: perawatan.php");
exit();
?>
