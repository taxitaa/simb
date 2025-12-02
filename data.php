<?php
// =================================================================
// BAGIAN BACKEND (PHP)
// =================================================================

// [DATABASE] Memanggil file koneksi agar terhubung ke database MySQL
include 'koneksi.php'; 

// --- LOGIKA UNTUK GRAFIK (CHART) ---
$tahun_label = [];
$jumlah_data = [];

// [LOGIKA] Cek apakah koneksi berhasil sebelum mengambil data
if (isset($koneksi)) {
    // [DATABASE] Query Khusus Grafik:
    // 1. Mengambil TAHUN dari kolom 'tanggal'.
    // 2. Menghitung JUMLAH kejadian per tahun tersebut.
    // 3. Hanya mengambil data yang tahunnya valid (> 0).
    $query_chart = mysqli_query($koneksi, "SELECT YEAR(tanggal) as tahun, COUNT(*) as jumlah FROM data_tsunami WHERE YEAR(tanggal) > 0 GROUP BY YEAR(tanggal) ORDER BY tahun ASC");

    // [LOOPING] Memasukkan hasil query ke dalam array PHP
    if ($query_chart) {
        while ($chart_row = mysqli_fetch_assoc($query_chart)) {
            $tahun_label[] = $chart_row['tahun']; // Masukkan tahun ke label
            $jumlah_data[] = $chart_row['jumlah']; // Masukkan jumlah ke data
        }
    }
}

// [DATA TRANSFER] Mengubah Array PHP menjadi format JSON agar bisa dibaca oleh JavaScript (Chart.js)
$json_tahun = json_encode($tahun_label);
if (!$json_tahun) $json_tahun = '[]'; // Jaga-jaga jika kosong

$json_jumlah = json_encode($jumlah_data);
if (!$json_jumlah) $json_jumlah = '[]'; // Jaga-jaga jika kosong
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Data Tsunami - SIMATSU</title>

    <!-- Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Chart.js (Library untuk membuat Grafik) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

    <!-- Leaflet CSS & JS (Library Peta) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>

    <style>
      /* --- CSS STYLING (TAMPILAN) --- */
      :root {
        --primary-dark: #005f99;
        --primary-main: #0088cc;
        --primary-light: #e0f7fa;
        --accent: #ffd700;
        --text-dark: #2c3e50;
        --text-grey: #607d8b;
        --bg-body: #f4f9fc;
        --shadow-card: 0 4px 12px rgba(0, 0, 0, 0.05);
        
        /* Layout Variables */
        --sidebar-width: 260px;
        --header-height: 80px;
        --stats-bar-height: 50px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      }

      * { margin: 0; padding: 0; box-sizing: border-box; }
      body { font-family: "Poppins", sans-serif; background-color: var(--bg-body); color: var(--text-dark); overflow-x: hidden; }

      /* Sidebar Style */
      .sidebar {
        position: fixed; left: 0; top: 0; bottom: 0; width: var(--sidebar-width);
        background: white; box-shadow: 2px 0 15px rgba(0,0,0,0.05); z-index: 1002;
        display: flex; flex-direction: column; transition: var(--transition);
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
        color: var(--text-grey); text-decoration: none; border-radius: 10px;
        margin-bottom: 5px; transition: var(--transition); font-weight: 500; font-size: 14px;
      }
      .menu-item i { width: 24px; text-align: center; color: #b0bec5; font-size: 18px; transition: var(--transition); }
      .menu-item:hover, .menu-item.active { background-color: var(--primary-light); color: var(--primary-main); }
      .menu-item:hover i, .menu-item.active i { color: var(--primary-main); }
      .menu-item.active { font-weight: 600; border-left: 4px solid var(--primary-main); }

      /* Header Style */
      .header {
        position: fixed; top: 0; right: 0; left: var(--sidebar-width);
        width: calc(100% - var(--sidebar-width)); height: var(--header-height);
        background: linear-gradient(135deg, var(--primary-dark), var(--primary-main));
        color: white; z-index: 1000; display: flex; align-items: center;
        padding: 0 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); transition: var(--transition);
      }
      .header-content { display: flex; justify-content: space-between; align-items: center; width: 100%; }
      .header-left { display: flex; align-items: center; gap: 20px; }
      .header-title h1 { font-size: 20px; font-weight: 700; margin-bottom: 2px; }
      .header-title p { font-size: 12px; opacity: 0.9; font-weight: 300; letter-spacing: 0.5px; }
      
      .btn-login {
        background: rgba(255, 255, 255, 0.2); color: white; border: 1px solid rgba(255, 255, 255, 0.4);
        padding: 8px 20px; border-radius: 50px; font-weight: 500; font-size: 13px; cursor: pointer;
        display: flex; align-items: center; gap: 8px; text-decoration: none; transition: var(--transition);
      }
      .btn-login:hover { background: white; color: var(--primary-dark); transform: translateY(-2px); }

      /* Stats Bar Style */
      .stats-bar {
        position: fixed; top: var(--header-height); right: 0; left: var(--sidebar-width);
        width: calc(100% - var(--sidebar-width)); height: var(--stats-bar-height);
        background: white; display: flex; align-items: center; padding: 0 30px;
        border-bottom: 1px solid #edf2f7; z-index: 990; transition: var(--transition);
      }
      .stat-mini-item { margin-right: 30px; font-size: 13px; color: var(--text-grey); font-weight: 500; display: flex; align-items: center; gap: 8px; }
      .stat-mini-item i { color: var(--primary-main); }
      .stat-mini-item span { font-weight: 700; color: var(--text-dark); }

      /* Main Content Style */
      .main-content {
        margin-left: var(--sidebar-width);
        margin-top: calc(var(--header-height) + var(--stats-bar-height));
        padding: 30px 40px; min-height: calc(100vh - 130px); transition: var(--transition);
      }

      /* Cards Style */
      .card { background: white; border-radius: 15px; padding: 30px; box-shadow: var(--shadow-card); margin-bottom: 30px; }
      .card-title {
        font-size: 18px; font-weight: 700; color: var(--primary-dark); margin-bottom: 20px;
        border-bottom: 1px solid #f0f0f0; padding-bottom: 15px; display: flex; align-items: center; gap: 10px;
      }
      .chart-container { position: relative; height: 350px; width: 100%; }

      /* Table Style */
      .table-responsive { overflow-x: auto; border-radius: 10px; }
      .custom-table { width: 100%; border-collapse: collapse; font-size: 14px; white-space: nowrap; }
      .custom-table thead { background-color: var(--primary-light); color: var(--primary-dark); }
      .custom-table th { padding: 15px; text-align: left; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; }
      .custom-table td { padding: 15px; border-bottom: 1px solid #f0f0f0; color: var(--text-dark); }
      .custom-table tbody tr:hover { background-color: #f9fcff; }
      .badge { padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
      .badge-magnitude { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }

      /* Responsive Mobile */
      .mobile-toggle { display: none; font-size: 24px; margin-right: 15px; cursor: pointer; }
      @media (max-width: 992px) {
        .sidebar { transform: translateX(-100%); }
        .sidebar.active { transform: translateX(0); }
        .header { left: 0; width: 100%; padding-left: 20px; }
        .stats-bar { left: 0; width: 100%; }
        .main-content { margin-left: 0; padding: 20px; }
        .mobile-toggle { display: block; }
      }
    </style>
  </head>

  <body>
    <!-- 
      [DATA BRIDGE]
      Input tersembunyi ini berfungsi sebagai 'jembatan' untuk mengirim data dari PHP ke JavaScript.
      Value-nya diisi oleh PHP (echo), lalu nanti diambil oleh JavaScript di bawah.
    -->
    <input type="hidden" id="dataTahun" value='<?php echo $json_tahun; ?>'>
    <input type="hidden" id="dataJumlah" value='<?php echo $json_jumlah; ?>'>

    <!-- Sidebar Menu -->
    <div class="sidebar" id="sidebar">
      <div class="sidebar-logo">
        <img src="assets/logosimbb.png" alt="Logo" class="logo-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
        <div class="logo-text">SIMATSU</div>
      </div>
      
      <div class="sidebar-menu">
        <a href="simb.html" class="menu-item">
          <i class="fa-solid fa-house"></i> Dashboard
        </a>
        <a href="data.php" class="menu-item active"> 
          <i class="fa-solid fa-chart-simple"></i> Data Tsunami
        </a>
        <a href="sejarah.html" class="menu-item">
          <i class="fa-solid fa-clock-rotate-left"></i> Sejarah Kejadian
        </a>
        <a href="penyebab.html" class="menu-item">
          <i class="fa-solid fa-bolt"></i> Penyebab
        </a>
        <a href="dampak.html" class="menu-item">
          <i class="fa-solid fa-triangle-exclamation"></i> Dampak
        </a>
        <a href="mitigasi.html" class="menu-item">
          <i class="fa-solid fa-compass"></i> Mitigasi
        </a>
        <a href="videoedukasi.html" class="menu-item">
          <i class="fa-solid fa-video"></i> Video Edukasi
        </a>
        <a href="peta.html" class="menu-item">
          <i class="fa-solid fa-map-location-dot"></i> Peta Sebaran
        </a>
      </div>
    </div>

    <!-- Header Atas -->
    <div class="header">
      <div class="header-content">
        <div class="header-left">
          <i class="fa-solid fa-bars mobile-toggle" onclick="document.getElementById('sidebar').classList.toggle('active')"></i>
          <div class="header-title">
            <h1>Sistem Informasi Manajemen Tsunami</h1>
            <p>Mengenali, Mencegah, dan Mengurangi Risiko Bencana</p>
          </div>
        </div>
        <a href="loginadmin.php" class="btn-login">
          <i class="fa-solid fa-user-lock"></i> Login Admin
        </a>
      </div>
    </div>

    <!-- Stats Bar (Baris Statistik Kecil) -->
    <div class="stats-bar">
      <div class="stat-mini-item">
        <i class="fa-solid fa-users"></i>
        Pengunjung: <span>12,547</span>
      </div>
      <div class="stat-mini-item">
        <i class="fa-solid fa-newspaper"></i>
        Artikel: <span>287</span>
      </div>
    </div>

    <!-- KONTEN UTAMA -->
    <div class="main-content">
      
      <!-- 1. KARTU GRAFIK -->
      <div class="card">
        <div class="card-title">
            <i class="fa-solid fa-chart-column"></i>
            Statistik Frekuensi Kejadian Tsunami
        </div>
        <div class="chart-container">
            <!-- Canvas ini adalah tempat Chart.js menggambar grafik -->
            <canvas id="tsunamiChart"></canvas>
        </div>
        <p style="margin-top: 15px; font-size: 13px; color: #666; text-align: center;">
            Grafik ini menunjukkan jumlah kejadian tsunami yang tercatat dalam sistem berdasarkan tahun kejadian.
        </p>
      </div>

      <!-- 2. KARTU TABEL DATA -->
      <div class="card">
        <div class="card-title">
            <i class="fa-solid fa-table-list"></i>
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
                    // [PHP] Bagian ini memproses data dari Database untuk ditampilkan di tabel
                    $no = 1;

                    // Cek apakah variabel koneksi tersedia
                    if (isset($koneksi)) {
                        
                        // [DATABASE] Query mengambil SEMUA data dari tabel 'data_tsunami' diurutkan tanggal terbaru
                        $query = mysqli_query($koneksi, "SELECT * FROM data_tsunami ORDER BY tanggal DESC");
                        
                        // Cek apakah ada datanya?
                        if($query && mysqli_num_rows($query) > 0){
                            
                            // [LOOPING] Perulangan while untuk menampilkan baris data satu per satu
                            while ($row = mysqli_fetch_assoc($query)) {
                        ?>
                        <tr>
                            <!-- Menampilkan data menggunakan PHP echo () -->
                            <td><?= $no++; ?></td>
                            <td>
                                <strong style="color: #005f99;"><?= date('d F Y', strtotime($row['tanggal'])); ?></strong><br>
                                <span style="color: #666; font-size: 12px;"><?= $row['waktu']; ?> WIB</span>
                            </td>
                            <td><?= $row['lokasi']; ?></td>
                            <td><span class="badge badge-magnitude"><?= $row['magnitudo']; ?> SR</span></td>
                            <td><?= $row['kedalaman']; ?></td>
                            <td><?= $row['dampak']; ?></td>
                        </tr>
                        <?php 
                            } // Akhir dari Looping while
                        } else {
                            // [PLACEHOLDER] Jika data kosong, tampilkan pesan ini
                            echo "<tr><td colspan='6' style='text-align:center; padding: 20px;'>Belum ada data tsunami yang tercatat.</td></tr>";
                        }
                    } else {
                        // [ERROR] Jika koneksi gagal
                        echo "<tr><td colspan='6' style='text-align:center; color:red; padding: 20px;'>Koneksi database gagal! Cek file koneksi.php</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
      </div>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        // [SCRIPT] Implementasi Chart.js
        try {
            // Mengambil data JSON dari input hidden di atas
            const rawLabels = document.getElementById('dataTahun').value;
            const rawJumlah = document.getElementById('dataJumlah').value;

            // Parsing data JSON menjadi format Array Javascript
            const labels = JSON.parse(rawLabels);
            const dataJumlah = JSON.parse(rawJumlah);

            // Inisialisasi Grafik pada element canvas 'tsunamiChart'
            const ctx = document.getElementById('tsunamiChart').getContext('2d');
            const myChart = new Chart(ctx, {
                type: 'bar', // Tipe grafik batang
                data: {
                    labels: labels, // Data Tahun (Sumbu X)
                    datasets: [{
                        label: 'Jumlah Kejadian',
                        data: dataJumlah, // Data Jumlah (Sumbu Y)
                        backgroundColor: '#0088cc', /* Warna Batang */
                        borderColor: '#005f99',
                        borderWidth: 1,
                        borderRadius: 6,
                        barThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { display: false }, // Sembunyikan legenda
                        tooltip: {
                            backgroundColor: '#005f99',
                            titleFont: { size: 14 },
                            bodyFont: { size: 14 },
                            padding: 10,
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, color: '#607d8b' },
                            grid: { color: '#f0f0f0' }
                        },
                        x: { 
                            grid: { display: false },
                            ticks: { color: '#2c3e50', font: { weight: 'bold' } }
                        }
                    }
                }
            });
        } catch (e) {
            console.error("Gagal memuat grafik: ", e);
        }
    </script>
  </body>
</html>