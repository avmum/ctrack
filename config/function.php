<?php
// Fungsi untuk mengecek apakah user sudah login
function isLoggedIn() {
    return isset($_SESSION['user']);
}

// Fungsi untuk redirect ke login jika belum login
function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        echo "<script>alert('Silakan login terlebih dahulu.'); window.location.href='beranda.php';</script>";
        exit;
    }
}

// Fungsi untuk membersihkan input dari XSS atau whitespace
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Fungsi untuk mengambil data user lengkap dari DB
function getUserData($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE userId = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
