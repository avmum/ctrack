/**
 * KONFIGURASI VALIDASI FORM
 * Object yang berisi aturan validasi untuk setiap form dalam aplikasi
 * Setiap form memiliki fields dengan ID, error element, dan fungsi validasi
 */
const formConfigs = {
  // Konfigurasi untuk form registrasi
  registerForm: {
    fields: {
      // Validasi nama: minimal 3 huruf
      nama: {
        id: "nama",
        error: "namaError",
        validate: val => val.trim().length >= 3,
        message: "Nama minimal 3 huruf"
      },
      // Validasi email: menggunakan regex untuk format email yang benar
      email: {
        id: "email",
        error: "emailError",
        validate: val => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val),
        message: "Format email tidak valid"
      },
      // Validasi password: minimal 6 karakter
      password: {
        id: "password",
        error: "passwordError",
        validate: val => val.length >= 6,
        message: "Password minimal 6 karakter"
      },
      // Validasi konfirmasi password: harus sama dengan password asli
      konfirmasiPassword: {
        id: "konfirmasiPassword",
        error: "konfpwddError",
        validate: val => val === document.getElementById("password")?.value,
        message: "Password tidak cocok"
      },
      // Validasi alamat: minimal 5 karakter
      alamat: {
        id: "alamat",
        error: "alamatError",
        validate: val => val.trim().length >= 5,
        message: "Alamat minimal 5 karakter"
      },
      // Validasi nomor HP: harus dimulai dengan 08 dan 10-13 digit total
      nomorHP: {
        id: "nomorHP",
        error: "nomorHPError",
        validate: val => /^08\d{8,11}$/.test(val),
        message: "Nomor HP harus dimulai dengan 08 dan 10–13 digit"
      },
      // Validasi jenis kelamin: tidak boleh kosong
      jenisKelamin: {
        id: "jenisKelamin",
        error: "jkError",
        validate: val => val !== "",
        message: "Jenis kelamin wajib dipilih"
      },
      // Validasi tanggal lahir: tidak boleh kosong
      tanggalLahir: {
        id: "tanggalLahir",
        error: "tglLahirError",
        validate: val => val !== "",
        message: "Tanggal lahir wajib diisi"
      },
      // Validasi golongan darah: tidak boleh kosong
      golonganDarah: {
        id: "golonganDarah",
        error: "golDarError",
        validate: val => val !== "",
        message: "Golongan darah wajib dipilih"
      },
      // Validasi jenis penjamin: tidak boleh kosong
      jenisPenjamin: {
        id: "jenisPenjamin",
        error: "jpError",
        validate: val => val !== "",
        message: "Jenis penjamin wajib dipilih"
      },
      // Validasi foto profil: wajib upload, cek tipe dan ukuran file
      fotoProfil: {
        id: "fotoProfilRegister",
        error: "fotoProfilError",
        validate: (input) => {
          // Jika tidak ada file yang dipilih, tidak valid (wajib)
          if (!input.files || input.files.length === 0) {
            return false;
          }

          const file = input.files[0];
          const allowedTypes = ["image/jpeg", "image/jpg", "image/png", "image/gif"];
          const maxSize = 2 * 1024 * 1024; // 2MB dalam bytes

          // Validasi tipe file (hanya gambar yang diizinkan)
          if (!allowedTypes.includes(file.type)) {
            return false;
          }

          // Validasi ukuran file (maksimal 2MB)
          if (file.size > maxSize) {
            return false;
          }

          return true;
        },
        message: "Foto profil wajib diupload" // Pesan default
      },
    }
  },

  // Konfigurasi untuk form login (validasi lebih sederhana)
  loginForm: {
    fields: {
      email: {
        id: "emailLogin",
        error: "emailLoginError",
        validate: val => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val),
        message: "Format email tidak valid"
      },
      password: {
        id: "passwordLogin",
        error: "passwordLoginError",
        validate: val => val.length >= 6,
        message: "Password minimal 6 karakter"
      }
    }
  },

  // Konfigurasi untuk form edit profil (mirip dengan register tapi foto tidak wajib)
  editProfilForm: {
    fields: {
      nama: {
        id: "editNama",
        error: "editnamaError",
        validate: val => val.trim().length >= 3,
        message: "Nama minimal 3 huruf"
      },
      email: {
        id: "editEmail",
        error: "editemailError",
        validate: val => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val),
        message: "Format email tidak valid"
      },
      tglLahir: {
        id: "editTglLahir",
        error: "edittglLahirError",
        validate: val => val !== "",
        message: "Tanggal lahir wajib diisi"
      },
      jenisKelamin: {
        id: "editjenisKelamin",
        error: "editjkError",
        validate: val => val !== "",
        message: "Jenis kelamin wajib dipilih"
      },
      noHp: {
        id: "EditNoHp",
        error: "editnoHpError",
        validate: val => /^08\d{8,11}$/.test(val),
        message: "Nomor HP harus dimulai dengan 08 dan 10–13 digit"
      },
      alamat: {
        id: "editAlamat",
        error: "editalamatError",
        validate: val => val.trim().length >= 5,
        message: "Alamat minimal 5 karakter"
      },
      golDarah: {
        id: "EditGolDarah",
        error: "editgolDarahError",
        validate: val => val !== "",
        message: "Golongan darah wajib dipilih"
      },
      jenisPenjamin: {
        id: "editJenisPenjamin",
        error: "editjpError",
        validate: val => val !== "",
        message: "Jenis penjamin wajib dipilih"
      },
      // Foto profil di edit tidak wajib (berbeda dengan register)
      fotoProfil: {
        id: "editFotoProfilEdit",
        error: "editfotoProfilError",
        validate: (input) => {
          // Jika tidak ada file yang dipilih, valid (tidak wajib saat edit)
          if (!input.files || input.files.length === 0) {
            return true;
          }

          const file = input.files[0];
          const allowedTypes = ["image/jpeg", "image/jpg", "image/png", "image/gif"];
          const maxSize = 2 * 1024 * 1024; // 2MB

          // Validasi tipe file
          if (!allowedTypes.includes(file.type)) {
            return false;
          }

          // Validasi ukuran file
          if (file.size > maxSize) {
            return false;
          }

          return true;
        },
        message: ""
      }
    }
  },

  // Konfigurasi untuk form ganti password
  changePasswordForm: {
    fields: {
      oldPassword: {
        id: "oldPassword",
        error: "oldPasswordError",
        validate: val => val.length >= 6,
        message: "Password lama minimal 6 karakter"
      },
      newPassword: {
        id: "newPassword",
        error: "newPasswordError",
        validate: val => val.length >= 6,
        message: "Password baru minimal 6 karakter"
      },
      confirmNewPassword: {
        id: "confirmNewPassword",
        error: "confirmNewPasswordError",
        validate: val => val === document.getElementById("newPassword")?.value,
        message: "Konfirmasi password tidak cocok"
      }
    }
  }
};

/**
 * EVENT LISTENER UNTUK VALIDASI REAL-TIME
 * Dipanggil saat DOM sudah siap, menambahkan event listener ke semua form
 * untuk validasi input secara real-time (saat user mengetik)
 */
document.addEventListener("DOMContentLoaded", function () {
  // Loop melalui setiap konfigurasi form
  Object.keys(formConfigs).forEach(formId => {
    const form = document.getElementById(formId);
    const config = formConfigs[formId];

    // Skip jika form tidak ditemukan di halaman
    if (!form) return;

    const fields = config.fields;

    // Loop melalui setiap field dalam form
    Object.keys(fields).forEach((key) => {
      const field = fields[key];
      const input = document.getElementById(field.id);
      const error = document.getElementById(field.error);

      // Skip jika input atau error element tidak ditemukan
      if (!input || !error) {
        console.warn(`Element tidak ditemukan: input=${field.id}, error=${field.error}`);
        return;
      }

      // Fungsi validasi yang akan dipanggil saat input berubah
      const validate = () => {
        // Untuk input file, kirim objek input-nya langsung
        // Untuk input biasa, kirim value-nya
        const valueToValidate = input.type === "file" ? input : input.value;
        const isValid = field.validate(valueToValidate);

        // Penanganan khusus untuk file input
        if (input.type === "file") {
          // Jika sudah ada error message dari previewFotoProfil, jangan timpa
          if (error.textContent === "" && !isValid && field.message) {
            error.textContent = field.message;
          }
        } else {
          // Untuk input biasa, tampilkan pesan error normal
          if (typeof field.message === 'string' && field.message) {
            error.textContent = isValid ? "" : field.message;
          }
        }

        return isValid;
      };

      // Tambahkan event listener berdasarkan tipe input
      if (input.type === "file") {
        // File input hanya perlu change event
        input.addEventListener("change", validate);
      } else {
        // Input biasa perlu input dan change event
        input.addEventListener("input", validate);  // Saat mengetik
        input.addEventListener("change", validate); // Saat focus hilang
      }
    });
  });
});

/**
 * VALIDASI FORM REGISTER SAAT SUBMIT
 * Memvalidasi seluruh form register sebelum dikirim ke server
 * Mencegah submit jika ada field yang tidak valid
 */
document.addEventListener("DOMContentLoaded", function () {
  const registerForm = document.getElementById("registerForm");
  if (!registerForm) return;

  // Ambil konfigurasi field dari formConfigs
  const fields = formConfigs.registerForm.fields;

  // Event listener untuk submit form
  registerForm.addEventListener("submit", function (e) {
    let isFormValid = true;

    // Validasi setiap field
    Object.keys(fields).forEach((key) => {
      const field = fields[key];
      const input = document.getElementById(field.id);
      const error = document.getElementById(field.error);
      if (!input || !error) return;

      // Kirim langsung input jika file, kirim .value jika bukan
      const valueToValidate = input.type === "file" ? input : input.value;
      const isValid = field.validate(valueToValidate);

      // Tampilkan error jika ada
      if (typeof field.message === "string" && field.message) {
        error.textContent = isValid ? "" : field.message;
      }

      if (!isValid) isFormValid = false;
    });

    // Cegah submit jika form tidak valid
    if (!isFormValid) {
      e.preventDefault();
    }
  });
});

/**
 * VALIDASI FORM EDIT PROFIL SAAT SUBMIT
 * Similar dengan validasi register, tapi untuk form edit profil
 */
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("editProfilForm");
  if (!form) return;

  // Ambil konfigurasi field dari formConfigs
  const fields = formConfigs.editProfilForm.fields;

  form.addEventListener("submit", function (e) {
    let isFormValid = true;

    // Validasi setiap field
    Object.keys(fields).forEach((key) => {
      const field = fields[key];
      const input = document.getElementById(field.id);
      const error = document.getElementById(field.error);
      if (!input || !error) return;

      const valueToValidate = input.type === "file" ? input : input.value;
      const isValid = field.validate(valueToValidate);

      // Tampilkan pesan error kalau ada `message`
      if (typeof field.message === "string" && field.message) {
        error.textContent = isValid ? "" : field.message;
      }

      if (!isValid) isFormValid = false;
    });

    // Cegah form submit jika ada error
    if (!isFormValid) {
      e.preventDefault();
    }
  });
});

/**
 * FUNGSI UNTUK MENUTUP POPUP BERHASIL
 * Menangani popup notifikasi ketika profil berhasil diupdate
 */
function tutupPopupBerhasil() {
  const popup = document.getElementById("popupBerhasil");
  if (popup) popup.style.display = "none";
}

// Auto-close popup setelah 3 detik
setTimeout(() => {
  tutupPopupBerhasil();
}, 3000);

/**
 * INISIALISASI EVENT LISTENERS UTAMA
 * Menangani burger menu, dropdown, modal, dan interaksi UI lainnya
 */
document.addEventListener("DOMContentLoaded", function () {
  // Ambil referensi elemen-elemen penting
  const burger = document.getElementById("burger");
  const navLinks = document.querySelector(".nav-links") || document.getElementById("navLinks");
  const loginCard = document.getElementById("loginCard");
  const registerCard = document.getElementById("registerCard");
  const notifBtn = document.getElementById("btnNotifikasi");
  const loginMobileBtn = document.querySelector('.login-mobile');
  const loginDesktopBtn = document.querySelector('.login-btn');
  const loginPrompt = document.getElementById("loginPromptModal");

  /**
   * FUNGSI UNTUK MENUTUP BURGER MENU
   * Menghapus class active dari burger dan navigation
   */
  function closeBurgerMenu() {
    burger.classList.remove("active");
    navLinks.classList.remove("active", "show");
  }

  // Event listener untuk tombol login mobile
  if (loginMobileBtn) {
    loginMobileBtn.addEventListener('click', () => {
      closeBurgerMenu();
      openLogin(); // fungsi modal login
    });
  }

  // Event listener untuk tombol login desktop
  if (loginDesktopBtn) {
    loginDesktopBtn.addEventListener('click', () => {
      closeBurgerMenu(); // opsional buat desktop
      openLogin();
    });
  }

  /**
   * BURGER MENU TOGGLE
   * Menangani pembukaan/penutupan menu mobile
   */
  if (burger && navLinks) {
    burger.addEventListener("click", (e) => {
      e.stopPropagation(); // Mencegah event bubbling
      burger.classList.toggle("active");
      navLinks.classList.toggle("active");
      navLinks.classList.toggle("show");
    });

    // Tutup burger menu ketika klik di luar
    document.addEventListener("click", (e) => {
      if (!burger.contains(e.target) && !navLinks.contains(e.target)) {
        burger.classList.remove("active");
        navLinks.classList.remove("active");
        navLinks.classList.remove("show");
        // Tutup semua dropdown
        closeAllDropdowns();
      }
    });
  }

  /**
   * MENUTUP MODAL DENGAN KLIK DI LUAR
   * Menangani penutupan modal login/register ketika user klik di area luar modal
   */
  document.addEventListener("click", (e) => {
    // Jika klik di background modal login
    if (e.target === loginCard) {
      loginCard.style.display = "none";
      enableNavigation();
      // Reset login form ketika ditutup dengan klik luar
      resetLoginForm();
    }
    // Jika klik di background modal register
    if (e.target === registerCard) {
      registerCard.style.display = "none";
      enableNavigation();
      // Reset register form ketika ditutup dengan klik luar
      resetRegisterForm();
    }

    // Jika klik di background modal login prompt
    if (e.target === loginPrompt) {
      loginPromptModal.style.display = "none";
      enableNavigation();
    }
  });

  // Tampilkan notifikasi jika user sudah login
  if (notifBtn) {
    const userLoggedIn = true; // Ini bisa diganti dengan logika cek login yang sebenarnya
    if (userLoggedIn) notifBtn.style.display = "inline-block";
  }

  /**
   * DROPDOWN LOGIC UNTUK MOBILE
   * Menangani dropdown menu "Track" di navigation mobile
   */
  const dropdownToggles = document.querySelectorAll(".dropdown-toggle");

  dropdownToggles.forEach(toggle => {
    toggle.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();

      // Hanya jalankan di mobile (layar <= 1118px)
      if (window.innerWidth <= 1118) {
        const parent = this.closest(".dropdown");
        const isOpen = parent.classList.contains("open");

        // Tutup semua dropdown dulu
        closeAllDropdowns();

        // Buka yang diklik jika sebelumnya tertutup
        if (!isOpen) {
          parent.classList.add("open");
        }
      }
    });
  });

  // Tutup dropdown saat klik di luar
  document.addEventListener("click", (e) => {
    if (!e.target.closest(".dropdown") && window.innerWidth <= 1118) {
      closeAllDropdowns();
    }
  });

  // Tutup dropdown saat resize ke desktop
  window.addEventListener("resize", () => {
    if (window.innerWidth > 1118) {
      closeAllDropdowns();
    }
  });

  /**
   * FUNGSI UNTUK MENUTUP SEMUA DROPDOWN
   * Utility function untuk menutup semua dropdown yang terbuka
   */
  function closeAllDropdowns() {
    document.querySelectorAll(".dropdown").forEach(drop => {
      drop.classList.remove("open");
    });
  }

  /**
   * RESET UI SAAT WINDOW RESIZE
   * Menangani perubahan dari mobile ke desktop dan sebaliknya
   */
  window.addEventListener("resize", () => {
    if (window.innerWidth > 1118) {
      // Tutup semua dropdown
      document.querySelectorAll(".dropdown").forEach(drop => {
        drop.classList.remove("open");
      });

      // Tutup burger menu (jika masih terbuka)
      const navLinks = document.querySelector(".nav-links");
      const burger = document.getElementById("burger");
      if (navLinks && burger) {
        navLinks.classList.remove("active", "show");
        burger.classList.remove("active");
      }
    }
  });
});

/**
 * FUNGSI DISABLE/ENABLE NAVIGATION
 * Mencegah interaksi dengan navigation saat modal terbuka
 */
function disableNavigation() {
  const header = document.querySelector("header");
  if (header) header.classList.add("nav-disabled");
}

function enableNavigation() {
  const header = document.querySelector("header");
  if (header) header.classList.remove("nav-disabled");
}

/**
 * FUNGSI UNTUK MEMBUKA MODAL LOGIN
 * Menampilkan modal login dan menutup modal lain yang mungkin terbuka
 */
function openLogin() {
  const loginCard = document.getElementById("loginCard");
  const registSuccess = document.getElementById("registerSuccessModal");
  const loginPrompt = document.getElementById("loginPromptModal");

  if (loginCard) {
    loginCard.style.display = "flex";
    disableNavigation();
  }
  if (registSuccess) registSuccess.style.display = "none";
  if (loginPrompt) loginPrompt.style.display = "none";
}

/**
 * FUNGSI UNTUK MEMBUKA MODAL REGISTER
 * Menampilkan modal registrasi
 */
function openRegister() {
  const registerCard = document.getElementById("registerCard");
  if (registerCard) {
    registerCard.style.display = "flex";
    disableNavigation();
  }
}

/**
 * FUNGSI UNTUK MENUTUP MODAL LOGIN
 * Menyembunyikan modal login dan reset form
 */
function closeLogin() {
  const loginCard = document.getElementById("loginCard");
  if (loginCard) {
    loginCard.style.display = "none";
    enableNavigation();
  }
  // Reset form saat tombol close diklik
  resetLoginForm();
}

/**
 * FUNGSI UNTUK MENUTUP MODAL REGISTER
 * Menyembunyikan modal register dan reset form
 */
function closeRegister() {
  const registerCard = document.getElementById("registerCard");
  if (registerCard) {
    registerCard.style.display = "none";
    enableNavigation();
  }
  // Reset form saat tombol close diklik
  resetRegisterForm();
}

/**
 * FUNGSI RESET LOGIN FORM
 * Mengosongkan semua field di form login dan menghapus pesan error
 */
function resetLoginForm() {
  // Reset field values
  const emailLogin = document.getElementById("emailLogin");
  const passwordLogin = document.getElementById("passwordLogin");

  if (emailLogin) emailLogin.value = "";
  if (passwordLogin) passwordLogin.value = "";

  // Reset error messages dan styling
  resetLoginFormErrors();
}

/**
 * FUNGSI RESET REGISTER FORM
 * Mengosongkan semua field di form register dan menghapus pesan error
 */
function resetRegisterForm() {
  // Reset semua field values
  const fields = [
    "nama", "email", "password", "konfirmasiPassword",
    "alamat", "nomorHP", "jenisKelamin", "tanggalLahir",
    "golonganDarah", "jenisPenjamin", "fotoProfilRegister"
  ];

  fields.forEach(id => {
    const field = document.getElementById(id);
    if (field) {
      field.value = "";
      // Khusus untuk select, pastikan option pertama terpilih
      if (field.tagName === "SELECT") {
        field.selectedIndex = 0;
      }
    }
  });

  // Reset preview foto
  const previewFoto = document.getElementById("previewFotoRegister");
  if (previewFoto) {
    previewFoto.src = "";
    previewFoto.style.display = "none";
  }

  // Reset error messages dan styling
  resetRegisterFormErrors();
}

/**
 * FUNGSI RESET ERROR MESSAGES LOGIN
 * Menghapus semua pesan error di form login
 */
function resetLoginFormErrors() {
  const fields = ["emailLogin", "passwordLogin"];
  fields.forEach(id => {
    const field = document.getElementById(id);
    const err = document.getElementById(id + "Error");
    if (field) field.classList.remove("input-error");
    if (err) err.textContent = "";
  });
}

/**
 * FUNGSI RESET ERROR MESSAGES REGISTER
 * Menghapus semua pesan error di form register
 */
function resetRegisterFormErrors() {
  const errorIds = [
    "namaError", "emailError", "passwordError", "konfpwddError",
    "nomorHPError", "jkError", "tglLahirError", "golDarError",
    "jpError", "fotoProfilError"
  ];
  const inputIds = [
    "nama", "email", "password", "konfirmasiPassword",
    "nomorHP", "jenisKelamin", "tanggalLahir", "golonganDarah",
    "jenisPenjamin", "fotoProfilRegister"
  ];

  // Hapus pesan error
  errorIds.forEach(id => {
    const el = document.getElementById(id);
    if (el) el.textContent = "";
  });

  // Hapus styling error dari input
  inputIds.forEach(id => {
    const el = document.getElementById(id);
    if (el) el.classList.remove("input-error");
  });
}

/**
 * FUNGSI PREVIEW FOTO PROFIL
 * Menangani upload dan preview foto profil dengan validasi
 * @param {Event} event - Event dari input file
 */
function previewFotoProfil(event) {
  const input = event.target;

  // Tentukan elemen preview dan error berdasarkan ID input
  const preview =
      input.id === "fotoProfilRegister"
          ? document.getElementById("previewFotoRegister")
          : document.getElementById("previewFotoEdit");

  const errorSpan =
      input.id === "fotoProfilRegister"
          ? document.getElementById("fotoProfilError")
          : document.getElementById("editfotoProfilError");

  // Pastikan error span ditemukan
  if (!errorSpan) {
    console.error("Error span tidak ditemukan untuk:", input.id);
    return;
  }

  // Reset error message
  errorSpan.textContent = "";
  errorSpan.style.display = "block"; // Pastikan error span terlihat

  // Jika tidak ada file yang dipilih
  if (!input.files || input.files.length === 0) {
    if (preview) {
      preview.style.display = "none";
      preview.src = "";
    }

    // Untuk register, foto wajib diupload
    if (input.id === "fotoProfilRegister") {
      errorSpan.textContent = "Foto profil wajib diupload!";
    }
    return;
  }

  const file = input.files[0];
  const allowedTypes = ["image/jpeg", "image/jpg", "image/png", "image/gif"];
  const maxSize = 2 * 1024 * 1024; // 2MB

  // Validasi tipe file dengan pesan error yang jelas
  if (!allowedTypes.includes(file.type)) {
    errorSpan.textContent = "Hanya file gambar (JPEG, PNG, GIF) yang diizinkan!";
    input.value = ""; // Reset input
    if (preview) {
      preview.style.display = "none";
      preview.src = "";
    }
    return;
  }

  // Validasi ukuran file dengan pesan error yang jelas
  if (file.size > maxSize) {
    errorSpan.textContent = "Ukuran file maksimal 2MB!";
    input.value = ""; // Reset input
    if (preview) {
      preview.style.display = "none";
      preview.src = "";
    }
    return;
  }

  // File valid: tampilkan preview dan hapus error
  errorSpan.textContent = "";

  const reader = new FileReader();
  reader.onload = function (e) {
    if (preview) {
      preview.src = e.target.result;
      preview.style.display = "block";
    }
  };

  reader.onerror = function() {
    errorSpan.textContent = "Gagal membaca file!";
    if (preview) {
      preview.style.display = "none";
      preview.src = "";
    }
  };

  reader.readAsDataURL(file);
}

/**
 * FUNGSI SHOW MORE VIDEOS
 * Menampilkan video survival journey yang tersembunyi
 */
function showMoreVideos() {
  document.getElementById("more-videos").style.display = "grid";
  document.getElementById("showMoreButton").style.display = "none";
}

/**
 * FUNGSI RESET FORM (WRAPPER)
 * Wrapper function yang memanggil resetRegisterForm
 */
function resetForm() {
  // Panggil fungsi reset register yang sudah dibuat
  resetRegisterForm();
}

/**
 * FUNGSI MANAJEMEN MODAL PROFIL
 * Menangani pembukaan dan penutupan modal profil user
 */
function openProfilCard() {
  const card = document.getElementById("profilCard");
  if (card) {
    card.classList.add("active");
  }
}

function closeProfilCard() {
  const card = document.getElementById("profilCard");
  if (card) {
    card.classList.remove("active");
  }
}

/**
 * FUNGSI MANAJEMEN MODAL EDIT PROFIL
 * Menangani pembukaan dan penutupan modal edit profil
 */
function openEditProfil() {
  const modal = document.getElementById("editProfilModal");
  if (modal) {
    modal.style.display = "block";
  }
}

function closeEditProfil() {
  const modal = document.getElementById("editProfilModal");
  if (modal) {
    modal.style.display = "none";
  }
}

/**
 * FUNGSI MANAJEMEN LOGOUT
 * Menangani proses logout dengan konfirmasi
 */
function logout() {
  // Tampilkan modal logout untuk konfirmasi
  document.getElementById("logoutModal").style.display = "flex";
}

function closeLogoutModal() {
  // Sembunyikan modal logout
  document.getElementById("logoutModal").style.display = "none";
}

function confirmLogout() {
  // Redirect ke logout.php untuk menghapus session
  window.location.href = "logout.php";
}

/**
 * FUNGSI MANAJEMEN LOGIN PROMPT
 * Menangani popup yang muncul ketika user belum login tapi mencoba akses fitur Track
 */
function showLoginPrompt() {
  document.getElementById("loginPromptModal").style.display = "flex";
}

function closeLoginPrompt() {
  document.getElementById("loginPromptModal").style.display = "none";
}

/**
 * EVENT LISTENER UNTUK MENUTUP PROFIL DENGAN KLIK LUAR
 * Menutup modal profil ketika user klik di area luar modal
 */
window.addEventListener("click", function (e) {
  const card = document.getElementById("profilCard");
  if (e.target === card) {
    card.classList.remove("active");
  }
});

/**
 * FUNGSI MANAJEMEN MODAL GANTI PASSWORD
 * Menangani pembukaan dan penutupan modal ganti password
 */
function openPasswordModal() {
  document.getElementById("changePasswordModal").style.display = "block";
}

function closePasswordModal() {
  document.getElementById("changePasswordModal").style.display = "none";
}

/**
 * FUNGSI TAMPILKAN MODAL SUCCESS REGISTER
 * Menampilkan modal sukses registrasi berdasarkan data attribute di body
 */
document.addEventListener("DOMContentLoaded", function () {
  const body = document.querySelector("body");
  const success = body?.dataset.registerSuccess === "true";
  if (success) {
    const modal = document.getElementById("registerSuccessModal");
    if (modal) modal.style.display = "flex";
  }
});

/**
 * FUNGSI MENUTUP MODAL SUCCESS REGISTER
 * Menyembunyikan modal sukses registrasi
 */
function closeRegisterSuccessModal() {
  const modal = document.getElementById("registerSuccessModal");
  if (modal) modal.style.display = "none";
}