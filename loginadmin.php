<?php
// =================================================================
// BAGIAN LOGIKA PHP (BACKEND)
// =================================================================

// [1. SESSION] Memulai sesi PHP.
// Fungsi: Wajib diletakkan di baris paling atas sebelum ada output HTML apapun.
// Tujuannya adalah untuk memulai atau melanjutkan sesi penyimpanan data pengguna (seperti status login)
// agar bisa diakses di halaman lain (misalnya di pageadmin.php).
session_start();

// [2. KONEKSI DATABASE]
// Mengimpor file koneksi.php. Pastikan file ini berisi konfigurasi host, user, password, dan nama database yang benar.
include 'koneksi.php';

// [3. PROSES LOGIN]
// Mengecek apakah tombol 'Masuk' (dengan atribut name="login") telah ditekan oleh pengguna?
if (isset($_POST['login'])) {

  // [4. KEAMANAN & SANITASI DATA]
  // Mengambil data yang diketik user di form input.
  // Fungsi mysqli_real_escape_string() SANGAT PENTING untuk mencegah serangan SQL Injection.
  // Fungsi ini akan membersihkan karakter-karakter berbahaya (seperti tanda kutip ') sebelum data dimasukkan ke query database.
  $username = mysqli_real_escape_string($koneksi, $_POST['username']);
  $password = mysqli_real_escape_string($koneksi, $_POST['password']);

  // [5. EKSEKUSI QUERY]
  // Meminta database untuk mencari baris data di tabel 'admin' 
  // di mana kolom 'username' cocok DAN kolom 'password' cocok dengan inputan.
  // Catatan: Dalam aplikasi nyata, password sebaiknya dienkripsi (md5/bcrypt) dan dicek menggunakan password_verify().
  $query = mysqli_query($koneksi, "SELECT * FROM admin WHERE username='$username' AND password='$password'");

  // [6. PENGECEKAN HASIL]
  // Menghitung jumlah baris data yang ditemukan oleh query di atas.
  $cek = mysqli_num_rows($query);

  // Jika jumlah baris > 0, berarti username dan password BENAR (akun ditemukan).
  if ($cek > 0) {
    // [7. LOGIN BERHASIL]
    
    // Menyimpan username ke dalam variabel SESSION global.
    // Ini adalah "tanda pengenal" bahwa user ini sudah login sah.
    $_SESSION['admin'] = $username;

    // Mengarahkan (Redirect) pengguna ke halaman dashboard admin.
    header("Location: pageadmin.php");
    
    // Menghentikan eksekusi script segera setelah redirect untuk keamanan/efisiensi.
    exit(); 
  } else {
    // [8. LOGIN GAGAL]
    // Jika data tidak ditemukan (0 baris), buat pesan error.
    // Pesan ini akan ditampilkan di bagian HTML di bawah.
    $error = "Username atau password yang Anda masukkan salah!";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin | SIMATSU</title>
  
  <!-- Font Poppins (Konsisten dengan halaman lain) -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <style>
    /* Menggunakan Variabel CSS Modern (Sama dengan tema SIMATSU lainnya) */
    :root {
      --primary-dark: #005f99;
      --primary-main: #0088cc;
      --primary-light: #e0f7fa;
      --text-dark: #2c3e50;
      --bg-body: #f4f9fc;
      --shadow-card: 0 10px 30px rgba(0, 95, 153, 0.15);
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--bg-body);
      /* Background gradient halus */
      background: linear-gradient(135deg, var(--bg-body) 0%, #dbeafe 100%);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .login-container {
      background: #fff;
      padding: 40px;
      border-radius: 20px;
      box-shadow: var(--shadow-card);
      width: 100%;
      max-width: 400px;
      text-align: center;
      border: 1px solid rgba(255, 255, 255, 0.5);
      position: relative;
      overflow: hidden;
    }

    /* Hiasan dekoratif di atas kartu */
    .login-container::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 5px;
      background: linear-gradient(90deg, var(--primary-dark), var(--primary-main));
    }

    .logo {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      font-size: 32px;
      font-weight: 800;
      color: var(--primary-main);
      margin-bottom: 5px;
      letter-spacing: -1px;
    }
    
    .logo img { height: 45px; width: auto; }

    .subtitle {
      font-size: 14px;
      color: #64748b;
      margin-bottom: 30px;
    }

    .form-group {
      margin-bottom: 20px;
      text-align: left;
    }

    label {
      font-size: 13px;
      color: var(--text-dark);
      font-weight: 600;
      margin-bottom: 8px;
      display: block;
    }

    .input-wrapper {
      position: relative;
    }

    .input-wrapper i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #94a3b8;
      font-size: 16px;
    }

    input {
      width: 100%;
      padding: 12px 15px 12px 45px; /* Padding kiri lebih besar untuk ikon */
      border-radius: 10px;
      border: 1px solid #e2e8f0;
      font-size: 14px;
      transition: all 0.3s;
      outline: none;
      font-family: 'Poppins', sans-serif;
    }

    input:focus {
      border-color: var(--primary-main);
      box-shadow: 0 0 0 3px rgba(0, 136, 204, 0.1);
    }

    .btn-login {
      width: 100%;
      padding: 12px;
      background: linear-gradient(to right, var(--primary-dark), var(--primary-main));
      border: none;
      border-radius: 10px;
      color: white;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;
      margin-top: 10px;
    }

    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 95, 153, 0.3);
    }

    .error-msg {
      background-color: #fee2e2;
      color: #ef4444;
      padding: 10px;
      border-radius: 8px;
      font-size: 13px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 8px;
      border: 1px solid #fecaca;
    }

    .back-link {
      margin-top: 20px;
      font-size: 13px;
    }

    .back-link a {
      color: var(--primary-main);
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s;
    }

    .back-link a:hover {
      color: var(--primary-dark);
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <!-- Logo Header -->
    <div class="logo">
      <img src="assets/logosimbb.png" alt="Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
      <span>SIMATSU</span>
    </div>
    <p class="subtitle">Silakan login untuk mengelola sistem</p>

    <!-- Menampilkan Error jika login gagal -->
    <?php if (!empty($error)): ?>
      <div class="error-msg">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span><?php echo $error; ?></span>
      </div>
    <?php endif; ?>

    <!-- Form Login -->
    <form method="POST">
      <div class="form-group">
        <label>Username</label>
        <div class="input-wrapper">
          <i class="fa-solid fa-user"></i>
          <input type="text" name="username" placeholder="Masukkan username" required autocomplete="off">
        </div>
      </div>

      <div class="form-group">
        <label>Password</label>
        <div class="input-wrapper">
          <i class="fa-solid fa-lock"></i>
          <input type="password" name="password" placeholder="Masukkan kata sandi" required>
        </div>
      </div>

      <button type="submit" name="login" class="btn-login">
        Masuk Dashboard <i class="fa-solid fa-arrow-right-to-bracket" style="margin-left:5px;"></i>
      </button>
    </form>

    <div class="back-link">
      <a href="simb.html"><i class="fa-solid fa-arrow-left"></i> Kembali ke Halaman Utama</a>
    </div>
  </div>

</body>
</html>