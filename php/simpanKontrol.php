<?php
require_once '../config/Database.php';
session_start();

$pdo = (new Database())->getConnection();
$userId = $_SESSION['user']['userId'];

$tanggal = $_POST['tglKontrol'];
$jam = $_POST['jamKontrol'];
$dokter = $_POST['dokter'];
$ket = $_POST['ketKontrol'];

$stmt = $pdo->prepare("INSERT INTO perawatan (userId, tglPerawatan, waktuPerawatan, namaDokter, keterangan, status) VALUES (?, ?, ?, ?, ?, 'belum')");
$stmt->execute([$userId, $tanggal, $jam, $dokter, $ket]);

header("Location: perawatan.php?success=kontrol");
exit();
?>
