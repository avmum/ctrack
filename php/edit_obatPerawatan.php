<?php
require_once '../config/database.php';
session_start();
$pdo = (new Database())->getConnection();

$id = $_GET['id'] ?? null;
if (!$id) exit("ID tidak ditemukan.");

// Handle POST submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaObat = $_POST['namaObat'];
    $dosis = $_POST['dosis'];
    $stok = $_POST['stok'];
    $tgl = $_POST['tglMinum'];
    $waktu = $_POST['waktu'];
    $ket = $_POST['keterangan'];

    $stmt = $pdo->prepare("UPDATE obat SET namaObat=?, dosis=?, stok=?, tglMinum=?, waktu=?, keterangan=? WHERE obatId=?");
    $success = $stmt->execute([$namaObat, $dosis, $stok, $tgl, $waktu, $ket, $id]);

    if ($success) {
        echo "success";
    } else {
        $error = $stmt->errorInfo();
        echo "error: " . $error[2]; // tampilkan pesan error
    }
    exit;
}

// Jika GET request, tampilkan form
$stmt = $pdo->prepare("SELECT * FROM obat WHERE obatId = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!-- Form Edit Obat di dalam modal -->
<h3>Edit Jadwal Minum Obat</h3>
<form id="formEditObat" class="care-form" method="POST" action="edit_obatPerawatan.php?id=<?= $id ?>">
    <input type="text" name="namaObat" value="<?= htmlspecialchars($data['namaObat']) ?>" required>
    <input type="text" name="dosis" value="<?= htmlspecialchars($data['dosis']) ?>" required>
    <input type="number" name="stok" value="<?= htmlspecialchars($data['stok']) ?>" required>
    <input type="date" name="tglMinum" value="<?= $data['tglMinum'] ?>" required>
    <input type="time" name="waktu" value="<?= $data['waktu'] ?>" required>
    <textarea name="keterangan" rows="3"><?= htmlspecialchars($data['keterangan']) ?></textarea>
    <div class="form-actions">
        <button type="submit" class="btn">💾 Simpan Perubahan</button>
        <button type="button" class="btn-batal" onclick="closeEditModal()">Tutup</button>
    </div>
</form>


