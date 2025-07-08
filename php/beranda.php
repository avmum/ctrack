<?php
session_start();
require_once '../config/Database.php';
require_once '../config/function.php';

$database = new Database();
$pdo = $database->getConnection();

// Cek apakah user sudah login
$userData = null;
if (isset($_SESSION['user']) && isset($_SESSION['user']['userId'])) {
    $userId = $_SESSION['user']['userId'];
    $userData = getUserData($pdo, $userId);
}

// Cek cookie "Ingat Saya"
$remembered_email = '';
$is_remembered = false;

if (isset($_COOKIE['user_email'])) {
    $remembered_email = $_COOKIE['user_email'];
    $is_remembered    = true;
}


$showSuccessModal = false;
if (isset($_SESSION['register_success'])) {
    $showSuccessModal = true;
    unset($_SESSION['register_success']);
}

$query = "SELECT * FROM survivaljourney ORDER BY tglUpload DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = count($videos);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>C-track</title>
  <link rel="stylesheet" href="../css/berandaStyle.css" />
  <link rel="icon" href="../assets/logo.png" type="image/png">
  <script src="../javascript/berandaScript.js"></script>
</head>
<body <?= $showSuccessModal ? 'data-register-success="true"' : '' ?>>
<div class="page-wrapper">
    <header>
        <!-- Logo -->
        <div class="logo">
            <img src="../assets/logo.png" alt="Logo" class="logo-img" />
            <div class="logo-text">
                <span class="logo-title">Cancer Tracking</span>
                <span class="logo-slogan">Memantau Kesehatan, Menjaga Harapan</span>
            </div>
        </div>

        <!-- Burger Menu (Mobile) -->
        <div class="burger" id="burger">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <!-- Navigasi -->
        <nav>
            <ul class="nav-links">
                <li><a href="#">Beranda</a></li>
                <li class="dropdown">
                    <?php if (isset($_SESSION['user'])): ?>
                        <!-- Jika sudah login, menu aktif seperti biasa -->
                        <a href="#" class="dropdown-toggle" id="trackMenu">Track</a>
                        <ul class="dropdown-content">
                            <li><a href="pemantauan.php">Pemantauan Gejala</a></li>
                            <li><a href="konsultasi.php">Konsultasi Awal</a></li>
                            <li><a href="perawatan.php">Manajemen Perawatan</a></li>
                            <li><a href="rs.php">Rumah Sakit Kanker</a></li>
                        </ul>
                    <?php else: ?>
                        <!-- Jika belum login, munculkan alert -->
                        <a href="#" class="dropdown-toggle" onclick="showLoginPrompt()">Track</a>
                    <?php endif; ?>
                </li>
                <li><a href="beranda.php#infoContainer">What's a Cancer</a></li>
                <li><a href="beranda.php#survivalJourney">Survival Journey</a></li>
                <li><a href="tentang.php">About Us</a></li>
                <?php if (isset($_SESSION['user'])): ?>
                    <li><a href="#" class="profile-mobile" onclick="openProfilCard()">Profil</a></li>
                <?php else: ?>
                    <li><a href="#" class="login-mobile" onclick="openLogin()">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <?php if (isset($_SESSION['user'])): ?>
            <button class="profil-btn" onclick="openProfilCard()">Profil</button>
        <?php else: ?>
            <button class="login-btn" onclick="openLogin()">Login</button>
        <?php endif; ?>

    </header>

    <!-- Modal Login Dulu Buat pop-up nya kalau belum login tapi user klik menu track -->
    <div id="loginPromptModal" class="modalLoginPrompt">
        <div class="modal-content-login">
            <h3>Akses Ditolak</h3>
            <p>Silakan login terlebih dahulu untuk mengakses fitur Track.</p>
            <div class="modal-buttons">
                <button onclick="openLogin()">Login Sekarang</button>
                <button onclick="closeLoginPrompt()">Nanti Saja</button>
            </div>
        </div>
    </div>

    <!-- Modal Login Card -->
    <div class="login-modal" id="loginCard" style="display: none;">
        <div class="login-card">
            <span class="close-btn" onclick="closeLogin()">&times;</span>
            <h2>Login ke C-Track</h2>
            <form action="login.php" method="POST" id="loginForm">
                <input type="text" id="emailLogin" placeholder="Email" required name="email"
                       value="<?= isset($_COOKIE['user_email']) ? $_COOKIE['user_email'] : '' ?>" />
                <span class="error" id="emailLoginError"></span>

                <input type="password" id="passwordLogin" placeholder="Kata Sandi" required name="password" />
                <span class="error" id="passwordLoginError"></span>

                <label style="color: #6a1b9a; display: flex; align-items: center; gap: 6px; margin: 8px 5px 12px 5px;">
                    <input type="checkbox" name="remember" <?= isset($_COOKIE['user_email']) ? 'checked' : '' ?> />
                    Ingat Saya
                </label>

                <button type="submit">Masuk</button>
            </form>
            <p class="register-text">Belum punya akun? <a href="#" onclick="openRegister()">Daftar di sini</a></p>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="register-modal" id="registerCard" style="display: none;">
        <div class="register-card">
            <span class="close-btn" onclick="closeRegister()">&times;</span>
            <h2>Daftar Member C-Track</h2>
            <form action="registerProses.php" method="POST" enctype="multipart/form-data" id="registerForm">
                <input type="text" id="nama" name="nama" placeholder="Nama Lengkap" required />
                <span class="error" id="namaError"></span>

                <input type="email" id="email" name="email" placeholder="Email" required />
                <span class="error" id="emailError"></span>

                <input type="password" id="password" name="password" placeholder="Password" required />
                <span class="error" id="passwordError"></span>

                <input type="password" id="konfirmasiPassword" name="konfirmasiPassword" placeholder="Konfirmasi Password" required />
                <span class="error" id="konfpwddError"></span>

                <textarea id="alamat" name="alamat" placeholder="Alamat" required></textarea>
                <span class="error" id="alamatError"></span>

                <input type="tel" id="nomorHP" name="noHp" placeholder="Nomor HP" required />
                <span class="error" id="nomorHPError"></span>

                <select id="jenisKelamin" name="jenisKelamin" required>
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
                <span class="error" id="jkError"></span>

                <input type="date" id="tanggalLahir" name="tglLahir" required />
                <span class="error" id="tglLahirError"></span>

                <select id="golonganDarah" name="golDarah" required>
                    <option value="">Golongan Darah</option>
                    <option>A</option>
                    <option>B</option>
                    <option>AB</option>
                    <option>O</option>
                </select>
                <span class="error" id="golDarError"></span>

                <select id="jenisPenjamin" name="jenisPenjamin" required>
                    <option value="">Jenis Penjamin</option>
                    <option>BPJS</option>
                    <option>Asuransi</option>
                    <option>Mandiri</option>
                </select>
                <span class="error" id="jpError"></span>

                <!-- Foto Profil -->
                <label for="fotoProfilRegister" class="custom-file-label">Pilih Foto Profil</label>
                <input type="file" id="fotoProfilRegister" name="fotoProfil" accept="image/*" onchange="previewFotoProfil(event)" hidden />
                <div style="margin-top: 10px;">
                    <img id="previewFotoRegister" class="preview-img" src="" alt="Preview Foto" style="display: none;" />
                </div>
                <span class="error" id="fotoProfilError"></span>

                <div class="form-buttons">
                    <button type="reset" onclick="resetForm()">Reset</button>
                    <button type="submit">Daftar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ketika registrasi berhasil -->
    <div id="registerSuccessModal" class="modalLoginPrompt" style="display: none;">
        <div class="modal-content-login">
            <h3>Pendaftaran Berhasil</h3>
            <p>Akunmu berhasil dibuat! Silakan login untuk mulai menggunakan C-Track.</p>
            <div class="modal-buttons">
                <button onclick="openLogin()">Login Sekarang</button>
                <button onclick="closeRegisterSuccessModal()">Nanti Saja</button>
            </div>
        </div>
    </div>

    <section class="hero">
        <div class="hero-content">
            <h1>Selamat Datang di C-Track</h1>
            <p>Platform pemantauan dan informasi seputar kanker untuk mendampingi Anda menjaga kesehatan dan harapan.</p>
        </div>
    </section>

    <div class="info-container" id="infoContainer">
        <h2 class="info-title">Pelajari tentang Kanker</h2>
        <h4 class="info-subtitle">Kenali Lebih Dini<br /></h4>
        <div class="info-sections">
            <a href="whatC.php" class="section">
                <h2>Apa itu Kanker?</h2>
                <p>Kanker adalah penyakit yang ditandai dengan pertumbuhan sel yang tidak terkendali dalam tubuh. Sel kanker dapat menyebar ke jaringan dan organ lain.</p>
            </a>

            <a href="pencegahan.php" class="section">
                <h2>Pencegahan</h2>
                <p>Kanker merupakan istilah untuk suatu penyakit ketika sel-sel abnormal dalam tubuh berkembang biak secara tidak terkendali dan dapat merusak jaringan di sekitarnya. Sel-sel ini juga bisa menyebar ke area tubuh lainnya melalui aliran darah dan sistem getah bening.</p>
            </a>

            <a href="faktorisiko.php" class="section">
                <h2>Faktor Risiko</h2>
                <p>Risiko terkena kanker meningkat seiring bertambahnya usia. Lebih dari setengah kasus kanker biasanya terjadi pada orang yang berusia di atas 60 tahun. Dengan kata lain, semakin tua usia seseorang, semakin besar kemungkinan terserang penyakit ini.</p>
            </a>

            <a href="jeniskanker.php" class="section">
                <h2>Jenis-jenis Kanker</h2>
                <p>
                    KANKER HATI
                    Merupakan jenis tumor ganas yang berkembang di organ hati. Sekitar 90% pasien kanker hati baru memeriksakan diri ke dokter saat sudah memasuki stadium lanjut, sehingga pengobatan menjadi lebih sulit.
                    <br />Apa itu Kanker Hati?
                </p>
            </a>
        </div>
    </div>

    <div class="wave-divider-top">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="gradient-top" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="rgba(241, 7, 163, 0.8)" />
                    <stop offset="100%" stop-color="rgba(123, 47, 247, 0.8)" />
                </linearGradient>
            </defs>
            <path d="M0,0 C480,120 960,0 1440,120 L1440,0 L0,0 Z" fill="url(#gradient-top)"></path>
        </svg>
    </div>

    <section class="stories-section" id="survivalJourney">
        <h2 class="stories-title">Survival Journey</h2>
        <div class="stories-container">
            <?php foreach (array_slice($videos, 0, 3) as $video): ?>
                <div class="story-card">
                    <div class="video-wrapper">
                        <iframe src="<?= htmlspecialchars($video['urlVideo']) ?>" title="<?= htmlspecialchars($video['judul']) ?>" allowfullscreen></iframe>
                    </div>
                    <div class="story-card-content">
                        <h3><?= htmlspecialchars($video['judul']) ?></h3>
                        <p><?= htmlspecialchars($video['deskripsi']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (count($videos) > 3): ?>
            <div id="more-videos" style="display: none;" class="stories-container">
                <?php foreach (array_slice($videos, 3) as $video): ?>
                    <div class="story-card">
                        <div class="video-wrapper">
                            <iframe src="<?= htmlspecialchars($video['urlVideo']) ?>" title="<?= htmlspecialchars($video['judul']) ?>" allowfullscreen></iframe>
                        </div>
                        <div class="story-card-content">
                            <h3><?= htmlspecialchars($video['judul']) ?></h3>
                            <p><?= htmlspecialchars($video['deskripsi']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="see-more-container">
                <button id="showMoreButton" class="see-more-button" onclick="showMoreVideos()">Lihat Video Lainnya</button>
            </div>
        <?php endif; ?>
    </section>

    <div class="wave-divider-buttom">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="gradient-buttom" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="rgba(123, 47, 247, 0.8)" />
                    <stop offset="100%" stop-color="rgba(241, 7, 163, 0.8)" />
                </linearGradient>
            </defs>
            <path d="M0,0 C480,120 960,0 1440,120 L1440,0 L0,0 Z" fill="url(#gradient-buttom)"></path>
        </svg>
    </div>


    <?php
    include("footer.php");
    ?>

    <?php if ($userData): ?>
        <div class="profil-card" id="profilCard">
            <div class="card-content">
                <div class="avatar-container">
                    <img src="../uploads/<?= htmlspecialchars($userData['fotoProfil']) ?>" alt="Avatar" class="avatar" />
                    <h3>Profil Pengguna</h3>
                </div>

                <div class="profil-info">
                    <table class="profil-table">
                        <tr><td>Nama</td><td>: <?= htmlspecialchars($userData['nama']) ?></td></tr>
                        <tr><td>Email</td><td>: <?= htmlspecialchars($userData['email']) ?></td></tr>
                        <tr><td>Tanggal Lahir</td><td>: <?= htmlspecialchars($userData['tglLahir']) ?></td></tr>
                        <tr><td>Jenis Kelamin</td><td>: <?= htmlspecialchars($userData['jenisKelamin']) ?></td></tr>
                        <tr><td>No. HP</td><td>: <?= htmlspecialchars($userData['noHp'] ?? '-') ?></td></tr>
                        <tr><td>Alamat</td><td>: <?= htmlspecialchars($userData['alamat']) ?></td></tr>
                        <tr><td>Golongan Darah</td><td>: <?= htmlspecialchars($userData['golDarah']) ?></td></tr>
                    </table>
                </div>

                <div class="button-group">
                    <button class="logout-btn" onclick="logout()">Logout</button>
                    <button class="edit-btn" onclick="openEditProfil()">Edit Profil</button>
                    <button class="tutup-btn" onclick="closeProfilCard()">Tutup</button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($userData): ?>
        <div class="modal" id="editProfilModal">
            <div class="modal-content">
                <span class="close" onclick="closeEditProfil()">&times;</span>
                <h2>Edit Profil</h2>
                <form id="editProfilForm" action="updateProfil.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">

                        <!-- Data User -->
                        <input id="editNama" type="text" name="nama" value="<?= $userData['nama'] ?>" placeholder="Nama Lengkap" required />
                        <span class="error" id="editnamaError"></span>

                        <input id="editEmail" type="email" name="email" value="<?= $userData['email'] ?>" placeholder="Email" required />
                        <span class="error" id="editemailError"></span>

                        <input id="editTglLahir" type="date" name="tglLahir" value="<?= $userData['tglLahir'] ?>" required />
                        <span class="error" id="edittglLahirError"></span>

                        <select id="editjenisKelamin" name="jenisKelamin" required>
                            <option value="Perempuan" <?= $userData['jenisKelamin'] === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                            <option value="Laki-laki" <?= $userData['jenisKelamin'] === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                        </select>
                        <span class="error" id="editjkError"></span>

                        <input id="EditNoHp" type="tel" name="noHp" value="<?= $userData['noHp'] ?>" placeholder="Nomor HP" required />
                        <span class="error" id="editnoHpError"></span>

                        <textarea id="editAlamat" name="alamat" placeholder="Alamat"><?= $userData['alamat'] ?></textarea>
                        <span class="error" id="editalamatError"></span>

                        <select id="EditGolDarah" name="golDarah" required>
                            <option value="A" <?= $userData['golDarah'] === 'A' ? 'selected' : '' ?>>A</option>
                            <option value="B" <?= $userData['golDarah'] === 'B' ? 'selected' : '' ?>>B</option>
                            <option value="AB" <?= $userData['golDarah'] === 'AB' ? 'selected' : '' ?>>AB</option>
                            <option value="O" <?= $userData['golDarah'] === 'O' ? 'selected' : '' ?>>O</option>
                        </select>
                        <span class="error" id="editgolDarahError"></span>

                        <select id="editJenisPenjamin" name="jenisPenjamin" required>
                            <option value="">Pilih Jenis Penjamin</option>
                            <option value="BPJS" <?= $userData['jenisPenjamin'] === 'BPJS' ? 'selected' : '' ?>>BPJS</option>
                            <option value="Asuransi" <?= $userData['jenisPenjamin'] === 'Asuransi' ? 'selected' : '' ?>>Asuransi</option>
                            <option value="Mandiri" <?= $userData['jenisPenjamin'] === 'Mandiri' ? 'selected' : '' ?>>Mandiri</option>
                        </select>
                        <span class="error" id="editjpError"></span>


                        <!-- Foto Profil -->
                        <label for="editFotoProfilEdit" class="custom-file-label">Pilih Foto Profil</label>
                        <input type="file" id="editFotoProfilEdit" name="fotoProfil" accept="image/*" onchange="previewFotoProfil(event)" hidden />
                        <div style="margin-top: 10px;">
                            <img id="previewFotoEdit" class="preview-img" src="" alt="Preview Foto" style="display: none;" />
                        </div>
                        <span class="error" id="editfotoProfilError"></span>
                    </div>

                    <input type="hidden" name="redirect_url" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">


                    <div class="modal-actions">
                        <button type="submit" class="save-btn">Simpan</button>
                        <button type="button" class="changepw-btn" onclick="openPasswordModal()">Ganti Password</button>
                        <button type="button" class="cancel-btn" onclick="closeEditProfil()">Batal</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Ganti Password -->
        <div class="modal" id="changePasswordModal" style="display: none;">
            <div class="modal-content">
                <span class="close" onclick="closePasswordModal()">&times;</span>
                <h2>Ganti Password</h2>
                <form id="changePasswordForm" action="changePW.php" method="POST">
                    <div class="modal-body">
                        <input id="oldPassword" type="password" name="passwordLama" placeholder="Password Lama" required />
                        <span class="error" id="oldPasswordError"></span>
                        <input id="newPassword" type="password" name="passwordBaru" placeholder="Password Baru" required />
                        <span class="error" id="newPasswordError"></span>
                        <input id="confirmNewPassword" type="password" name="konfirmasiPassword" placeholder="Konfirmasi Password Baru" required />
                        <span class="error" id="confirmNewPasswordError"></span>
                    </div>
                    <input type="hidden" name="redirect_url" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                    <div class="modal-actions">
                        <button type="submit" class="save-btn">Ubah Password</button>
                        <button type="button" class="cancel-btn" onclick="closePasswordModal()">Batal</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Logout -->
        <div id="logoutModal" class="modalLogout">
            <div class="modal-content-logout">
                <span class="close-btn" onclick="closeLogoutModal()">&times;</span>
                <h3>Konfirmasi Logout</h3>
                <p>Apakah kamu yakin ingin logout?</p>
                <div class="modal-buttons">
                    <button onclick="confirmLogout()">Ya, Logout</button>
                    <button onclick="closeLogoutModal()">Batal</button>
                </div>
            </div>
        </div>

    <?php endif; ?>

    <?php if (isset($_SESSION['popup_berhasil'])): ?>
        <div id="popupBerhasil" class="popup-berhasil show">
            <div class="popup-content">
                <span class="close-popup" onclick="tutupPopupBerhasil()">&times;</span>
                <p> ✅ Profil berhasil diperbarui!</p>
            </div>
        </div>
        <?php unset($_SESSION['popup_berhasil']); ?>
    <?php elseif (!empty($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8'); ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
</div>
</body>
</html>
