<?php
require_once '../config/Database.php';

$pdo = (new Database())->getConnection();
$cssFile = "../css/konsultasiStyle.css";
include("header.php");

// Ambil userId dari session
$userId = $_SESSION['user']['userId'] ?? null;
?>

<body>
<div class="page-wrapper">
    <div class="main-container">
        <div class="menu-nav">
            <button class="menu-btn active" onclick="showTab('form-tab', this)">Konsultasi</button>
            <button class="menu-btn" onclick="showTab('riwayat-tab', this)">Riwayat Konsultasi</button>
        </div>

        <!-- Tab: Konsultasi -->
        <main class="form-container tab-content" id="form-tab">
            <h2>Konsultasi Awal</h2>
            <p>Isi formulir di bawah ini untuk mendapatkan saran awal terkait kondisi kesehatan Anda.</p>

            <form action="konsultasiHandler.php" method="POST" class="form-box">
                <label for="nama">Nama Lengkap:</label>
                <input type="text" id="nama" name="nama" required />

                <label for="usia">Usia:</label>
                <input type="number" id="usia" name="usia" min="1" max="120" required />

                <label for="jenisKelamin">Jenis Kelamin:</label>
                <select id="jenisKelamin" name="jenisKelamin" required>
                    <option value="">--Pilih--</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>

                <label for="keluhan">Keluhan Utama (pisahkan dengan koma):</label>
                <textarea id="keluhan" name="keluhan" rows="4" placeholder="Contoh: sakit kepala, mual, demam" required></textarea>

                <label for="riwayat">Riwayat Kesehatan Keluarga:</label>
                <textarea id="riwayat" name="riwayatKeluarga" rows="3" placeholder="Riwayat penyakit dalam keluarga (opsional)"></textarea>

                <button type="submit" class="submit-btn">Kirim Konsultasi</button>
            </form>

            <?php
            if ($userId) {
                try {
                    $responseQuery = $pdo->prepare("
                    SELECT hk.saranPenanganan, k.created_at
                    FROM hasilKonsultasi hk
                    JOIN konsultasi k ON hk.konsultasiId = k.konsultasiId
                    WHERE k.userId = ?
                    ORDER BY hk.hasilKonsultasiId DESC
                    LIMIT 1
                ");
                    $responseQuery->execute([$userId]);
                    $response = $responseQuery->fetch(PDO::FETCH_ASSOC);

                    if ($response):
                        ?>
                    <?php endif;
                } catch (PDOException $e) {
                    echo "<div class='alert-error'>Gagal memuat hasil konsultasi: " . htmlspecialchars($e->getMessage()) . "</div>";
                }
            }
            ?>
        </main>

        <!-- Tab: Riwayat Konsultasi -->
        <div class="tab-content" id="riwayat-tab" style="display: none;">
            <div class="riwayat-container">
                <h2>Riwayat Konsultasi</h2>
                <p>Berikut adalah riwayat konsultasi Anda sebelumnya:</p>

                <div class="table-wrapper">
                    <table class="riwayat-table">
                        <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Usia</th>
                            <th>Jenis Kelamin</th>
                            <th>Tanggal</th>
                            <th>Keluhan Utama</th>
                            <th>Riwayat Keluarga</th>
                            <th>Hasil Konsultasi</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if ($userId) {
                            try {
                                $riwayatStmt = $pdo->prepare("
                                SELECT k.nama, k.usia, k.jenisKelamin, k.keluhan, k.riwayatKeluarga, k.created_at, hk.saranPenanganan
                                FROM konsultasi k
                                LEFT JOIN hasilKonsultasi hk ON hk.konsultasiId = k.konsultasiId
                                WHERE k.userId = ?
                                ORDER BY k.created_at DESC
                            ");
                                $riwayatStmt->execute([$userId]);
                                $riwayatList = $riwayatStmt->fetchAll(PDO::FETCH_ASSOC);

                                if ($riwayatList) {
                                    foreach ($riwayatList as $row) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['usia']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['jenisKelamin']) . "</td>";
                                        echo "<td>" . date('d M Y', strtotime($row['created_at'])) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['keluhan']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['riwayatKeluarga'] ?? '-') . "</td>";
                                        echo "<td class='hasil-konsultasi'>" . nl2br(htmlspecialchars($row['saranPenanganan'] ?? 'Belum ada hasil')) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7'>Belum ada riwayat konsultasi.</td></tr>";
                                }
                            } catch (PDOException $e) {
                                echo "<tr><td colspan='7'>Gagal memuat riwayat: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>Silakan login untuk melihat riwayat.</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer akan selalu di bawah -->
    <?php include("footer.php"); ?>
</div>

<script>
    function showTab(tabId, buttonElement) {
        // Hide all tab contents
        var tabs = document.querySelectorAll('.tab-content');
        tabs.forEach(function(tab) {
            tab.style.display = 'none';
        });

        // Remove active class from all buttons
        var buttons = document.querySelectorAll('.menu-btn');
        buttons.forEach(function(btn) {
            btn.classList.remove('active');
        });

        // Show selected tab and mark button as active
        document.getElementById(tabId).style.display = 'block';
        buttonElement.classList.add('active');
    }
</script>

<script>
    if (window.location.search.includes("status=") || window.location.search.includes("sukses=")) {
        window.history.replaceState({}, document.title, window.location.pathname);
    }
</script>

<?php if (isset($_GET['sukses']) && $_GET['sukses'] == 1): ?>
    <?php
    $modalQuery = $pdo->prepare("
    SELECT hk.saranPenanganan
    FROM hasilKonsultasi hk
    JOIN konsultasi k ON hk.konsultasiId = k.konsultasiId
    WHERE k.userId = ?
    ORDER BY hk.hasilKonsultasiId DESC
    LIMIT 1
");
    $modalQuery->execute([$userId]);
    $modalData = $modalQuery->fetch(PDO::FETCH_ASSOC);
    if ($modalData):
        ?>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const hasilText = `<?= nl2br(htmlspecialchars($modalData['saranPenanganan'])) ?>`;
                document.getElementById("hasilKonsultasiText").innerHTML = hasilText;
                document.getElementById("hasilModal").style.display = "flex";

                // Hapus parameter ?sukses dari URL
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        </script>
    <?php endif; ?>
<?php endif; ?>

<!-- Modal hasil konsultasi -->
<div id="hasilModal" class="modal-konsultasi no-anim" style="display: none;">
    <div class="modal-content-konsultasi">
        <h3>🩺 Hasil Konsultasi Anda</h3>
        <div class="modal-body-konsultasi">
            <p id="hasilKonsultasiText"></p>
            <p class="note">📁 Hasil ini juga dapat dilihat kembali di menu <strong>Riwayat Konsultasi</strong>.</p>
        </div>
        <button class="close-btn-konsultasi" onclick="closeModal()">Tutup</button>
    </div>
</div>

</body>
</html>