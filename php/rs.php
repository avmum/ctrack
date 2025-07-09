<?php
$cssFile = "../css/rsStyle.css";
include("header.php");

require_once '../config/Database.php';

$pdo = (new Database())->getConnection();

// Ambil daftar kota unik dari tabel rumahsakit
$stmtKota = $pdo->prepare("SELECT DISTINCT kota FROM rumahsakit ORDER BY kota ASC");
$stmtKota->execute();
$daftarKota = $stmtKota->fetchAll(PDO::FETCH_COLUMN);

// Ambil semua rumah sakit
$stmt = $pdo->prepare("SELECT * FROM rumahsakit");
$stmt->execute();
$daftarRS = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="page-wrapper">
    <main class="container">
        <h1 class="page-title">Daftar Rumah Sakit Kanker</h1>

        <div class="controls">
            <input type="text" id="searchInput" placeholder="Cari rumah sakit..." />
            <select id="filterKota">
                <option value="">Semua Kota/Kabupaten</option>
                <?php foreach ($daftarKota as $kota): ?>
                    <option value="<?= htmlspecialchars($kota) ?>"><?= htmlspecialchars($kota) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="table-container">
            <table id="rsTable">
                <thead>
                <tr>
                    <th>Nama Rumah Sakit</th>
                    <th>Alamat</th>
                    <th>Kota/Kabupaten</th>
                    <th>Provinsi</th>
                    <th>Kontak</th>
                    <th>Website</th>
                    <th>Spesialis</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($daftarRS as $rs): ?>
                    <tr>
                        <td data-label="Nama Rumah Sakit"><?= htmlspecialchars($rs['nama']) ?></td>
                        <td data-label="Alamat"><?= htmlspecialchars($rs['alamat']) ?></td>
                        <td data-label="Kota/Kabupaten"><?= htmlspecialchars($rs['kota']) ?></td>
                        <td data-label="Provinsi"><?= htmlspecialchars($rs['provinsi']) ?></td>
                        <td data-label="Kontak"><?= htmlspecialchars($rs['noTlp']) ?></td>
                        <td data-label="Website">
                            <?php if (!empty($rs['website'])): ?>
                                <a href="<?= htmlspecialchars($rs['website']) ?>" target="_blank"><?= htmlspecialchars($rs['website']) ?></a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td data-label="Spesialis"><?= htmlspecialchars($rs['spesialis']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Optional: Add JavaScript for enhanced filtering -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('searchInput');
                const filterKota = document.getElementById('filterKota');
                const table = document.getElementById('rsTable');
                const tbody = table.getElementsByTagName('tbody')[0];
                const rows = tbody.getElementsByTagName('tr');

                function filterTable() {
                    const searchTerm = searchInput.value.toLowerCase();
                    const selectedKota = filterKota.value.toLowerCase();

                    for (let i = 0; i < rows.length; i++) {
                        const row = rows[i];
                        const cells = row.getElementsByTagName('td');
                        let showRow = true;

                        // Check search term
                        if (searchTerm) {
                            let found = false;
                            for (let j = 0; j < cells.length; j++) {
                                if (cells[j].textContent.toLowerCase().includes(searchTerm)) {
                                    found = true;
                                    break;
                                }
                            }
                            if (!found) showRow = false;
                        }

                        // Check kota filter
                        if (selectedKota && showRow) {
                            const kotaCell = cells[2]; // Kota column is index 2
                            if (!kotaCell.textContent.toLowerCase().includes(selectedKota)) {
                                showRow = false;
                            }
                        }

                        row.style.display = showRow ? '' : 'none';
                    }
                }

                searchInput.addEventListener('input', filterTable);
                filterKota.addEventListener('change', filterTable);
            });
        </script>
    </main>

    <?php
    include("footer.php");
    ?>
</div>
</body>
</html>