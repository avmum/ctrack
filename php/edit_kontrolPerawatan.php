<?php
require_once '../config/database.php';
session_start();
$pdo = (new Database())->getConnection();

$id = $_GET['id'] ?? null;
if (!$id) exit("ID tidak ditemukan.");

// Handle POST submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tgl = $_POST['tglPerawatan'];
    $waktu = $_POST['waktuPerawatan'];
    $dokter = $_POST['namaDokter'];
    $ket = $_POST['keterangan'];

    $stmt = $pdo->prepare("UPDATE perawatan SET tglPerawatan=?, waktuPerawatan=?, namaDokter=?, keterangan=? WHERE perawatanId=?");
    $success = $stmt->execute([$tgl, $waktu, $dokter, $ket, $id]);

    if ($success) {
        echo "success";
    } else {
        $error = $stmt->errorInfo();
        echo "error: " . $error[2];
    }
    exit; // menghentikan eksekusi agar tidak render HTML form
}

// Jika GET request, ambil data
$stmt = $pdo->prepare("SELECT * FROM perawatan WHERE perawatanId = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!-- HTML FORM untuk edit kontrol -->
<h3>Edit Jadwal Kontrol</h3>
<form id="formEditKontrol" class="care-form" method="POST" action="edit_kontrolPerawatan.php?id=<?= $id ?>">
    <input type="date" name="tglPerawatan" value="<?= $data['tglPerawatan'] ?>" required>
    <input type="time" name="waktuPerawatan" value="<?= $data['waktuPerawatan'] ?>" required>
    <input type="text" name="namaDokter" value="<?= htmlspecialchars($data['namaDokter']) ?>" required>
    <textarea name="keterangan"><?= htmlspecialchars($data['keterangan']) ?></textarea>
    <div class="form-actions">
        <button type="submit" class="btn">💾 Simpan Perubahan</button>
        <button type="button" class="btn-batal" onclick="closeEditModal()">Tutup</button>
    </div>
</form>
