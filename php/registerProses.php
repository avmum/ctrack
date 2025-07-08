<?php
session_start();
require_once '../config/Database.php';

$database = new Database();
$pdo = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama           = $_POST['nama'];
    $email          = $_POST['email'];
    $password       = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $alamat         = $_POST['alamat'];
    $noHp           = $_POST['noHp'];
    $jenisKelamin   = $_POST['jenisKelamin'];
    $tglLahir       = $_POST['tglLahir'];
    $golDarah       = $_POST['golDarah'];
    $jenisPenjamin  = $_POST['jenisPenjamin'];

    $fotoProfilName = null;

    // ========== UPLOAD FOTO PROFIL ==========
    if (isset($_FILES['fotoProfil']) && $_FILES['fotoProfil']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['fotoProfil']['tmp_name'];
        $originalName = $_FILES['fotoProfil']['name'];
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($extension, $allowedExtensions)) {
            if (!file_exists('../uploads/')) {
                mkdir('../uploads/', 0777, true);
            }

            $fotoProfilName = 'user_' . time() . '.' . $extension;
            $uploadPath = '../uploads/' . $fotoProfilName;

            move_uploaded_file($tmpName, $uploadPath);
        } else {
            $_SESSION['error'] = "Format file tidak didukung. Gunakan JPG, PNG, atau GIF.";
            header("Location: beranda.php");
            exit;
        }
    }

    // ========== SIMPAN KE DATABASE ==========
    try {
        $sql = "INSERT INTO users 
                (nama, email, password, alamat, noHp, jenisKelamin, tglLahir, golDarah, jenisPenjamin, fotoProfil) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nama, $email, $password, $alamat, $noHp,
            $jenisKelamin, $tglLahir, $golDarah, $jenisPenjamin, $fotoProfilName
        ]);

        $_SESSION['register_success'] = true;
        header("Location: beranda.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = "Gagal mendaftar: " . $e->getMessage();
        header("Location: beranda.php");
        exit;
    }
}
?>
