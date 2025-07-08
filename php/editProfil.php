<?php
session_start();
require_once '../config/database.php';

$database = new Database();
$pdo = $database->getConnection();

$userId = $_SESSION['user']['userId'];

// Ambil data dari form
$nama = $_POST['nama'];
$email = $_POST['email'];
$tglLahir = $_POST['tglLahir'];
$jenisKelamin = $_POST['gender'];
$noHp = $_POST['noHp'];
$alamat = $_POST['alamat'];
$goldar = $_POST['goldar'];
$passwordBaru = $_POST['passwordBaru'] ?? null;
$fotoBaru = $_FILES['fotoProfil']['name'] ?? null;

// 1. Update password jika ada
if (!empty($passwordBaru)) {
    $hashedPassword = password_hash($passwordBaru, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE userId = ?");
    $stmt->execute([$hashedPassword, $userId]);
}

// 2. Upload Foto jika ada
if (!empty($fotoBaru)) {
    $targetDir = "../uploads/";
    $targetFile = $targetDir . basename($_FILES["foto"]["name"]);
    move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFile);

    $stmt = $pdo->prepare("UPDATE users SET fotoProfil = ? WHERE userId = ?");
    $stmt->execute([$fotoBaru, $userId]);
}

// 3. Update data lain
$stmt = $pdo->prepare("UPDATE users SET nama=?, email=?, tglLahir=?, jenisKelamin=?, noHp=?, alamat=?, golDarah=? WHERE userId=?");
$stmt->execute([$nama, $email, $tglLahir, $jenisKelamin, $noHp, $alamat, $goldar, $userId]);

// Kembali ke beranda
header("Location: beranda.php");
exit;
