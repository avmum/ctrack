<?php
$cssFile = "../css/perawatanStyle.css";
include("header.php");
require_once '../config/Database.php';

$pdo = (new Database())->getConnection();
$userId = $_SESSION['user']['userId'] ?? 0;

// Ambil data dari tabel perawatan
$stmt1 = $pdo->prepare("
  SELECT 
    'kontrol' AS jenis,
    perawatanId AS id,
    tglPerawatan AS tanggal,
    waktuPerawatan AS waktu,
    namaDokter AS nama,
    '' AS dosis,
    '' AS stok,
    keterangan,
    status
  FROM perawatan
  WHERE userId = ?
");
$stmt1->execute([$userId]);
$dataKontrol = $stmt1->fetchAll(PDO::FETCH_ASSOC);

// Ambil data dari tabel obat
$stmt2 = $pdo->prepare("
  SELECT 
    'obat' AS jenis,
    obatId AS id,
    COALESCE(tglMinum, CURDATE()) AS tanggal, -- kalau null, tampilkan tanggal hari ini (khusus yang repeat)
    waktu,
    namaObat AS nama,
    dosis,
    stok,
    keterangan,
    status
  FROM obat
  WHERE userId = ?
    AND (
        (repeatSetiapHari = 'ya') 
        OR 
        (repeatSetiapHari = 'tidak' AND tglMinum = CURDATE())
    )
");
$stmt2->execute([$userId]);
$dataObat = $stmt2->fetchAll(PDO::FETCH_ASSOC);

$dataGabungan = array_merge($dataKontrol, $dataObat);

// Urutkan berdasarkan tanggal + waktu
usort($dataGabungan, function ($a, $b) {
    return strtotime($a['tanggal'] . ' ' . $a['waktu']) <=> strtotime($b['tanggal'] . ' ' . $b['waktu']);
});

// Filter data berdasarkan status
$dataJadwal = array_filter($dataGabungan, function ($row) {
    return $row['status'] !== 'selesai';
});

$dataRiwayatGabungan = array_filter($dataGabungan, function ($row) {
    return $row['status'] === 'selesai';
});

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="<?= $cssFile ?>" />
    <title>Manajemen Perawatan</title>
</head>

<body>
<div class="page-wrapper">
    <main class="main-container">
        <?php if (isset($_GET['success'])): ?>
            <div class="popup-success" id="popupSuccess">
                ✅ Jadwal <?= $_GET['success'] == 'kontrol' ? 'kontrol' : 'minum obat' ?> berhasil ditambahkan!
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const popup = document.getElementById("popupSuccess");
                    popup.style.display = "block";
                    setTimeout(() => {
                        popup.style.opacity = "0";
                        setTimeout(() => popup.remove(), 500);
                    }, 3000);

                    // Hapus ?success=... dari URL setelah popup tampil
                    if (history.replaceState) {
                        const newUrl = window.location.origin + window.location.pathname;
                        history.replaceState({}, document.title, newUrl);
                    }
                });
            </script>
        <?php endif; ?>


        <h1>Manajemen Perawatan</h1>

        <!-- Navigasi Menu -->
        <nav class="menu-nav">
            <button class="menu-btn" onclick="showSection('jadwal')">📅 Jadwal Pengobatan</button>
            <button class="menu-btn" onclick="showSection('riwayat')">📋 Riwayat Konsultasi</button>
        </nav>

        <!-- Section: Jadwal -->
        <div class="menu-section" id="jadwal" style="display: block;">
            <section class="treatment-schedule">
                <button class="btn add-btn" onclick="togglePilihanForm()">+ Tambah Jadwal</button>

                <!-- Popup Pilihan Jenis Jadwal -->
                <div class="popup-form" id="pilihanForm" style="display: none;">
                    <div class="popup-content">
                        <h3>Pilih Jenis Jadwal</h3>
                        <button class="btn" onclick="showForm('kontrol')">📌 Jadwal Kontrol</button>
                        <button class="btn" onclick="showForm('minum')">💊 Jadwal Minum Obat</button>
                        <button class="btn-batal" onclick="closeAllForms()">Batal</button>
                    </div>
                </div>

                <!-- Form Jadwal Kontrol -->
                <div class="popup-form" id="formKontrol" style="display: none;">
                    <div class="popup-content">
                        <h3>Tambah Jadwal Kontrol</h3>
                        <form class="care-form" action="simpanKontrol.php" method="POST">
                            <input type="date" name="tglKontrol" required>
                            <input type="time" name="jamKontrol" required>
                            <input type="text" name="dokter" required placeholder="Dokter">
                            <textarea name="ketKontrol" placeholder="Keterangan Kontrol"></textarea>
                            <button type="submit" class="btn">💾 Simpan</button>
                            <button type="button" class="btn-batal" onclick="closeAllForms()">Tutup</button>
                        </form>
                    </div>
                </div>

                <!-- Form Jadwal Minum Obat -->
                <div class="popup-form" id="formMinum" style="display: none;">
                    <div class="popup-content" style="max-height: 90vh; overflow-y: auto; scrollbar-width: none; -ms-overflow-style: none;">
                        <h3>Tambah Jadwal Minum Obat</h3>
                        <form class="care-form" action="simpanObat.php" method="POST">
                            <select name="repeatObat" id="repeatObat" onchange="toggleTanggalObat()" required>
                                <option value="">-- Pilih Tipe Jadwal --</option>
                                <option value="ya">Setiap Hari</option>
                                <option value="tidak">Hanya Tanggal Tertentu</option>
                            </select>

                            <div id="tglObatWrapper" style="display: none;">
                                <input type="date" name="tglObat" id="tglObat" required placeholder="Tanggal Minum Obat">
                            </div>
                            <input type="time" name="jamObat" required>
                            <input type="text" name="namaObat" required placeholder="Nama Obat">
                            <input type="text" name="dosisObat" required placeholder="Dosis Obat">
                            <input type="number" name="stokObat" required placeholder="Stok Obat">
                            <textarea name="ketObat" placeholder="Keterangan Minum Obat"></textarea>
                            <button type="submit" class="btn">💾 Simpan</button>
                            <button type="button" class="btn-batal" onclick="closeAllForms()">Tutup</button>
                        </form>
                    </div>
                </div>

                <h2 class="schedule-title">💊 Jadwal Pengobatan & Minum Obat</h2>
                <table class="schedule-table">
                    <thead>
                    <tr>
                        <th>Status</th>
                        <th>Hari/Tanggal</th>
                        <th>Waktu</th>
                        <th>Nama Obat/Dokter</th>
                        <th>Dosis</th>
                        <th>Stok</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody id="jadwalBody">
                    <?php foreach ($dataJadwal as $row): ?>
                        <tr>
                            <td>
                                <input type="checkbox"
                                       class="status-checkbox"
                                       data-jenis="<?= $row['jenis'] ?>"
                                       data-id="<?= $row['id'] ?>"
                                       onchange="updateStatus(this)"
                                    <?= $row['status'] === 'selesai' ? 'checked disabled' : '' ?>>

                            </td>
                            <td><?= date("l, d M Y", strtotime($row['tanggal'])) ?></td>
                            <td><?= htmlspecialchars($row['waktu']) ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['dosis']) ?></td>
                            <td><?= htmlspecialchars($row['stok']) ?></td>
                            <td><?= htmlspecialchars($row['keterangan']) ?></td>
                            <td>
                                <a href="#"
                                   class="btn-edit"
                                   onclick="openEditModal('<?= $row['jenis'] ?>', <?= $row['id'] ?>)" style="">
                                    ✏️
                                </a>
                                <a href="#"
                                   class="btn-hapus"
                                   data-id="<?= $row['id'] ?>"
                                   data-jenis="<?= $row['jenis'] ?>"
                                   onclick="showHapusModal(this)">
                                    🗑️
                                </a>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </div>

        <!-- Section: Riwayat -->
        <div class="menu-section" id="riwayat" style="display: none;">
            <section class="section">
                <h2>📋 Riwayat Konsultasi</h2>
                <table class="riwayat-table">
                    <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Dokter</th>
                        <th>Catatan</th>
                    </tr>
                    </thead>
                    <tbody id="riwayatBody">
                    <?php
                    $stmt = $pdo->prepare("
          SELECT tglPerawatan AS tanggal, namaDokter AS dokter, keterangan 
          FROM perawatan 
          WHERE userId = ? AND status = 'selesai' 
          ORDER BY tglPerawatan DESC
        ");
                    $stmt->execute([$userId]);
                    $dataRiwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($dataRiwayat) > 0):
                        foreach ($dataRiwayat as $r): ?>
                            <tr>
                                <td><?= date("Y-m-d", strtotime($r['tanggal'])) ?></td>
                                <td><?= htmlspecialchars($r['dokter']) ?></td>
                                <td><?= htmlspecialchars($r['keterangan']) ?></td>
                            </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="3">Belum ada riwayat konsultasi.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </main>

    <?php include("footer.php"); ?>

    <!-- Modal Konfirmasi -->
    <div id="popupKonfirmasi" class="popup-overlay" style="display: none;">
        <div class="popup-box">
            <p>Yakin ingin menandai sebagai selesai?</p>
            <div class="popup-actions">
                <button class="btn btn-ya" id="btnYa">Ya</button>
                <button class="btn btn-tidak" id="btnTidak">Batal</button>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="editModal" class="popup-form" style="display: none;">
        <div class="popup-content" onclick="event.stopPropagation()">
            <div id="editModalBody">
                <p>Memuat formulir...</p>
            </div>
        </div>
    </div>

    <!-- Modal Gapus -->
    <div id="hapusModal" class="popup-form" style="display: none;">
        <div class="popup-content">
            <h3>Konfirmasi Hapus</h3>
            <div class="modal-body-konsultasi">
                <p>Apakah Anda yakin ingin menghapus data ini?</p>
                <div class="note" id="hapusInfo"></div>
            </div>
            <button class="btn" onclick="confirmHapus()">Ya, Hapus</button>
            <button class="btn-batal" onclick="closeHapusModal()">Batal</button>
        </div>
    </div>

    <div id="successToast" class="success-toast">Berhasil!</div>
</div>
</body>
</html>
