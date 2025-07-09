<?php
require_once '../config/Database.php';
session_start();
$pdo = (new Database())->getConnection();

$userId = $_SESSION['user']['userId'] ?? 0;

$repeat = $_POST['repeatObat'] ?? 'tidak'; // default 'tidak'
$tgl = $_POST['tglObat'] ?? null;          // bisa kosong jika repeat = 'ya'
$waktu = $_POST['jamObat'] ?? null;
$nama = $_POST['namaObat'] ?? '';
$dosis = $_POST['dosisObat'] ?? '';
$stok = $_POST['stokObat'] ?? 0;
$keterangan = $_POST['ketObat'] ?? '';
$status = 'belum';

if ($repeat === 'ya') {
    $tgl = date('Y-m-d');
} else{
    $tgl = $_POST['tglObat'] ?? null;
}

$stmt = $pdo->prepare("INSERT INTO obat (userId, namaObat, dosis, stok, tglMinum, waktu, keterangan, repeatSetiapHari, status) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$success = $stmt->execute([$userId, $nama, $dosis, $stok, $tgl, $waktu, $keterangan, $repeat, $status]);

if ($success) {
    header("Location: perawatan.php?success=obat");
    exit;
} else {
    echo "Gagal menyimpan obat.";
}
?>
