document.addEventListener("DOMContentLoaded", function () {
  const formConfigs = {
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
        fotoProfil: {
          id: "editFotoProfilEdit",
          error: "editfotoProfilError",
          validate: () => {
            const input = document.getElementById("editFotoProfilEdit");
            // hanya validasi jika ada file, kalau tidak ada, tetap dianggap valid
            return input ? (input.files.length === 0 || input.files[0].type.startsWith("image/")) : true;
          },
          message: "Format foto harus gambar (jpg/png/gif)"
        }
      }
    },

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

  Object.keys(formConfigs).forEach(formId => {
    const form = document.getElementById(formId);
    const config = formConfigs[formId];

    if (!form) return;

    const fields = config.fields;

    Object.keys(fields).forEach((key) => {
      const field = fields[key];
      const input = document.getElementById(field.id);
      const error = document.getElementById(field.error);

      if (!input || !error) return;

      const validate = () => {
        const isValid = field.validate(input.type === "file" ? input : input.value);
        error.textContent = isValid ? "" : field.message;
        return isValid;
      };

      input.addEventListener("input", validate);
      if (input.tagName === "SELECT" || input.type === "file") {
        input.addEventListener("change", validate);
      }
    });

    form.addEventListener("submit", function (e) {
      let isFormValid = true;

      Object.keys(fields).forEach((key) => {
        const field = fields[key];
        const input = document.getElementById(field.id);
        const error = document.getElementById(field.error);
        if (!input || !error) return;

        const isValid = field.validate(input.type === "file" ? input : input.value);
        error.textContent = isValid ? "" : field.message;
        if (!isValid) isFormValid = false;
      });

      if (!isFormValid) {
        e.preventDefault();
        console.log(`Form "${formId}" gagal dikirim karena tidak valid.`);
      } else {
        console.log(`Form "${formId}" valid dan siap dikirim.`);
      }
    });
  });
});

document.addEventListener("DOMContentLoaded", function () {
  // ========== BURGER MENU ==========
  const burger = document.getElementById("burger");
  const navLinks = document.querySelector(".nav-links");

  if (burger && navLinks) {
    burger.addEventListener("click", function (e) {
      e.stopPropagation();
      burger.classList.toggle("active");
      navLinks.classList.toggle("active");
      navLinks.classList.toggle("show");
    });

    document.addEventListener("click", function (e) {
      if (!burger.contains(e.target) && !navLinks.contains(e.target)) {
        burger.classList.remove("active");
        navLinks.classList.remove("active", "show");
        closeAllDropdowns();
      }
    });
  }

  // ========== DROPDOWN (Mobile Only) ==========
  const dropdownToggles = document.querySelectorAll(".dropdown-toggle");

  dropdownToggles.forEach(toggle => {
    toggle.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();

      if (window.innerWidth <= 1118) {
        const parent = this.closest(".dropdown");
        const isOpen = parent.classList.contains("open");

        closeAllDropdowns();
        if (!isOpen) parent.classList.add("open");
      }
    });
  });

  document.addEventListener("click", function (e) {
    if (window.innerWidth <= 1118 && !e.target.closest(".dropdown")) {
      closeAllDropdowns();
    }
  });

  window.addEventListener("resize", () => {
    if (window.innerWidth > 1118) {
      closeAllDropdowns();
      burger?.classList.remove("active");
      navLinks?.classList.remove("active", "show");
    }
  });

  function closeAllDropdowns() {
    document.querySelectorAll(".dropdown").forEach(drop => {
      drop.classList.remove("open");
    });
  }

  // ========== PROFIL ==========
  const profilCard = document.getElementById("profilCard");

  function openProfil() {
    if (profilCard) {
      profilCard.classList.add("active");

      // Tutup burger menu
      burger?.classList.remove("active");
      navLinks?.classList.remove("active", "show");
      closeAllDropdowns();
    }
  }

  const profilBtnDesktop = document.querySelector(".profil-btn");
  const profilBtnMobile = document.querySelector(".profil-link-mobile");

  if (profilBtnDesktop) {
    profilBtnDesktop.addEventListener("click", openProfil);
  }

  if (profilBtnMobile) {
    profilBtnMobile.addEventListener("click", openProfil);
  }

  // Klik luar menutup profil
  window.addEventListener("click", function (e) {
    if (e.target === profilCard) {
      profilCard.classList.remove("active");
    }

    const logoutModal = document.getElementById("logoutModal");
    if (e.target === logoutModal) {
      logoutModal.style.display = "none";
    }

    const gejalaModal = document.getElementById("gejalaModal");
    if (e.target === gejalaModal) {
      gejalaModal.style.display = "none";
    }
  });

  // ========== TAB LOGIC ==========
  const tabButtons = document.querySelectorAll(".tab-btn");
  const tabContents = document.querySelectorAll(".tab-content");

  tabButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      const target = btn.getAttribute("data-tab");

      tabButtons.forEach(b => b.classList.remove("active"));
      tabContents.forEach(c => c.classList.remove("show"));

      btn.classList.add("active");
      document.getElementById(target).classList.add("show");
    });
  });

  // Default tab
  showTab('form-tab', document.querySelector('.tab-btn.active'));

  const form        = document.getElementById('gejalaForm');
  const successMsg  = document.getElementById('successMsg');

  if (form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      // kirim data
      const formData  = new FormData(form);
      const response  = await fetch('saveGejalaUser.php', { method:'POST', body:formData });
      const result    = await response.json();

      if (result.status === 'success') {

        /* 1) tampilkan pesan sukses di dalam form */
        if (successMsg) {
          successMsg.textContent   = result.message;   // pakai pesan dari server
          successMsg.style.display = 'block';          // tampilkan

          // otomatis hilang setelah 2½ detik
          setTimeout(() => {
            successMsg.style.display = 'none';
          }, 2500);
        }

        /* 2) kosongkan form (atau tetap biarkan, sesuai kebutuhan) */
        form.reset();

        /* 3) buka modal konfirmasi apa pun yang Anda suka */
        showGejalaModal(result.message);   // jika tetap mau pakai modal
      } else {
        alert(result.message || 'Gagal menyimpan data.');
      }
    });
  }
});

// ========== MODAL & POPUP HANDLER ==========
window.addEventListener("click", function (event) {
  const profilCard = document.getElementById("profilCard");
  const editModal = document.getElementById("editProfilModal");

  // Close profilCard jika klik luar
  if (event.target === profilCard) {
    profilCard.classList.remove("active");
  }

  // Close modal edit
  if (event.target === editModal) {
    editModal.style.display = "none";
  }

  // Close popup form
  document.querySelectorAll('.popup-form').forEach(popup => {
    if (event.target === popup) {
      popup.style.display = "none";
    }
  });
});

// Untuk edit profil berhasil
function tutupPopupBerhasil() {
  const popup = document.getElementById("popupBerhasil");
  if (popup) popup.style.display = "none";
}

// Auto-close setelah 3 detik
setTimeout(() => {
  tutupPopupBerhasil();
}, 3000);

// ========== SHOW / HIDE FUNGSI ==========
function showProfilCard() {
  document.getElementById("profilCard").classList.add("active");
}

function closeProfilCard() {
  document.getElementById("profilCard").classList.remove("active");
}

function openEditProfil() {
  document.getElementById("editProfilModal").style.display = "block";
}

function closeEditProfil() {
  document.getElementById("editProfilModal").style.display = "none";
}

function showSection(sectionId) {
  document.querySelectorAll('.menu-section').forEach(sec => sec.style.display = 'none');
  document.getElementById(sectionId).style.display = 'block';
  closeAllForms();
}

function togglePilihanForm() {
  document.getElementById('pilihanForm').style.display = 'flex';
}

function showForm(formType) {
  closeAllForms();
  document.getElementById('form' + capitalize(formType)).style.display = 'flex';
}

function closeAllForms() {
  document.querySelectorAll('.popup-form').forEach(form => form.style.display = 'none');
}

function capitalize(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}

function openForm() {
  document.getElementById("popupForm").style.display = "flex";
}

function closeForm() {
  document.getElementById("popupForm").style.display = "none";
}

// ========== TAB FOR KONSULTASI ==========
function showTab(tabId, button) {
  document.querySelectorAll('.tab-content').forEach(tab => tab.style.display = 'none');
  const selectedTab = document.getElementById(tabId);
  if (selectedTab) selectedTab.style.display = 'block';

  document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
  if (button) button.classList.add('active');
}

// ========== CHART ==========
renderSymptomChart();
async function renderSymptomChart() {
  const canvas = document.getElementById("symptomChart");
  if (!canvas) return;

  const ctx = canvas.getContext('2d');
  const existingChart = Chart.getChart(canvas);
  if (existingChart) existingChart.destroy();

  try {
    const response = await fetch("getGrafikData.php"); // Pastikan path-nya benar
    const result = await response.json();

    if (result.status !== "success") {
      console.error("Gagal ambil data:", result.message);
      return;
    }

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: result.labels,
        datasets: [{
          label: 'Intensitas Gejala',
          data: result.values,
          borderColor: 'rgba(123, 47, 247, 1)',
          backgroundColor: 'rgba(123, 47, 247, 0.2)',
          tension: 0.4,
          fill: true,
          pointBackgroundColor: '#f107a3',
          pointRadius: 5
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            labels: { color: '#333' }
          }
        },
        scales: {
          x: { ticks: { color: '#333' } },
          y: { beginAtZero: true, ticks: { color: '#333' } }
        }
      }
    });
  } catch (error) {
    console.error("Gagal render chart:", error);
  }
}

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

// Modal Logout
function logout() {
  // Tampilkan modal logout
  document.getElementById("logoutModal").style.display = "flex";
}

function closeLogoutModal() {
  // Sembunyikan modal logout
  document.getElementById("logoutModal").style.display = "none";
}

function confirmLogout() {
  // Redirect ke logout.php
  window.location.href = "logout.php";
}

// Optional: klik luar card untuk menutup profil
window.addEventListener("click", function (e) {
  const card = document.getElementById("profilCard");
  if (e.target === card) {
    card.classList.remove("active");
  }
});

function openPasswordModal() {
  document.getElementById("changePasswordModal").style.display = "block";
}

function closePasswordModal() {
  document.getElementById("changePasswordModal").style.display = "none";
}

document.addEventListener("DOMContentLoaded", function () {
  // Untuk modal register
  const fotoRegister = document.getElementById('fotoProfilRegister');
  const previewRegister = document.getElementById('previewFotoRegister');
  if (fotoRegister && previewRegister) {
    fotoRegister.addEventListener('change', function () {
      const file = this.files[0];
      if (file) {
        previewRegister.src = URL.createObjectURL(file);
        previewRegister.style.display = "block";
      }
    });
  }

  // Untuk modal edit
  const fotoEdit = document.getElementById('fotoEdit');
  const previewEdit = document.getElementById('previewEdit');
  if (fotoEdit && previewEdit) {
    fotoEdit.addEventListener('change', function () {
      const file = this.files[0];
      if (file) {
        previewEdit.src = URL.createObjectURL(file);
        previewEdit.style.display = "block";
      }
    });
  }
});

// FUNGSI YANG DIPERBAHARUI untuk preview foto
function previewFotoProfil(event) {
  const input = event.target;
  const preview = input.id === 'fotoProfilRegister'
      ? document.getElementById('previewFotoRegister')
      : document.getElementById('previewFotoEdit');
  const errorSpan = document.getElementById('fotoProfilError');

  // Clear previous error
  if (errorSpan) errorSpan.textContent = '';

  if (input.files && input.files[0]) {
    const file = input.files[0];

    // Validasi tipe file
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!allowedTypes.includes(file.type)) {
      if (errorSpan) errorSpan.textContent = 'Hanya file gambar (JPEG, PNG, GIF) yang diizinkan!';
      input.value = '';
      if (preview) preview.style.display = 'none';
      return;
    }

    // Validasi ukuran file (maksimal 2MB)
    const maxSize = 2 * 1024 * 1024; // 2MB
    if (file.size > maxSize) {
      if (errorSpan) errorSpan.textContent = 'Ukuran file maksimal 2MB!';
      input.value = '';
      if (preview) preview.style.display = 'none';
      return;
    }

    // Show preview
    const reader = new FileReader();
    reader.onload = function (e) {
      if (preview) {
        preview.src = e.target.result;
        preview.style.display = 'block';
      }
    };
    reader.readAsDataURL(file);
  } else {
    if (preview) preview.style.display = 'none';
  }
}

// UNTUK MENU PEMANTAUAN
function showGejalaModal(message) {
  const modal = document.getElementById("gejalaModal");
  const msg = document.getElementById("gejalaMessage");
  if (modal && msg) {
    msg.textContent = message;
    modal.style.display = "flex";
  }
}

function closeModal() {
  const modal = document.getElementById("hasilModal");
  if (modal) {
    modal.style.display = "none";
  }
}

// Menu Perawatan
let pendingStatus = {};
let pendingCheckbox = null;

function updateStatus(checkbox) {
  const jenis = checkbox.getAttribute("data-jenis");
  const id = checkbox.getAttribute("data-id");

  pendingStatus = { jenis, id };
  pendingCheckbox = checkbox;

  document.getElementById("popupKonfirmasi").style.display = "flex";
}

document.addEventListener("DOMContentLoaded", function () {
  const btnYa = document.getElementById("btnYa");
  const btnTidak = document.getElementById("btnTidak");
  const popup = document.getElementById("popupKonfirmasi");
  if (!btnYa || !btnTidak) return;

  btnYa.addEventListener("click", () => {
    const { jenis, id } = pendingStatus;

    btnYa.disabled = true;

    fetch(`updateStatusPerawatan.php?jenis=${jenis}&id=${id}`)
        .then(response => response.json())
        .then(data => {
          btnYa.disabled = false;

          if (data.success) {
            const row = pendingCheckbox.closest("tr");
            if (row) row.remove();

            showSuccessNotification("Status berhasil diperbarui!");
          } else {
            alert("Gagal memperbarui status: " + data.message);
          }

          popup.style.display = "none";
        })
        .catch((err) => {
          btnYa.disabled = false;
          console.error("FETCH ERROR:", err);
          alert("Terjadi kesalahan saat menghubungi server.");
          popup.style.display = "none";
        });
  });

  btnTidak.addEventListener("click", () => {
    popup.style.display = "none";
    if (pendingCheckbox) pendingCheckbox.checked = false;
  });
});

function showSuccessNotification(message) {
  const toast = document.getElementById("successToast");
  toast.textContent = message;
  toast.classList.add("show");
  setTimeout(() => toast.classList.remove("show"), 2000);
}

function confirmUpdateStatus() {
  if (!pendingStatus) return;

  fetch(`updateStatusPerawatan.php?jenis=${pendingStatus.jenis}&id=${pendingStatus.id}`)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const row = document.querySelector(`input[onchange*="'${pendingStatus.jenis}', ${pendingStatus.id}"]`).closest("tr");
          if (row) row.remove();
          showSuccessNotification("Status berhasil diperbarui!");
        } else {
          showErrorNotification("Gagal memperbarui status.");
        }
      })
      .catch(() => showErrorNotification("Terjadi kesalahan saat menghubungi server."))
      .finally(() => {
        closeKonfirmasiPopup();
        pendingStatus = null;
      });
}

function closeKonfirmasiPopup() {
  document.getElementById("popupKonfirmasi").style.display = "none";
}

function closeAllForms() {
  document.querySelectorAll('.popup-form').forEach(form => {
    form.style.display = 'none';
  });
}

function openForm() {
  closeAllForms(); // tambahkan ini supaya form lain tertutup dulu
  document.getElementById("popupForm").style.display = "flex";
}

function openEditModal(jenis, id) {
  const modal = document.getElementById("editModal");
  const body = document.getElementById("editModalBody");

  // Reset isi form agar tidak double
  body.innerHTML = "<p>Memuat formulir...</p>";

  const url = `edit_${jenis}Perawatan.php?id=${id}`;

  fetch(url)
      .then(response => response.text())
      .then(html => {
        body.innerHTML = html;
        modal.style.display = "flex"; // modal tampil di tengah
      })
      .catch(error => {
        body.innerHTML = "<p style='color:red'>Gagal memuat data.</p>";
        modal.style.display = "flex";
      });
}

function closeEditModal() {
  const modal = document.getElementById("editModal");
  const body = document.getElementById("editModalBody");

  modal.style.display = "none";
  body.innerHTML = "<p>Memuat formulir...</p>"; // reset ke default
}


document.addEventListener("click", function (e) {
  const modal = document.getElementById("editModal");

  if (modal) {
    const formContainer = modal.querySelector(".popup-form");

    // ⛑️ Periksa juga apakah formContainer ada
    if (modal.style.display === "flex" && formContainer && !formContainer.contains(e.target)) {
      closeEditModal();
    }
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const popup = document.getElementById("popupSuccess");
  if (popup) {
    popup.style.display = "block";
    setTimeout(() => {
      popup.style.opacity = "0";
      setTimeout(() => popup.remove(), 500);
    }, 3000);

    // Hapus parameter `success` dari URL tanpa reload
    if (history.replaceState) {
      const newUrl = window.location.origin + window.location.pathname;
      history.replaceState({}, document.title, newUrl);
    }
  }
});

// menghapus data di menu perawatan
let hapusId = null;
let hapusJenis = null;

function showHapusModal(element) {
  hapusId = element.getAttribute("data-id");
  hapusJenis = element.getAttribute("data-jenis");
  document.getElementById("hapusModal").style.display = "flex";
}

function closeHapusModal() {
  document.getElementById("hapusModal").style.display = "none";
  hapusId = null;
  hapusJenis = null;
}

async function confirmHapus() {
  try {
    const response = await fetch(`hapus_${hapusJenis}Perawatan.php?id=${hapusId}`, {
      method: 'GET'
    });
    const result = await response.text(); // bisa diganti jadi json jika server support
    closeHapusModal();
    showSuccessNotification("Data berhasil dihapus!");
    setTimeout(() => window.location.reload(), 1200); // refresh setelah 1.2 detik
  } catch (error) {
    console.error("Gagal menghapus:", error);
    alert("Terjadi kesalahan saat menghapus data.");
  }
}

function showSuccessNotification(message) {
  const toast = document.getElementById("successToast");
  toast.textContent = message;
  toast.classList.add("show");

  setTimeout(() => {
    toast.classList.remove("show");
  }, 2000);
}

document.addEventListener("DOMContentLoaded", function () {
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get("success") === "edit") {
    showSuccessNotification("Data berhasil diedit!");
    setTimeout(() => {
      window.history.replaceState({}, document.title, window.location.pathname);
    }, 2000); // hapus query setelah 2 detik
  }
});

document.addEventListener("submit", function (e) {
  if (e.target.matches("#formEditObat") || e.target.matches("#formEditKontrol")) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    // Ambil action dari form
    const actionUrl = form.getAttribute("action") || window.location.href;

    fetch(actionUrl, {
      method: "POST",
      body: formData
    })
        .then(res => res.text())
        .then(res => {
          if (res.trim() === "success") {
            showSuccessNotification("Perubahan berhasil disimpan!");
            closeEditModal();
            setTimeout(() => location.reload(), 1000);
          } else {
            alert("Gagal menyimpan: " + res);
          }
        })
        .catch(err => {
          alert("Terjadi kesalahan saat menyimpan.");
          console.error(err);
        });
  }
});

document.addEventListener("submit", function (e) {
  if (e.target.matches("#formEditObat") || e.target.matches("#formEditKontrol")) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    fetch(form.action, {
      method: "POST",
      body: formData
    })
        .then(res => res.text())
        .then(res => {
          if (res.trim() === "success") {
            showSuccessNotification("Perubahan berhasil disimpan.");
            closeEditModal(); // Tutup modal edit
            setTimeout(() => location.reload(), 1300); // reload ringan
          } else {
            alert("Gagal menyimpan:\n" + res);
          }
        })
        .catch(() => alert("Terjadi kesalahan saat menyimpan."));
  }
});

// menu tambah obat
function toggleTanggalObat() {
  const repeat = document.getElementById("repeatObat").value;
  const tglWrapper = document.getElementById("tglObatWrapper");
  const tglInput = document.getElementById("tglObat");
  if (!repeat || !tglWrapper || !tglInput) return;

  if (repeat === "ya") {
    tglWrapper.style.display = "none";
    tglInput.removeAttribute("required");
    tglInput.value = ""; // bersihkan agar tidak ikut disubmit
  } else if (repeat === "tidak") {
    tglWrapper.style.display = "block";
    tglInput.setAttribute("required", "required");
  } else {
    // Jika belum dipilih
    tglWrapper.style.display = "none";
    tglInput.removeAttribute("required");
    tglInput.value = "";
  }
}

// Jalankan saat halaman dibuka untuk set kondisi awal
document.addEventListener('DOMContentLoaded', () => {
  const repeatSelect = document.getElementById('repeatObat');

  // Hanya pasang listener jika elemen memang ada
  if (repeatSelect) {
    repeatSelect.addEventListener('change', toggleTanggalObat);
    toggleTanggalObat();        // panggil sekali untuk state awal
  }
});


// Menu Rumah Sakit agar langsung terfilter
document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("searchInput");
  const filterKota = document.getElementById("filterKota");
  const tableRows = document.querySelectorAll("#rsTable tbody tr");

  if (!searchInput || !filterKota || tableRows.length === 0) return; // berhenti jika elemen tidak ada

  function filterTable() {
    const keyword = searchInput.value.toLowerCase();
    const selectedKota = filterKota.value.toLowerCase();

    tableRows.forEach(row => {
      const cells = row.querySelectorAll("td");
      let matchKeyword = false;

      // Cek keyword di semua kolom
      cells.forEach(cell => {
        if (cell.textContent.toLowerCase().includes(keyword)) {
          matchKeyword = true;
        }
      });

      const kota = cells[2].textContent.toLowerCase(); // kolom kota
      const matchKota = selectedKota === "" || kota === selectedKota;

      row.style.display = (matchKeyword && matchKota) ? "" : "none";
    });
  }

  searchInput.addEventListener("input", filterTable);
  filterKota.addEventListener("change", filterTable);
});
