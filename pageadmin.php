<?php
// =================================================================
// BAGIAN LOGIKA PHP (BACKEND)
// =================================================================

// [1. SESSION] (Opsional - Jika ingin memproteksi halaman ini, uncomment baris di bawah)
// session_start();
// if (!isset($_SESSION['admin'])) { header("Location: loginadmin.php"); exit(); }

// [2. KONEKSI DATABASE]
// Mengimpor file konfigurasi database agar script ini bisa berinteraksi dengan MySQL.
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - SIMATSU</title>
  
  <!-- [FONTS] Google Fonts Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- [ICONS] Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- [FRAMEWORK] Bootstrap 5 (Dipertahankan untuk Fungsionalitas Modal & Grid) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* =================================================================
       CUSTOM CSS - MODERN THEME (Overriding Bootstrap)
    ================================================================= */
    
    :root {
      --primary-dark: #005f99;
      --primary-main: #0088cc;
      --primary-light: #e0f7fa;
      --accent: #ffd700;
      --text-dark: #2c3e50;
      --bg-body: #f4f9fc;
      --sidebar-width: 260px;
      --header-height: 80px;
      --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body { font-family: 'Poppins', sans-serif; background-color: var(--bg-body); color: var(--text-dark); overflow-x: hidden; }

    /* --- SIDEBAR --- */
    .sidebar {
      position: fixed; left: 0; top: 0; bottom: 0; width: var(--sidebar-width);
      background: white; box-shadow: 2px 0 15px rgba(0,0,0,0.05); z-index: 1002;
      display: flex; flex-direction: column;
    }
    .sidebar-logo {
      height: var(--header-height); display: flex; align-items: center; padding: 0 25px;
      border-bottom: 1px solid #f0f0f0;
    }
    .logo-img { height: 40px; width: auto; margin-right: 12px; }
    .logo-text { font-size: 24px; font-weight: 800; color: var(--primary-main); letter-spacing: -1px; }
    .sidebar-menu { padding: 20px 15px; overflow-y: auto; flex: 1; }
    
    .menu-item {
      display: flex; align-items: center; gap: 12px; padding: 12px 18px;
      color: #607d8b; text-decoration: none; border-radius: 10px;
      margin-bottom: 5px; transition: var(--transition); font-weight: 500; font-size: 14px;
    }
    .menu-item:hover, .menu-item.active { background-color: var(--primary-light); color: var(--primary-main); }
    .menu-item.active { font-weight: 600; border-left: 4px solid var(--primary-main); }

    /* --- HEADER --- */
    .header {
      position: fixed; top: 0; right: 0; left: var(--sidebar-width);
      width: calc(100% - var(--sidebar-width)); height: var(--header-height);
      background: linear-gradient(135deg, var(--primary-dark), var(--primary-main));
      color: white; z-index: 1000; display: flex; align-items: center;
      padding: 0 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .header-title h1 { font-size: 20px; font-weight: 700; margin: 0; }
    .header-title p { font-size: 12px; opacity: 0.9; margin: 0; font-weight: 300; }

    /* --- MAIN CONTENT --- */
    .main-content {
      margin-left: var(--sidebar-width);
      margin-top: var(--header-height);
      padding: 30px 40px; min-height: calc(100vh - 80px);
    }

    /* --- CARD & TABLE --- */
    .card-custom {
      background: white; border: none; border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); overflow: hidden;
    }
    .card-header-custom {
      background: white; padding: 20px 25px; border-bottom: 1px solid #f0f0f0;
      display: flex; justify-content: space-between; align-items: center;
    }
    .table-custom thead { background-color: var(--primary-light); color: var(--primary-dark); }
    .table-custom th { border: none; padding: 15px; font-weight: 600; text-transform: uppercase; font-size: 13px; }
    .table-custom td { border-bottom: 1px solid #f0f0f0; padding: 15px; vertical-align: middle; color: #555; }
    .table-custom tr:hover td { background-color: #f9fcff; }

    /* --- BUTTONS --- */
    .btn-add {
      background: linear-gradient(to right, var(--primary-dark), var(--primary-main));
      border: none; color: white; border-radius: 50px; padding: 10px 20px; font-weight: 600;
      font-size: 14px; box-shadow: 0 4px 10px rgba(0, 136, 204, 0.3); transition: all 0.3s;
    }
    .btn-add:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0, 136, 204, 0.4); color: white; }
    
    .btn-action { width: 32px; height: 32px; border-radius: 8px; padding: 0; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; border: none; }
    .btn-edit { background-color: #e0f7fa; color: var(--primary-main); }
    .btn-edit:hover { background-color: var(--primary-main); color: white; }
    .btn-delete { background-color: #fee2e2; color: #ef4444; }
    .btn-delete:hover { background-color: #ef4444; color: white; }

    /* --- MODAL STYLING --- */
    .modal-content { border-radius: 15px; border: none; overflow: hidden; }
    .modal-header { background: linear-gradient(135deg, var(--primary-dark), var(--primary-main)); color: white; border: none; }
    .modal-title { font-weight: 700; font-size: 18px; }
    .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
    .form-control { border-radius: 8px; border: 1px solid #e0e0e0; padding: 10px 15px; }
    .form-control:focus { border-color: var(--primary-main); box-shadow: 0 0 0 3px rgba(0, 136, 204, 0.1); }

    /* Responsive */
    @media (max-width: 992px) {
      .sidebar { transform: translateX(-100%); transition: var(--transition); }
      .header { left: 0; width: 100%; padding-left: 20px; }
      .main-content { margin-left: 0; padding: 20px; }
    }
  </style>
</head>
<body>

  <!-- SIDEBAR -->
  <div class="sidebar">
    <div class="sidebar-logo">
      <img src="assets/logosimbb.png" alt="Logo" class="logo-img" onerror="this.style.display='none';">
      <span class="logo-text">SIMATSU</span>
    </div>
    <div class="sidebar-menu">
      <a href="#" class="menu-item active">
        <i class="fa-solid fa-gauge-high"></i> Dashboard Admin
      </a>
      <a href="simb.html" class="menu-item">
        <i class="fa-solid fa-earth-asia"></i> Lihat Website Utama
      </a>
      <a href="simb.html" class="menu-item" style="color: #ef4444; margin-top: 20px;">
        <i class="fa-solid fa-right-from-bracket"></i> Keluar
      </a>
    </div>
  </div>

  <!-- HEADER -->
  <div class="header">
    <div class="header-title">
      <h1>Panel Admin</h1>
      <p>Kelola Data Tsunami</p>
    </div>
  </div>

  <!-- MAIN CONTENT -->
  <div class="main-content">
    
    <!-- Kartu Tabel -->
    <div class="card-custom">
      <div class="card-header-custom">
        <h5 style="margin:0; font-weight:700; color:var(--text-dark);">
          <i class="fa-solid fa-database" style="margin-right:10px; color:var(--primary-main);"></i> 
          Database Kejadian
        </h5>
        <!-- Tombol Tambah Data (Memicu Modal) -->
        <button type="button" class="btn-add" data-bs-toggle="modal" data-bs-target="#modalTambah">
          <i class="fas fa-plus"></i> Tambah Data
        </button>
      </div>
      
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-custom mb-0" width="100%">
            <thead>
              <tr>
                <th width="5%">No</th>
                <th>Waktu Kejadian</th>
                <th>Lokasi</th>
                <th class="text-center">Magnitudo</th>
                <th class="text-center">Kedalaman</th>
                <th>Dampak</th>
                <th class="text-center" width="15%">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // [3. INISIALISASI VARIABEL]
              $no = 1; // Variabel untuk nomor urut baris

              // [4. QUERY DATABASE]
              // Mengambil semua data dari tabel 'data_tsunami'
              // ORDER BY tanggal DESC: Mengurutkan dari tanggal terbaru ke terlama.
              $query = mysqli_query($koneksi, "SELECT * FROM data_tsunami ORDER BY tanggal DESC");

              // [5. LOOPING DATA]
              // mysqli_fetch_assoc mengubah hasil query menjadi array asosiatif ($row)
              // Loop ini akan berjalan sebanyak jumlah baris data yang ada.
              while ($row = mysqli_fetch_assoc($query)) {
              ?>
              <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td>
                  <!-- Format Tanggal menjadi: 01-01-2024 -->
                  <strong><?= date('d-m-Y', strtotime($row['tanggal'])); ?></strong><br>
                  <span style="font-size: 12px; color: #888;">
                    <i class="fa-regular fa-clock"></i> <?= $row['waktu']; ?> WIB
                  </span>
                </td>
                <td>
                    <i class="fa-solid fa-location-dot" style="color:var(--primary-main);"></i> 
                    <?= $row['lokasi']; ?>
                </td>
                <td class="text-center">
                    <span class="badge bg-warning text-dark rounded-pill">
                        <?= $row['magnitudo']; ?> SR
                    </span>
                </td>
                <td class="text-center"><?= $row['kedalaman']; ?></td>
                <td>
                    <!-- Membatasi panjang teks dampak agar tabel tidak terlalu lebar -->
                    <span title="<?= $row['dampak']; ?>">
                        <?= substr($row['dampak'], 0, 50) . (strlen($row['dampak']) > 50 ? '...' : ''); ?>
                    </span>
                </td>
                <td class="text-center">
                  <!-- Tombol Edit (Memicu Modal Edit spesifik berdasarkan ID) -->
                  <button class="btn-action btn-edit" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id']; ?>" title="Edit">
                    <i class="fas fa-pen"></i>
                  </button>
                  <!-- Tombol Hapus (Link langsung ke prosesadmin.php dengan parameter ID) -->
                  <a href="prosesadmin.php?hapus=<?= $row['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Yakin ingin menghapus data ini?')" title="Hapus">
                    <i class="fas fa-trash"></i>
                  </a>
                </td>
              </tr>

              <!-- ========================================== -->
              <!-- MODAL EDIT DATA (Berada di dalam loop)     -->
              <!-- ========================================== -->
              <!-- ID Modal harus unik untuk setiap baris, makanya pakai id="modalEdit<?= $row['id']; ?>" -->
              <div class="modal fade" id="modalEdit<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title"><i class="fa-solid fa-pen-to-square"></i> Edit Data Tsunami</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <!-- Form mengirim data ke prosesadmin.php -->
                    <form action="prosesadmin.php" method="POST">
                      <div class="modal-body">
                        <!-- Input Hidden ID (Penting untuk identifikasi data yang diedit) -->
                        <input type="hidden" name="id" value="<?= $row['id']; ?>">
                        
                        <div class="mb-3">
                          <label class="form-label fw-bold">Tanggal</label>
                          <input type="date" name="tanggal" class="form-control" value="<?= $row['tanggal']; ?>" required>
                        </div>
                        <div class="mb-3">
                          <label class="form-label fw-bold">Waktu (WIB)</label>
                          <input type="time" name="waktu" class="form-control" value="<?= $row['waktu']; ?>" required>
                        </div>
                        <div class="mb-3">
                          <label class="form-label fw-bold">Lokasi</label>
                          <input type="text" name="lokasi" class="form-control" value="<?= $row['lokasi']; ?>" required>
                        </div>
                        <div class="row">
                          <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Magnitudo (SR)</label>
                            <input type="number" step="0.1" name="magnitudo" class="form-control" value="<?= $row['magnitudo']; ?>" required>
                          </div>
                          <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Kedalaman</label>
                            <input type="text" name="kedalaman" class="form-control" value="<?= $row['kedalaman']; ?>" required>
                          </div>
                        </div>
                        <div class="mb-3">
                          <label class="form-label fw-bold">Dampak / Keterangan</label>
                          <textarea name="dampak" class="form-control" rows="3" required><?= $row['dampak']; ?></textarea>
                        </div>
                      </div>
                      <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="update_data" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              <!-- End Modal Edit -->

              <?php } ?>
              
              <!-- [6. STATE KOSONG] Menampilkan pesan jika tidak ada data -->
              <?php if(mysqli_num_rows($query) == 0) { ?>
                <tr><td colspan="7" class="text-center py-5 text-muted">Belum ada data tsunami yang tercatat.</td></tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div> <!-- End Main Content -->

  <!-- ========================================== -->
  <!-- MODAL TAMBAH DATA (Di luar loop)           -->
  <!-- ========================================== -->
  <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Tambah Data Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="prosesadmin.php" method="POST">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label fw-bold">Tanggal</label>
              <input type="date" name="tanggal" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Waktu (WIB)</label>
              <input type="time" name="waktu" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Lokasi</label>
              <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Perairan Selatan Jawa" required>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Magnitudo (SR)</label>
                <input type="number" step="0.1" name="magnitudo" class="form-control" placeholder="0.0" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Kedalaman</label>
                <input type="text" name="kedalaman" class="form-control" placeholder="Contoh: 10 km" required>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Dampak / Keterangan</label>
              <textarea name="dampak" class="form-control" rows="3" placeholder="Deskripsi singkat kejadian..." required></textarea>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
            <button type="submit" name="simpan_data" class="btn btn-primary rounded-pill px-4">Simpan Data</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Script Bootstrap (Wajib ada untuk fungsi Modal) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>