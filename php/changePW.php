<?php
session_start();
require_once '../config/database.php';

$database = new Database();
$pdo      = $database->getConnection();

$userId             = $_SESSION['user']['userId'];
$passwordLama       = $_POST['passwordLama']      ?? '';
$passwordBaru       = $_POST['passwordBaru']      ?? '';
$konfirmasiPassword = $_POST['konfirmasiPassword']?? '';

// Validasi dasar
if ($passwordBaru !== $konfirmasiPassword) {
    $_SESSION['error_message'] = 'Password baru tidak cocok dengan konfirmasi.';
    header('Location: beranda.php');
    exit;
}

// Ambil hash password lama
$stmt = $pdo->prepare('SELECT password FROM users WHERE userId = ?');
$stmt->execute([$userId]);
$data = $stmt->fetch();

if (!$data || !password_verify($passwordLama, $data['password'])) {
    $_SESSION['error_message'] = 'Password lama salah!';
    header('Location: beranda.php');
    exit;
}

//  Update password
$hashedPasswordBaru = password_hash($passwordBaru, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('UPDATE users SET password = ? WHERE userId = ?');
$stmt->execute([$hashedPasswordBaru, $userId]);

// Set “bendera” sukses satu kali
$_SESSION['popup_berhasil'] = true;

$redirect = $_POST['redirect_url'] ?? 'beranda.php';
header('Location: ' . $redirect);
exit;
