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

$showSuccessModal = false;
if (isset($_SESSION['register_success'])) {
    $showSuccessModal = true;
    unset($_SESSION['register_success']); // Hapus langsung setelah ditampilkan
}
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>C-track</title>
        <link rel="stylesheet" href="<?= $cssFile ?>?v=<?= time() ?>">
        <link rel="icon" href="../assets/logo.png" type="image/png">
        <script src="../javascript/berandaScript.js"></script>
        <style>
            /* Header */
            header {
                display: flex;
                width: 100%;
                justify-content: space-between;
                position: relative;
                align-items: center;
                background: linear-gradient(135deg, #9b5de5, #f15bb5);
                padding: 20px 40px;
                color: white;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                top: 0;
                z-index: 1000;
                border-bottom-left-radius: 25px;
                border-bottom-right-radius: 25px;
                animation: slideDown 0.8s ease-out;
            }

            @keyframes slideDown {
                from {
                    transform: translateY(-40px);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            .logo {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .logo-img {
                width: 50px;
                height: 50px;
                animation: rotateLogo 5s infinite linear;
            }

            @keyframes rotateLogo {
                0%, 100% { transform: rotate(0deg); }
                50% { transform: rotate(2deg); }
            }

            .logo-text {
                display: flex;
                flex-direction: column;
                line-height: 1.2;
            }

            .logo-title {
                font-size: 1.6em;
                font-weight: bold;
                color: white;
            }

            .logo-slogan {
                font-size: 0.85em;
                color: #fcddec;
                font-style: italic;
            }

            /* Navigation */
            .nav-links {
                list-style: none;
                display: flex;
                gap: 25px;
                align-items: center;
            }

            .nav-links a {
                text-decoration: none;
                color: white;
                font-weight: 500;
                padding: 8px 14px;
                border-radius: 8px;
                transition: background 0.3s, transform 0.2s;
            }

            .nav-links a:hover {
                background-color: #521899;
                transform: scale(1.05);
            }

            /* Dropdown */
            .dropdown {
                position: relative;
            }

            .dropdown-content {
                display: none;
                position: absolute;
                background-color: #fff0f6;
                min-width: 220px;
                z-index: 1000;
                list-style: none;
                padding: 10px 0;
                top: 120%;
                left: 0;
                border-radius: 8px;
                box-shadow: 0 8px 16px rgba(0,0,0,0.15);
            }

            .dropdown-content li a {
                display: block;
                padding: 10px 20px;
                color: #6a0572;
                text-decoration: none;
                transition: background-color 0.3s;
            }

            .dropdown-content li a:hover {
                background-color: #e8b7ea;
            }

            /* Tombol Login */
            .login-btn {
                background-color: #f15bb5;
                border: none;
                color: white;
                padding: 10px 20px;
                cursor: pointer;
                border-radius: 12px;
                font-weight: bold;
                box-shadow: 0 3px 8px rgba(0,0,0,0.2);
                transition: background 0.3s, transform 0.2s;
            }

            .login-btn:hover {
                background-color: #521899;
                transform: scale(1.05);
            }

            /* Animasi dropdown */
            @keyframes fadeSlide {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Modal background */
            .login-modal {
                display: none;
                position: fixed;
                z-index: 1001;
                left: 0; top: 0;
                width: 100%; height: 100%;
                background-color: rgba(0,0,0,0.4);
                backdrop-filter: blur(4px);
                justify-content: center;
                align-items: center;
            }

            .login-modal h2{
                color: #6b21a8;
                margin-bottom: 10px;
                font-weight: bold;
                font-size: 1.5rem;
                text-align: center;
            }

            /* Card Login */
            .login-card {
                background: #fff0fb;
                border-radius: 16px;
                padding: 30px;
                width: 90%;
                max-width: 400px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.2);
                position: relative;
            }

            /* Close button */
            .close-btn {
                position: absolute;
                top: 10px;
                right: 16px;
                font-size: 24px;
                cursor: pointer;
                color: #888;
            }
            .close-btn:hover {
                color: #c77dff;
            }

            /* Form input */
            .login-card input[type="text"],
            .login-card input[type="password"] {
                width: 100%;
                padding: 12px;
                margin: 12px 0;
                border: 1px solid #ddd;
                border-radius: 10px;
                background: #fff;
                font-size: 14px;
            }

            /* Card login */
            .login-card button {
                width: 100%;
                background-color: #da77f2;
                color: white;
                padding: 12px;
                border: none;
                border-radius: 10px;
                font-weight: bold;
                cursor: pointer;
            }
            .login-card button:hover {
                background-color: #783cda;
            }

            /* forgot password*/
            .forgot-password-modal {
                position: fixed;
                top: 0; left: 0;
                width: 100%; height: 100%;
                background-color: rgba(0, 0, 0, 0.4);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 2000;
                backdrop-filter: blur(4px);
            }

            .forgot-password-card {
                background-color: #fff;
                padding: 32px 24px;
                border-radius: 16px;
                box-shadow: 0 8px 20px rgba(170, 50, 220, 0.2);
                max-width: 400px;
                width: 90%;
                text-align: center;
                position: relative;
            }

            .forgot-password-card h2 {
                color: #a64ac9;
                font-size: 22px;
                margin-bottom: 20px;
            }

            .forgot-password-card input[type="email"] {
                width: 100%;
                padding: 12px;
                margin-bottom: 10px;
                border: 1px solid #d1c4e9;
                border-radius: 8px;
                background-color: #f8f3ff;
            }

            .forgot-password-card input[type="email"]:focus {
                outline: none;
                border-color: #ba68c8;
                background-color: #fff;
            }

            .forgot-password-card .error {
                font-size: 12px;
                color: red;
                margin-bottom: 10px;
                display: block;
            }

            .forgot-password-card button[type="submit"] {
                background-color: #ba68c8;
                color: #fff;
                padding: 12px 20px;
                border: none;
                border-radius: 8px;
                font-weight: bold;
                cursor: pointer;
            }

            .forgot-password-card button[type="submit"]:hover {
                background-color: #9c27b0;
            }

            .close-btn {
                position: absolute;
                top: 10px;
                right: 16px;
                font-size: 24px;
                cursor: pointer;
                color: #888;
            }

            .close-btn:hover {
                color: #c77dff;
            }

            .back-login-text {
                margin-top: 15px;
                font-size: 14px;
            }

            /* Teks bawah */
            .register-text {
                text-align: center;
                margin-top: 15px;
                font-size: 14px;
            }
            .register-text a {
                color: #b75ce5;
                text-decoration: none;
                font-weight: 600;
            }
            .register-text a:hover {
                text-decoration: underline;
            }

            /* Form Registrasi*/
            .register-modal {
                display: none;
                position: fixed;
                z-index: 9999;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0,0,0,0.4);
                backdrop-filter: blur(4px);
                justify-content: center;
                align-items: center;
            }

            .register-card {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: #fff0fb;
                padding: 40px 30px;
                border-radius: 20px;
                box-shadow: 0 10px 30px rgba(155, 93, 229, 0.25);
                width: 90%;
                max-width: 420px;
                z-index: 10000;
                font-family: 'Segoe UI', sans-serif;
                animation: fadeIn 0.4s ease-in-out;
                overflow-y: auto;
                max-height: 95vh;
            }

            /* Judul */
            .register-card h2 {
                color: #7b2cbf;
                font-size: 1.7rem;
                text-align: center;
                font-weight: 700;
                margin-bottom: 25px;
            }

            /* Close Button */
            .register-card .close-btn {
                position: absolute;
                top: 18px;
                right: 20px;
                font-size: 24px;
                color: #b75ce5;
                cursor: pointer;
                transition: color 0.3s ease;
            }

            .register-card .close-btn:hover {
                color: #7b2cbf;
            }

            /* Form Input dan Select */
            .register-card form input,
            .register-card form select {
                width: 100%;
                padding: 12px 14px;
                margin-bottom: 10px;
                border-radius: 10px;
                border: 1.5px solid #d8b4ef;
                font-size: 15px;
                background-color: #fff;
                transition: all 0.3s ease;
                box-shadow: inset 0 0 0 transparent;
            }

            .register-card form input:focus,
            .register-card form select:focus {
                border-color: #c77dff;
                outline: none;
                box-shadow: 0 0 6px rgba(199, 125, 255, 0.3);
            }

            .register-card form textarea {
                width: 100%;
                height: 100px;
                padding: 12px 14px;
                margin-bottom: 10px;
                border-radius: 10px;
                border: 1.5px solid #d8b4ef;
                font-size: 15px;
                background-color: #fff;
                transition: all 0.3s ease;
                box-shadow: inset 0 0 0 transparent;
            }

            .register-card form textarea:focus {
                border-color: #c77dff;
                outline: none;
                box-shadow: 0 0 6px rgba(199, 125, 255, 0.3);
            }

            /* Tombol Submit dan Reset */
            .register-card .form-buttons {
                display: flex;
                justify-content: space-between;
                gap: 14px;
                margin-top: 20px;
            }

            /* Tombol umum */
            .register-card .form-buttons button {
                flex: 1;
                padding: 12px 0;
                border: none;
                border-radius: 10px;
                font-weight: bold;
                font-size: 1rem;
                cursor: pointer;
                color: #fff;
                transition: transform 0.2s, background 0.3s;
                box-shadow: 0 4px 12px rgba(180, 80, 220, 0.3);
                overflow-y: scroll;
                scrollbar-width: none;
                -ms-overflow-style: none;
            }

            /* Tombol submit: Ungu-pink */
            .register-card .form-buttons button[type="submit"] {
                background: linear-gradient(to right, #ba5aff, #8e44e9);
            }

            /* Tombol reset: Pink-lembut */
            .register-card .form-buttons button[type="reset"] {
                background: linear-gradient(to right, #ff9aa2, #ffb7b2);
            }

            .register-card .form-buttons button[type="submit"]:hover {
                transform: scale(1.05);
                background: linear-gradient(to right, #b55fff, #4b004b);
            }

            .register-card .form-buttons button[type="reset"]:hover {
                transform: scale(1.05);
                background: linear-gradient(to right, #ee6a75, #df4343);
            }

            .custom-file-label {
                display: inline-block;
                width: 100%;
                padding: 12px 14px;
                margin-bottom: 10px;
                border-radius: 10px;
                border: 2px dashed #d8b4ef;
                background: linear-gradient(to right, #f3d9fa, #f9e0ff);
                color: #7b2cbf;
                font-weight: 600;
                text-align: center;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            /* Link Login */
            .register-card p {
                margin-top: 16px;
                font-size: 14px;
                text-align: center;
                color: #555;
            }

            .register-card a {
                color: #9b5de5;
                font-weight: 600;
                text-decoration: none;
            }

            .register-card a:hover {
                text-decoration: underline;
            }

            /* Fade In Animation */
            @keyframes fadeIn {
                from { opacity: 0; transform: translate(-50%, -55%); }
                to { opacity: 1; transform: translate(-50%, -50%); }
            }

            .register-card::-webkit-scrollbar {
                display: none; /* Chrome, Safari, Opera */
            }

            .error {
                display: block;
                margin-bottom: 10px;
                color: #d32f2f; /* warna merah muda gelap */
                font-size: 14px;
                font-style: italic;
                padding-left: 4px;
                transition: opacity 0.3s ease;
            }

            /*=======Agar Responsive ========*/
            /* Burger Menu */
            .burger {
                display: none;
                flex-direction: column;
                justify-content: space-between;
                width: 30px;
                height: 22px;
                cursor: pointer;
                z-index: 1002;
                transition: transform 0.3s ease;
                margin-left: auto;
            }

            .burger span {
                display: block;
                height: 3px;
                background: #fff;
                border-radius: 4px;
                transition: all 0.3s ease-in-out;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
                transform-origin: center;
            }

            /* Burger Animation when active */
            .burger.active span:nth-child(1) {
                transform: rotate(45deg) translate(5px, 5px);
            }

            .burger.active span:nth-child(2) {
                opacity: 0;
                transform: scale(0);
            }

            .burger.active span:nth-child(3) {
                transform: rotate(-45deg) translate(6px, -6px);
            }

            /* Animasi slide dari kiri ke kanan */
            @keyframes slideInFromLeft {
                from {
                    opacity: 0;
                    transform: translateX(-100%);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            /* Overlay untuk burger menu */
            .nav-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.4);
                z-index: 1000;
                backdrop-filter: blur(3px);
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .nav-overlay.show {
                display: block;
                opacity: 1;
            }

            /* Update responsive navigation untuk slide dari kiri */
            @media (max-width: 1118px) {
                .burger {
                    display: flex;
                    position: relative;
                    z-index: 1003; /* Pastikan burger button di atas overlay */
                }

                /* Prevent body scroll when menu is open */
                body.menu-open {
                    overflow: hidden;
                }

                .nav-links {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: -100%; /* Mulai dari kiri, tersembunyi */
                    width: 280px;
                    height: 100vh;
                    background: linear-gradient(135deg, rgba(155, 93, 229, 0.95), rgba(241, 91, 181, 0.95));
                    flex-direction: column;
                    justify-content: flex-start;
                    align-items: stretch;
                    padding: 80px 20px 20px;
                    gap: 15px;
                    box-shadow: 5px 0 15px rgba(0,0,0,0.2); /* Shadow ke kanan */
                    backdrop-filter: blur(10px);
                    transition: left 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94); /* Smooth easing */
                    z-index: 1001;
                    overflow-y: auto;
                }

                .nav-links.show {
                    display: flex;
                    left: 0; /* Slide masuk ke posisi 0 */
                    animation: slideInFromLeft 0.4s ease-out;
                }

                .nav-links li {
                    width: 100%;
                    transform: translateX(-30px);
                    opacity: 0;
                    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
                }

                .nav-links.show li {
                    transform: translateX(0);
                    opacity: 1;
                }

                /* Delay untuk setiap item */
                .nav-links.show li:nth-child(1) { transition-delay: 0.05s; }
                .nav-links.show li:nth-child(2) { transition-delay: 0.1s; }
                .nav-links.show li:nth-child(3) { transition-delay: 0.15s; }
                .nav-links.show li:nth-child(4) { transition-delay: 0.2s; }
                .nav-links.show li:nth-child(5) { transition-delay: 0.25s; }
                .nav-links.show li:nth-child(6) { transition-delay: 0.3s; }

                /* Animasi keluar untuk menu items */
                .nav-links:not(.show) li {
                    transform: translateX(-30px);
                    opacity: 0;
                    transition: all 0.2s ease-in-out;
                }

                .nav-links a {
                    display: block;
                    width: 100%;
                    padding: 15px 20px;
                    color: white;
                    font-size: 1.1rem;
                    font-weight: 600;
                    text-align: left;
                    border-radius: 10px;
                    transition: all 0.3s ease;
                    position: relative;
                    overflow: hidden;
                }

                .nav-links a::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: -100%;
                    width: 100%;
                    height: 100%;
                    background: rgba(255, 255, 255, 0.1);
                    transition: left 0.3s ease;
                }

                .nav-links a:hover::before {
                    left: 0;
                }

                .nav-links a:hover {
                    transform: translateX(5px);
                    background-color: rgba(255, 255, 255, 0.2);
                }

                /* Dropdown dalam mobile menu */
                .dropdown-content {
                    position: static;
                    background: rgba(255, 255, 255, 0.1);
                    margin-top: 10px;
                    border-radius: 8px;
                    box-shadow: none;
                    padding: 10px 0;
                }

                .dropdown-content li a {
                    padding: 12px 25px;
                    font-size: 1rem;
                    color: rgba(255, 255, 255, 0.9);
                }

                /* Responsive untuk perangkat yang sangat kecil */
                @media (max-width: 480px) {
                    .nav-links {
                        width: 250px; /* Sedikit lebih kecil di layar sangat kecil */
                    }

                    .nav-links a {
                        padding: 12px 16px;
                        font-size: 1rem;
                    }
                }
            }

            /* Responsive sembunyikan di mobile */
            @media (max-width: 1118px) {
                .login-btn,
                .profil-btn,  /* perbaikan di sini */
                .header-right {
                    display: none;
                }

                .login-mobile,
                .profile-mobile {
                    display: block;
                    background-color: #f15bb5;
                    color: white;
                    padding: 10px 16px;
                    border-radius: 12px;
                    font-weight: bold;
                    text-align: center;
                    transition: background 0.3s;
                    margin: 0 10px;
                }

                .login-mobile:hover,
                .profile-mobile:hover {
                    background-color: #c0419e;
                }
            }

            @media (min-width: 1119px) {
                .login-mobile,
                .profile-mobile {
                    display: none;
                }
            }

            /* Perbaikan untuk dropdown menu */

            /* Dropdown Desktop - Hover Only */
            @media (min-width: 1119px) {
                .dropdown:hover .dropdown-content {
                    display: block;
                }

                .dropdown-content {
                    display: none;
                    position: absolute;
                    background-color: #fff0f6;
                    min-width: 220px;
                    z-index: 1000;
                    list-style: none;
                    padding: 10px 0;
                    top: 120%;
                    left: 0;
                    border-radius: 8px;
                    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
                }

                .dropdown-content li a {
                    display: block;
                    padding: 10px 20px;
                    color: #6a0572;
                    text-decoration: none;
                    transition: background-color 0.3s;
                }

                .dropdown-content li a:hover {
                    background-color: #e8b7ea;
                }
            }

            /* Dropdown Mobile - Click Only */
            @media (max-width: 1118px) {
                /* HAPUS SEMUA HOVER - Ini yang penting! */
                .dropdown:hover .dropdown-content,
                .dropdown-toggle:hover + .dropdown-content {
                    display: none !important;
                }

                .dropdown-content {
                    display: none;
                    position: static;
                    flex-direction: column;
                    background: transparent;
                    padding: 0;
                    margin-top: 8px;
                    box-shadow: none;
                    border-radius: 0;
                    /* Hapus semua transition/animation */
                    transition: none !important;
                    animation: none !important;
                }

                /* Hanya tampil saat class .open */
                .dropdown.open .dropdown-content {
                    display: flex !important;
                }

                .dropdown-content li {
                    margin-bottom: 5px;
                }

                .dropdown-content li a {
                    color: white !important;
                    font-size: 1rem;
                    padding: 12px 16px;
                    border-radius: 8px;
                    background: rgba(255, 255, 255, 0.15);
                    display: block;
                    transition: background 0.2s ease;
                }

                .dropdown-content li a:hover {
                    background: rgba(255, 255, 255, 0.25);
                }

                /* Pastikan tidak ada efek hover pada parent */
                .dropdown-toggle {
                    pointer-events: auto;
                }

                .dropdown-toggle:hover {
                    background-color: rgba(255, 255, 255, 0.15);
                }
            }

            /* Tombol profil */
            .profil-btn {
                background-color: #f15bb5;
                border: none;
                color: white;
                padding: 10px 20px;
                cursor: pointer;
                border-radius: 12px;
                font-weight: bold;
                box-shadow: 0 3px 8px rgba(0,0,0,0.2);
                transition: background 0.3s, transform 0.2s;
            }

            .profil-btn:hover {
                background-color: #521899;
                transform: scale(1.05);
            }

            .profil-card {
                visibility: hidden;
                opacity: 0;
                transition: opacity 0.3s ease, visibility 0.3s ease;
                position: fixed;
                z-index: 2500;
                top: 0; left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.4);
                backdrop-filter: blur(4px);
                -webkit-backdrop-filter: blur(4px);
                display: flex;
                justify-content: center;
                align-items: center;
            }

            /* Saat aktif */
            .profil-card.active {
                visibility: visible;
                opacity: 1;
            }

            /* Card Content (bagian dalam profil) */
            .profil-card .card-content {
                background: white;
                border-radius: 16px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
                width: 380px;
                padding: 28px;
                font-family: 'Segoe UI', sans-serif;
                color: #333;
                animation: fadeInUp 0.4s ease-in-out;

            }

            .tutup-btn {
                background-color: #e0e0e0;
                color: #333;
                border: none;
                padding: 10px;
                border-radius: 8px;
                font-weight: 600;
                cursor: pointer;
            }

            .tutup-btn:hover {
                background-color: #f1eded;
            }

            /* Card Animation */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: scale(0.95);
                }
                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }

            .card-content h3 {
                margin-top: 12px;
                font-size: 1.4rem;
                text-align: center;
                color: #d81b60;
            }

            .avatar-container {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .avatar {
                width: 90px;
                height: 90px;
                border-radius: 50%;
                border: 3px solid #f8bbd0;
                margin-bottom: 10px;
                object-fit: cover;
            }

            .profil-info {
                margin-top: 16px;
            }

            .profil-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 0.97rem;
            }

            .profil-table td {
                padding: 6px 4px;
                vertical-align: top;
            }

            .profil-table td:first-child {
                width: 45%;
                font-weight: bold;
                color: #c2185b;
            }

            .profil-table td:nth-child(2) {
                color: #444;
            }

            .button-group {
                display: flex;
                justify-content: space-between;
                gap: 12px;
                margin-top: 24px;
            }

            .logout-btn,
            .tutup-btn, .edit-btn {
                flex: 1;
                padding: 10px;
                border: none;
                border-radius: 8px;
                font-weight: 600;
                font-size: 0.95rem;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

            .logout-btn {
                background-color: #f06292;
                color: #fff;
            }

            .logout-btn:hover {
                background-color: #d30e4f;
            }

            .tutup-btn {
                background-color: #e0e0e0;
                color: #333;
            }

            .tutup-btn:hover {
                background-color: #f1eded;
            }

            .edit-btn {
                background: linear-gradient(to right, #8f00ff, #ff5fc0);
                color: #fff;
                border: none;
                padding: 10px;
                border-radius: 8px;
                font-weight: 600;
                cursor: pointer;
                box-shadow: 0 4px 12px rgba(143, 0, 255, 0.25);
                transition: background 0.3s, transform 0.2s;
            }

            .edit-btn:hover {
                background: linear-gradient(to right, #6b00b3, #c81f8f);
                transform: scale(1.05);
            }

            /* ====== edit profil ========= */
            .modal {
                display: none;
                position: fixed;
                z-index: 3000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: hidden;
                background-color: rgba(0, 0, 0, 0.4);
                backdrop-filter: blur(2px);
            }

            /* Kontainer utama modal */
            .modal-content {
                position: relative;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: #fff0fb;
                padding: 40px 30px;
                border-radius: 20px;
                box-shadow: 0 10px 30px rgba(155, 93, 229, 0.25);
                width: 90%;
                max-width: 420px;
                z-index: 1000;
                font-family: 'Segoe UI', sans-serif;
                overflow: hidden;
                max-height: 95vh;
            }

            /* Tombol Close (X) */
            .modal-content .close {
                position: absolute;
                top: 18px;
                right: 20px;
                font-size: 24px;
                color: #b75ce5;
                cursor: pointer;
                transition: color 0.3s ease;
            }

            .modal-content .close:hover {
                color: #7b2cbf;
            }

            /* Judul Modal */
            .modal-content h2 {
                color: #7b2cbf;
                font-size: 1.7rem;
                text-align: center;
                font-weight: 700;
                margin-bottom: 25px;
            }

            /* Isi Form Scrollable */
            .modal-body {
                max-height: 300px;
                overflow-y: auto;
                padding-right: 0.5rem;
                scrollbar-width: none;
                -ms-overflow-style: none;
            }
            .modal-body::-webkit-scrollbar {
                display: none;
            }

            /* Input, Select, Textarea */
            .modal-content form input,
            .modal-content form select,
            .modal-content form textarea {
                width: 100%;
                padding: 12px 14px;
                margin-bottom: 10px;
                border-radius: 10px;
                border: 1.5px solid #d8b4ef;
                font-size: 15px;
                background-color: #fff;
                transition: all 0.3s ease;
                box-shadow: inset 0 0 0 transparent;
            }

            .modal-content form input:focus,
            .modal-content form select:focus,
            .modal-content form textarea:focus {
                border-color: #c77dff;
                outline: none;
                box-shadow: 0 0 6px rgba(199, 125, 255, 0.3);
            }

            /* Tombol Aksi di Modal */
            .modal-actions {
                display: flex;
                justify-content: space-between;
                gap: 14px;
                margin-top: 20px;
            }

            .modal-actions button {
                flex: 1;
                padding: 12px 0;
                border: none;
                border-radius: 10px;
                font-weight: bold;
                font-size: 1rem;
                cursor: pointer;
                color: #fff;
                transition: transform 0.2s, background 0.3s;
                box-shadow: 0 4px 12px rgba(180, 80, 220, 0.3);
            }

            /* Tombol Simpan */
            .modal-actions .save-btn {
                background: linear-gradient(to right, #ba5aff, #8e44e9);
            }

            .modal-actions .save-btn:hover {
                transform: scale(1.05);
                background: linear-gradient(to right, #6b4b88, #4b004b);
            }

            .changepw-btn {
                background-color: #d9b3ff; /* ungu pastel yang kalem */
                color: #4b006e;
                border: none;
                padding: 10px 20px;
                border-radius: 10px;
                font-weight: 600;
                font-size: 0.95rem;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 2px 6px rgba(123, 44, 191, 0.15);
            }

            .changepw-btn:hover {
                background-color: #a96fff; /* lebih dalam tapi masih soft */
                color: #000000;
                transform: scale(1.03);
                box-shadow: 0 4px 12px rgba(80, 10, 140, 0.25);
            }

            /* Tombol Batal */
            .modal-actions .cancel-btn {
                background: linear-gradient(to right, #ff9aa2, #ffb7b2);
            }

            .modal-actions .cancel-btn:hover {
                transform: scale(1.05);
                background: linear-gradient(to right, #ee6a75, #df4343);
            }

            .modal-body {
                max-height: 420px; /* bisa diganti sesuai kebutuhan */
                overflow-y: auto;
                scrollbar-width: none;
                -ms-overflow-style: none;
            }

            .modal-body::-webkit-scrollbar {
                display: none;
            }

            .preview-img {
                width: 100px;
                height: 150px;
                border-radius: 50%;
                border: 3px solid #e8baff;
                object-fit: cover;
                box-shadow: 0 4px 12px rgba(177, 100, 255, 0.3);
                display: block;
                margin-left: auto;
                margin-right: auto;
            }

            .register-card a {
                color: #9b5de5;
                font-weight: 600;
                text-decoration: none;
            }

            .register-card a:hover {
                text-decoration: underline;
            }

            /* Modal Login*/
            .modalLoginPrompt {
                display: none;
                position: fixed;
                z-index: 1001;
                top: 0; left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(128, 0, 128, 0.3);
                justify-content: center;
                align-items: center;
                backdrop-filter: blur(3px);
            }

            .modal-content-login {
                background: linear-gradient(to bottom right, #f3e5f5, #fce4ec);
                padding: 30px 40px;
                border-radius: 20px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.15);
                text-align: center;
                max-width: 400px;
                width: 90%;
                animation: fadeInScale 0.4s ease;
            }

            .modal-content-login h3 {
                color: #8e24aa;
                margin-bottom: 10px;
            }

            .modal-content-login p {
                color: #6a1b9a;
                font-size: 15px;
            }

            .modal-buttons {
                margin-top: 20px;
            }

            .modal-buttons button {
                padding: 10px 20px;
                margin: 0 8px;
                border: none;
                border-radius: 10px;
                font-weight: 500;
                cursor: pointer;
                transition: background-color 0.3s ease, transform 0.2s ease;
                font-size: 14px;
            }

            .modal-buttons button:first-child {
                background-color: #8e24aa;
                color: white;
            }
            .modal-buttons button:first-child:hover {
                background-color: #6a1b9a;
                transform: scale(1.05);
            }

            .modal-buttons button:last-child {
                background-color: #f8bbd0;
                color: #4a148c;
            }
            .modal-buttons button:last-child:hover {
                background-color: #f48fb1;
                transform: scale(1.05);
            }

            @keyframes fadeInScale {
                from { opacity: 0; transform: scale(0.8); }
                to { opacity: 1; transform: scale(1); }
            }

            /* Modal Logout */
            .modalLogout {
                display: none;
                position: fixed;
                z-index: 3000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background: rgba(128, 0, 128, 0.3); /* soft ungu transparan */
                justify-content: center;
                align-items: center;
                backdrop-filter: blur(3px);
            }

            /* Modal Box */
            .modal-content-logout {
                background: linear-gradient(to bottom right, #f3e5f5, #fce4ec); /* ungu muda ke pink */
                padding: 30px 40px;
                border-radius: 20px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.15);
                text-align: center;
                position: relative;
                max-width: 400px;
                width: 90%;
                animation: fadeInScale 0.4s ease;
            }

            /* Animasi muncul */
            @keyframes fadeInScale {
                from {
                    opacity: 0;
                    transform: scale(0.8);
                }
                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }

            /* Judul & Teks */
            .modal-content-logout h3 {
                color: #8e24aa;
                margin-bottom: 10px;
                font-weight: 600;
            }

            .modal-content-logout p {
                color: #6a1b9a;
                font-size: 15px;
            }

            /* Tombol */
            .modal-buttons {
                margin-top: 20px;
            }

            .modal-buttons button {
                padding: 10px 20px;
                margin: 0 8px;
                border: none;
                border-radius: 10px;
                font-weight: 500;
                cursor: pointer;
                transition: background-color 0.3s ease, transform 0.2s ease;
                font-size: 14px;
            }

            /* Tombol Logout */
            .modal-buttons button:first-child {
                background-color: #d81b60;
                color: white;
            }
            .modal-buttons button:first-child:hover {
                background-color: #c2185b;
                transform: scale(1.05);
            }

            /* Tombol Batal */
            .modal-buttons button:last-child {
                background-color: #e1bee7;
                color: #4a148c;
            }
            .modal-buttons button:last-child:hover {
                background-color: #ce93d8;
                transform: scale(1.05);
            }

            /* Popup berhasil */
            .popup-berhasil {
                position: fixed;
                top: 20px;
                right: 20px;
                background-color: #d4edda; /* Hijau muda */
                border-left: 6px solid #28a745; /* Hijau tegas */
                color: #155724; /* Teks hijau gelap */
                padding: 16px 20px;
                z-index: 9999;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                max-width: 300px;
                font-family: "Segoe UI", sans-serif;
            }

            .popup-berhasil .popup-content {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .popup-berhasil .close-popup {
                color: #155724;
                font-size: 18px;
                font-weight: bold;
                cursor: pointer;
                margin-left: 10px;
            }

            .popup-berhasil p {
                margin: 0;
                font-size: 15px;
            }

            .close-popup {
                display: none;
            }

            /* Responsive breakpoints untuk mobile */
            @media (max-width: 768px) {

                .logo-img {
                    width: 40px;
                    height: 40px;
                }

                .logo-title {
                    font-size: 1.4em;
                }

                .logo-slogan {
                    font-size: 0.75em;
                }

                .login-card,
                .register-card {
                    width: 95%;
                    padding: 25px 20px;
                }

                .modal-content {
                    width: 95%;
                    padding: 30px 20px;
                }

                .profil-card .card-content {
                    width: 320px;
                    padding: 24px;
                }

                header {
                    flex-direction: row;
                }
            }

            @media (max-width: 480px) {

                .login-card,
                .register-card {
                    padding: 20px 15px;
                }

                .profil-card .card-content {
                    width: 280px;
                    padding: 20px;
                }

                .button-group {
                    flex-direction: column;
                    gap: 8px;
                }

                .modal-actions {
                    flex-direction: column;
                    gap: 10px;
                }

                .nav-links {
                    width: 250px;
                }
            }

        </style>
    </head>
<body data-register-success="<?= $showSuccessModal ? 'true' : 'false' ?>">
    <header>
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
                <li><a href="beranda.php">Beranda</a></li>
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
                       value="<?= isset($_COOKIE['remember_email']) ? $_COOKIE['remember_email'] : '' ?>" />
                <span class="error" id="emailLoginError"></span>

                <input type="password" id="passwordLogin" placeholder="Kata Sandi" required name="password" />
                <span class="error" id="passwordLoginError"></span>

                <label style="color: #6a1b9a; display: flex; align-items: center; gap: 6px; margin: 8px 5px 12px 5px;">
                    <input type="checkbox" name="remember" <?= isset($_COOKIE['remember_email']) ? 'checked' : '' ?> />
                    Ingat Saya
                </label>

                <button type="submit">Masuk</button>
            </form>

            <p style="margin-top: 10px; font-size: 14px; text-align: center;">
                <a href="#" onclick="openForgotPassword()" style="color: #6a1b9a; ">Lupa Password?</a>
            </p>

            <p class="register-text">Belum punya akun? <a href="#" onclick="openRegister()">Daftar di sini</a></p>
        </div>
    </div>

    <!-- Modal Lupa Password -->
    <div class="forgot-password-modal" id="forgotPasswordModal" style="display: none;">
        <div class="forgot-password-card">
            <span class="close-btn" onclick="closeForgotPassword()">&times;</span>
            <h2>Lupa Password</h2>
            <form action="sendResetLink.php" method="POST" id="forgotPasswordForm">
                <input type="email" name="email" placeholder="Masukkan email kamu" required />
                <span class="error" id="forgotEmailError"></span>
                <button type="submit">Kirim Link Reset</button>
            </form>
            <p class="back-login-text">
                Sudah ingat? <a href="#" onclick="backToLogin()">Kembali ke Login</a>
            </p>
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
            <p>✅ Profil berhasil diperbarui!</p>
        </div>
    </div>
    <?php unset($_SESSION['popup_berhasil']); ?>
<?php elseif (!empty($_SESSION['error_message'])): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8'); ?>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>