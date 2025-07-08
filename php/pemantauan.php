<?php
$cssFile = "../css/pemantauanStyle.css";
include("header.php");

$database = new Database();
$pdo = $database->getConnection();

global $userId; // Ambil dari header.php jika sudah diset

// Cek apakah user sudah login, jika belum redirect (untuk keamanan ekstra)
if (!isset($userId)) {
    header("Location: beranda.php");
    exit;
}

// mendapatkan data gejala milik user dari DB
$stmt = $pdo->prepare("SELECT tglGejala, gejala, intensitas FROM gejalauser WHERE userId = ? ORDER BY tglGejala DESC");
$stmt->execute([$userId]);
$gejalaList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="page-wrapper">
    <main class="monitoring-section">
        <!-- Tombol Tambah Gejala -->
        <div style="text-align: right; margin-bottom: 1rem;">
            <button class="tambah" onclick="openForm()">+ Tambah Gejala</button>
        </div>

        <!-- Grafik Intensitas -->
        <section id="grafik-tab">
            <h2>Grafik Intensitas Gejala</h2>
            <canvas id="symptomChart" width="510" height="300"></canvas>
            <p class="chart-placeholder-text">Grafik Intensitas Gejala</p>
        </section>

        <!-- Histori Gejala -->
        <section id="history-tab">
            <h2>Histori Pemantauan Gejala</h2>
            <table class="history-table">
                <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Gejala</th>
                    <th>Intensitas</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($gejalaList)): ?>
                    <?php foreach ($gejalaList as $g): ?>
                        <tr>
                            <td><?= htmlspecialchars($g['tglGejala']) ?></td>
                            <td><?= htmlspecialchars($g['gejala']) ?></td>
                            <td><?= ucfirst(htmlspecialchars($g['intensitas'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3">Belum ada catatan gejala.</td></tr>
                <?php endif; ?>
                </tbody>

            </table>
        </section>

        <!-- Popup Form Gejala -->
        <div id="popupForm" class="popup-form" style="display: none;">
            <div class="form-box">
                <button class="button-close" onclick="closeForm()">×</button>
                <h1 class="monitoring-title">Tambah Catatan Gejala</h1>
                <form class="symptom-form" id="gejalaForm" method="POST">
                    <label for="tglGejala">Tanggal</label>
                    <input type="date" id="tglGejala" name="tglGejala" required />

                    <label for="gejala">Gejala yang Dirasakan</label>
                    <textarea id="gejala" name="gejala" rows="4" placeholder="Contoh: Nyeri dada, kelelahan..." required></textarea>

                    <label for="intensitas">Intensitas Gejala</label>
                    <select id="intensitas" name="intensitas" required>
                        <option value="">-- Pilih --</option>
                        <option value="ringan">Ringan</option>
                        <option value="sedang">Sedang</option>
                        <option value="berat">Berat</option>
                    </select>

                    <div class="btn-group">
                        <button type="reset" class="reset-btn">Reset</button>
                        <button type="submit" class="submit-btn">Simpan Catatan</button>
                    </div>

                    <div class="form-success" id="successMsg">✅ Catatan berhasil disimpan!</div>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php
    include("footer.php");
    ?>
</div>
</body>
</html>
