<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$database = new Database();
$pdo = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = $_SESSION['user']['userId'];
    $fotoName = null;
    $uploadSuccess = true;
    $errorMessage = '';

    // Ambil data dari form
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $tglLahir = $_POST['tglLahir'];
    $jenisKelamin = $_POST['jenisKelamin'];
    $noHp = $_POST['noHp'];
    $alamat = $_POST['alamat'];
    $golDarah = $_POST['golDarah'];
    $jenisPenjamin = $_POST['jenisPenjamin'];
    $passwordBaru = $_POST['passwordBaru'] ?? null;

    // ✔️ Upload foto jika ada
    if (isset($_FILES['fotoProfil']) && $_FILES['fotoProfil']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['fotoProfil']['tmp_name'];
        $originalName = $_FILES['fotoProfil']['name'];
        $fileSize = $_FILES['fotoProfil']['size'];
        $fileType = $_FILES['fotoProfil']['type'];

        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($fileType, $allowedTypes) || !in_array($extension, $allowedExtensions)) {
            $uploadSuccess = false;
            $errorMessage = 'Tipe file tidak diizinkan. Hanya JPEG, PNG, dan GIF.';
        }

        if ($fileSize > 2 * 1024 * 1024) {
            $uploadSuccess = false;
            $errorMessage = 'Ukuran file maksimal 2MB.';
        }

        if ($uploadSuccess) {
            $fotoName = 'user_' . $userId . '_' . time() . '.' . $extension;
            $uploadPath = '../uploads/' . $fotoName;

            if (!file_exists('../uploads/')) {
                mkdir('../uploads/', 0777, true);
            }

            // Hapus foto lama
            if (!empty($_SESSION['user']['fotoProfil'])) {
                $oldPath = '../uploads/' . $_SESSION['user']['fotoProfil'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            if (!move_uploaded_file($tmpName, $uploadPath)) {
                $uploadSuccess = false;
                $errorMessage = 'Gagal menyimpan foto profil.';
            }
        }
    }

    if ($uploadSuccess) {
        try {
            $sql = "UPDATE users SET nama=?, email=?, tglLahir=?, jenisKelamin=?, noHp=?, alamat=?, golDarah=?, jenisPenjamin=?";
            $params = [$nama, $email, $tglLahir, $jenisKelamin, $noHp, $alamat, $golDarah, $jenisPenjamin];

            if ($fotoName !== null) {
                $sql .= ", fotoProfil=?";
                $params[] = $fotoName;
            }

            $sql .= " WHERE userId=?";
            $params[] = $userId;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            // ✔️ Update password jika diisi
            if (!empty($passwordBaru)) {
                $hashed = password_hash($passwordBaru, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE users SET password=? WHERE userId=?")
                    ->execute([$hashed, $userId]);
            }

            // ✔️ Update session
            $_SESSION['user']['nama'] = $nama;
            $_SESSION['user']['email'] = $email;
            $_SESSION['user']['tglLahir'] = $tglLahir;
            $_SESSION['user']['jenisKelamin'] = $jenisKelamin;
            $_SESSION['user']['noHp'] = $noHp;
            $_SESSION['user']['alamat'] = $alamat;
            $_SESSION['user']['golDarah'] = $golDarah;
            $_SESSION['user']['jenisPenjamin'] = $jenisPenjamin;
            if ($fotoName !== null) {
                $_SESSION['user']['fotoProfil'] = $fotoName;
            }

            $redirect = $_POST['redirect_url'] ?? 'beranda.php';
            $_SESSION['popup_berhasil'] = true;
            header("Location: " . $redirect);
            exit;


        } catch (PDOException $e) {
            $errorMessage = 'Kesalahan database: ' . $e->getMessage();
        }
    }

    // Gagal
    $_SESSION['error_message'] = $errorMessage ?: 'Gagal memperbarui profil.';
    header("Location: beranda.php?error=1");
    exit;
}
?>
