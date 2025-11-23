<?php
// --- PENTING: BARIS INI WAJIB ADA DI PALING ATAS ---
include 'koneksi.php'; 
// ---------------------------------------------------

// --- 1. LOGIKA UNTUK CHART (Statistik Tahunan) ---
$tahun_label = [];
$jumlah_data = [];

// Cek koneksi & Query
if (isset($koneksi)) {
    // PERBAIKAN: Menghapus "WHERE tanggal != '0000-00-00'" yang menyebabkan error
    // Diganti dengan "WHERE YEAR(tanggal) > 0" agar lebih aman di semua versi MySQL
    $query_chart = mysqli_query($koneksi, "SELECT YEAR(tanggal) as tahun, COUNT(*) as jumlah FROM data_tsunami WHERE YEAR(tanggal) > 0 GROUP BY YEAR(tanggal) ORDER BY tahun ASC");

    if ($query_chart) {
        while ($chart_row = mysqli_fetch_assoc($query_chart)) {
            $tahun_label[] = $chart_row['tahun'];
            $jumlah_data[] = $chart_row['jumlah'];
        }
    }
}

// Mengubah array PHP ke format JSON
$json_tahun = json_encode($tahun_label);
if (!$json_tahun) $json_tahun = '[]';

$json_jumlah = json_encode($jumlah_data);
if (!$json_jumlah) $json_jumlah = '[]';
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Data Tsunami - SIMATSU</title>

    <!-- Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>

    <style>
      /* --- STYLE CSS --- */
      * { margin: 0; padding: 0; box-sizing: border-box; }
      body { font-family: "Poppins", sans-serif; background: #f5f7fa; color: #333; }
      
      /* Header */
      .header {
        background: linear-gradient(135deg, #648db3 0%, #5a7fa0 100%);
        color: white; padding: 20px 30px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        position: fixed; width: 100%; top: 0; z-index: 1000;
      }
      .header-content { display: flex; justify-content: space-between; align-items: center; max-width: 1600px; margin: 0 auto; }
      .header-left { display: flex; align-items: center; gap: 20px; }
      .logo { font-size: 28px; font-weight: 700; color: #ecefca; }
      .header-title h1 { font-size: 22px; font-weight: 600; margin-bottom: 5px; }
      .header-title p { font-size: 12px; opacity: 0.9; font-weight: 300; }
      .header-right { display: flex; gap: 15px; }
      
      .btn-login {
        padding: 10px 20px; border: none; border-radius: 8px; font-weight: 500; cursor: pointer;
        transition: all 0.3s; font-size: 14px; display: flex; align-items: center; gap: 8px; text-decoration: none;
      }
      .btn-pimpinan { background: #2c5282; color: white; }
      .btn-pimpinan:hover { background: #1e3a5f; transform: translateY(-2px); }
      .btn-admin { background: #ecefca; color: #648db3; }
      .btn-admin:hover { background: #dde0b8; transform: translateY(-2px); }

      /* Sidebar */
      .sidebar {
        position: fixed; left: 0; top: 95px; width: 260px; height: calc(100vh - 95px);
        background: white; box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05); padding: 20px 0;
        overflow-y: auto; z-index: 998; transition: all 0.3s;
      }
      .sidebar.collapsed { width: 80px; }
      .menu-toggle {
        position: absolute; top: 10px; right: 10px; background: #648db3; color: white;
        border: none; width: 35px; height: 35px; border-radius: 8px; cursor: pointer;
        font-size: 18px; transition: all 0.3s;
      }
      .menu-toggle:hover { background: #5a7fa0; }
      .menu-item {
        display: flex; align-items: center; gap: 15px; padding: 15px 25px;
        color: #555; text-decoration: none; transition: all 0.3s; cursor: pointer; border-left: 4px solid transparent;
      }
      .menu-item:hover { background: #f0f4f8; color: #648db3; }
      .menu-item.active {
        background: linear-gradient(90deg, #ecefca 0%, rgba(236, 239, 202, 0.3) 100%);
        color: #648db3; border-left-color: #648db3; font-weight: 600;
      }
      .menu-icon { font-size: 22px; min-width: 30px; text-align: center; }
      .menu-text { font-size: 15px; white-space: nowrap; }
      .sidebar.collapsed .menu-text { display: none; }

      /* Main Content */
      .main-content {
        margin-left: 260px; margin-top: 100px; padding: 30px; min-height: calc(100vh - 100px); transition: all 0.3s;
      }
      .main-content.expanded { margin-left: 80px; }

      /* Cards */
      .card {
        background: white; border-radius: 15px; padding: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); margin-bottom: 30px; transition: all 0.3s;
      }
      .card:hover { box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12); transform: translateY(-5px); }
      .card-title {
        font-size: 22px; font-weight: 600; color: #648db3; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;
      }
      .card-title-icon { font-size: 28px; }

      /* Table Styles */
      .table-responsive { overflow-x: auto; }
      .custom-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 14px; }
      .custom-table thead { background-color: #648db3; color: white; }
      .custom-table th, .custom-table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
      .custom-table tbody tr:hover { background-color: #f9fafc; }
      .badge { padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: 600; }
      .badge-magnitude { background-color: #ecefca; color: #648db3; }
      .chart-container { position: relative; height: 350px; width: 100%; }

      /* Responsive */
      @media (max-width: 768px) {
        .sidebar { top: 95px; transform: translateX(-100%); }
        .sidebar.mobile-open { transform: translateX(0); }
        .main-content { margin-left: 0; margin-top: 100px; }
        .header-content { flex-direction: column; gap: 15px; }
        .menu-toggle-mobile {
          display: block; position: fixed; bottom: 20px; right: 20px; width: 60px; height: 60px;
          background: #648db3; color: white; border: none; border-radius: 50%; font-size: 24px;
          box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3); z-index: 1001; cursor: pointer;
        }
      }
      @media (min-width: 769px) { .menu-toggle-mobile { display: none; } }
    </style>
  </head>

  <body>
    <!-- Hidden Inputs for Chart Data -->
    <input type="hidden" id="dataTahun" value='<?php echo $json_tahun; ?>'>
    <input type="hidden" id="dataJumlah" value='<?php echo $json_jumlah; ?>'>

    <!-- Header -->
    <div class="header">
      <div class="header-content">
        <div class="header-left">
          <div
            class="logo"
            style="display: flex; align-items: center; gap: 10px"
          >
            <img
              src="assets/logosimbb.png"
              alt="Logo SIMATSU"
              style="height: 50px; width: auto"
            />
            <span style="font-weight: 700; color: #ecefca; font-size: 28px"
              >SIMATSU</span
            >
          </div>
          <div class="header-title">
            <h1>Sistem Informasi Manajemen Tsunami</h1>
            <p>Mengenali, Mencegah, dan Mengurangi Risiko Bencana</p>
          </div>
        </div>
        <div class="header-right">
          <a href="loginadmin.php" class="btn-login btn-admin">🧑‍💼 Login Admin</a>
        </div>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
      <!-- <button class="menu-toggle" onclick="toggleSidebar()">☰</button> -->
      <a href="simb.html" class="menu-item" href="simb.html">
        <span class="menu-icon">🏠</span><span class="menu-text">Dashboard</span>
      </a>
      <a href="data.php" class="menu-item active" href="data.php">
        <span class="menu-icon">📊</span><span class="menu-text">Data Tsunami</span>
      </a>
      <a href="sejarah.html" class="menu-item" href="sejarah.html">
        <span class="menu-icon">📜</span><span class="menu-text">Sejarah Kejadian</span>
      </a>
      <a href="penyebab.html" class="menu-item" href="penyebab.html">
        <span class="menu-icon">⚡</span><span class="menu-text">Penyebab</span>
      </a>
      <a href="dampak.html" class="menu-item" href="dampak.html">
        <span class="menu-icon">🚨</span><span class="menu-text">Dampak</span>
      </a>
      <a href="mitigasi.html" class="menu-item" href="mitigasi.html">
        <span class="menu-icon">🧭</span><span class="menu-text">Mitigasi</span>
      </a>
      <a href="video.html" class="menu-item" href="videoedukasi.html">
        <span class="menu-icon">🎥</span><span class="menu-text">Video Edukasi</span>
      </a>
      <a href="peta.html" class="menu-item" href="peta.html">
        <span class="menu-icon">🗺</span><span class="menu-text">Peta Sebaran</span>
      </a>
    </div>
    <button class="menu-toggle-mobile" onclick="toggleMobileSidebar()">☰</button>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
      
      <!-- 1. Bagian Grafik Statistik -->
      <div class="card">
        <div class="card-title">
            <span class="card-title-icon">📈</span>
            Statistik Frekuensi Kejadian Tsunami (Tahun ke Tahun)
        </div>
        <div class="chart-container">
            <canvas id="tsunamiChart"></canvas>
        </div>
        <p style="margin-top: 15px; font-size: 13px; color: #666;">
            Grafik ini menunjukkan jumlah kejadian tsunami yang tercatat dalam sistem berdasarkan tahun kejadian.
        </p>
      </div>

      <!-- 2. Bagian Tabel Data -->
      <div class="card">
        <div class="card-title">
            <span class="card-title-icon">📋</span>
            Daftar Riwayat Kejadian Tsunami
        </div>
        
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Waktu Kejadian</th>
                        <th>Lokasi</th>
                        <th>Magnitudo</th>
                        <th>Kedalaman</th>
                        <th>Dampak</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    // Pastikan variabel $koneksi tersedia (sudah di-include di atas)
                    if (isset($koneksi)) {
                        $query = mysqli_query($koneksi, "SELECT * FROM data_tsunami ORDER BY tanggal DESC");
                        
                        if($query && mysqli_num_rows($query) > 0){
                            while ($row = mysqli_fetch_assoc($query)) {
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>
                                <strong><?= date('d F Y', strtotime($row['tanggal'])); ?></strong><br>
                                <span style="color: #666; font-size: 12px;"><?= $row['waktu']; ?> WIB</span>
                            </td>
                            <td><?= $row['lokasi']; ?></td>
                            <td><span class="badge badge-magnitude"><?= $row['magnitudo']; ?> SR</span></td>
                            <td><?= $row['kedalaman']; ?></td>
                            <td><?= $row['dampak']; ?></td>
                        </tr>
                        <?php 
                            } 
                        } else {
                            echo "<tr><td colspan='6' style='text-align:center;'>Belum ada data tsunami yang tercatat.</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center; color:red;'>Koneksi database gagal! Cek file koneksi.php</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
      </div>
    </div>

    <!-- JAVASCRIPT -->
    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("collapsed");
            document.getElementById("mainContent").classList.toggle("expanded");
        }

        function toggleMobileSidebar() {
            document.getElementById("sidebar").classList.toggle("mobile-open");
        }

        // --- SCRIPT CHART.JS ---
        try {
            const rawLabels = document.getElementById('dataTahun').value;
            const rawJumlah = document.getElementById('dataJumlah').value;

            const labels = JSON.parse(rawLabels);
            const dataJumlah = JSON.parse(rawJumlah);

            const ctx = document.getElementById('tsunamiChart').getContext('2d');
            const myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Kejadian',
                        data: dataJumlah,
                        backgroundColor: '#648db3',
                        borderColor: '#5a7fa0',
                        borderWidth: 1,
                        borderRadius: 5,
                        barThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 },
                            grid: { color: '#f0f0f0' }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        } catch (e) {
            console.error("Gagal memuat grafik: ", e);
        }
    </script>
  </body>
</html>