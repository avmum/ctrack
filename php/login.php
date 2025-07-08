<?php
session_start();
require_once '../config/Database.php';

$database = new Database();
$pdo = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        // Ambil data user dari database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifikasi password
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'userId' => $user['userId'],
                'nama' => $user['nama'],
                'email' => $user['email']
            ];

            // Jika checkbox "Ingat Saya" dicentang
            if (isset($_POST['remember'])) {
                setcookie('user_email', $email, time() + (86400 * 30), "/"); // 30 hari
            } else {
                // Jika tidak dicentang, hapus cookie (jika ada)
                if (isset($_COOKIE['user_email'])) {
                    setcookie('user_email', '', time() - 3600, "/");
                }
            }

            // Redirect setelah login berhasil
            header("Location: ../php/beranda.php");
            exit;
        } else {
            echo "<script>alert('Login gagal! Email atau password salah.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Harap isi email dan password!'); window.history.back();</script>";
    }
}
?>