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
    $GLOBALS['userId'] = $userId;
}
?>

    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>C-track</title>
        <!-- CSS dinamis -->
        <link rel="stylesheet" href="<?= $cssFile ?>?v=<?= time() ?>">
        <link rel="icon" href="../assets/logo.png" type="image/png">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="../javascript/trackScript.js" defer></script>
        <style>
            body{
                margin: 0;
            }
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
                max-height: 420px;
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
                background-color: #c88afa; /* ungu pastel yang kalem */
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

            .error {
                display: block;
                margin-bottom: 10px;
                color: #d32f2f; /* warna merah muda gelap */
                font-size: 14px;
                font-style: italic;
                padding-left: 4px;
                transition: opacity 0.3s ease;
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

            /* Close button */
            .close-btn {
                position: absolute;
                top: 10px;
                right: 16px;
                font-size: 24px;
                cursor: pointer;
                color: #888;
                z-index: 9999;
            }

            .close-btn:hover {
                color: #c77dff;
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
                .profil-btn {
                    display: none;
                }

                .profil-link-mobile {
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

                .profil-link-mobile:hover {
                    background-color: #c0419e;
                }
            }

            @media (min-width: 1119px) {
                .profil-link-mobile {
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

                .modal-content {
                    width: 95%;
                    padding: 30px 20px;
                }

                .profil-card .card-content {
                    width: 320px;
                    padding: 24px;
                }
            }

            @media (max-width: 480px) {


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
<body>
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

        <nav class="navbar">
            <ul class="nav-links">
                <li><a href="beranda.php">Beranda</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" id="trackDropdown">Track</a>
                    <ul class="dropdown-content">
                        <li><a href="pemantauan.php">Pemantauan Gejala</a></li>
                        <li><a href="konsultasi.php">Konsultasi Awal</a></li>
                        <li><a href="perawatan.php">Manajemen Perawatan</a></li>
                        <li><a href="rs.php">Rumah Sakit Kanker</a></li>
                    </ul>
                </li>
                <li><a href="beranda.php#infoContainer">What's a Cancer</a></li>
                <li><a href="#" class="profil-link-mobile" onclick="showProfilCard()">Profil</a></li>
            </ul>
        </nav>

        <button class="profil-btn" onclick="showProfilCard()">Profil</button>
    </header>

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