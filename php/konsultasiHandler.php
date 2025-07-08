<?php
session_start();
require_once '../config/Database.php';
require_once '../classes/SaranGenerator.php';

try {
    $pdo = (new Database())->getConnection();
} catch (Exception $e) {
    error_log("Koneksi database gagal: " . $e->getMessage());
    die("DB Error: " . $e->getMessage());
    exit;
}

// Ambil data dari form
$nama         = trim($_POST['nama'] ?? '');
$usia         = intval($_POST['usia'] ?? 0);
$jenisKelamin = trim($_POST['jenisKelamin'] ?? '');
$keluhan      = trim($_POST['keluhan'] ?? '');
$riwayat      = trim($_POST['riwayatKeluarga'] ?? '');
$userId       = $_SESSION['user']['userId'] ?? null;

// Validasi dasar
if (!$nama || !$usia || !$jenisKelamin || !$keluhan || !$userId || $usia < 1 || $usia > 120) {
    die("Validasi gagal: Nama = $nama | Usia = $usia | Jenis Kelamin = $jenisKelamin | Keluhan = $keluhan | UserID = $userId");
    exit;
}

// Proses keluhan jadi array gejala
$gejalaUser = array_filter(array_map('trim', explode(',', strtolower($keluhan))));
if (empty($gejalaUser)) {
    header("Location: konsultasi.php?status=no_gejala");
    exit;
}

// Siapkan array data
$gejalaData   = [];
$kankerData   = [];
$kankerGejala = [];

try {
    // Ambil data gejala
    $stmt = $pdo->query("SELECT gejalaId, nama FROM gejala");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $gejalaData[$row['gejalaId']] = $row['nama'];
    }

    // Ambil data kanker
    $stmt = $pdo->query("SELECT kankerId, nama FROM jenisKanker");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $kankerData[$row['kankerId']] = ['nama' => $row['nama']];
    }

    // Ambil relasi kanker-gejala
    $stmt = $pdo->query("SELECT kankerId, gejalaId FROM kankerGejala");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $kankerGejala[$row['kankerId']][] = $row['gejalaId'];
    }

} catch (PDOException $e) {
    error_log("Gagal mengambil data: " . $e->getMessage());
    header("Location: konsultasi.php?status=query_error");
    exit;
}

if (empty($gejalaData) || empty($kankerData)) {
    header("Location: konsultasi.php?status=no_data");
    exit;
}

// Inisialisasi dan analisis
$saranGen = new SaranGenerator();
$saranGen->setJenisKankerData($kankerData);
$saranGen->setGejalaData($gejalaData);
$saranGen->setKankerGejalaMapping($kankerGejala);

$analisis = $saranGen->analisisMultipleKanker($gejalaUser, 1);

if (empty($analisis)) {
    $jenisKankerId   = null;
    $skor            = 0;
    $saranPenanganan = "Tidak ditemukan kecocokan gejala dengan data kanker yang tersedia. Disarankan untuk berkonsultasi langsung ke dokter.";
} else {
    $hasil           = $analisis[0];
    $jenisKankerId   = $hasil['id'];
    $skor            = $hasil['skor'];
    $saranPenanganan = $saranGen->generateSaran($jenisKankerId, $skor, $gejalaUser);
}

try {
    $pdo->beginTransaction();

    // Simpan ke tabel konsultasi
    $stmt = $pdo->prepare("INSERT INTO konsultasi (userId, nama, usia, jenisKelamin, keluhan, riwayatKeluarga, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$userId, $nama, $usia, $jenisKelamin, $keluhan, $riwayat]);
    $konsultasiId = $pdo->lastInsertId();

    // Simpan ke tabel hasilKonsultasi
    $stmt = $pdo->prepare("INSERT INTO hasilKonsultasi (konsultasiId, kankerId, skorKecocokan, saranPenanganan, dibuat) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$konsultasiId, $jenisKankerId, $skor, $saranPenanganan]);

    $pdo->commit();

    header("Location: konsultasi.php?sukses=1");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Gagal menyimpan konsultasi: " . $e->getMessage());

    header("Location: konsultasi.php?status=save_error");
    exit;
}
?>
