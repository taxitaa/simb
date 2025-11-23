<?php
session_start();
include 'koneksi.php';

if (isset($_POST['login'])) {
  $username = mysqli_real_escape_string($koneksi, $_POST['username']);
  $password = mysqli_real_escape_string($koneksi, $_POST['password']);

  $query = mysqli_query($koneksi, "SELECT * FROM admin WHERE username='$username' AND password='$password'");
  $cek = mysqli_num_rows($query);

  if ($cek > 0) {
    $_SESSION['admin'] = $username;
    header("Location: pageadmin.php");
    exit();
  } else {
    $error = "Username atau password salah!";
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin | SIMATSU</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #648db3 0%, #94b4c1 100%);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .login-container {
      background: #fff;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.2);
      width: 400px;
      text-align: center;
    }
    .logo {font-size: 36px; font-weight: 700; color: #648db3; margin-bottom: 10px;}
    .title {font-size: 20px; color: #648db3; font-weight: 600; margin-bottom: 25px;}
    .form-group {margin-bottom: 15px; text-align: left;}
    label {font-size: 14px; color: #555;}
    input {
      width: 100%; padding: 10px; border-radius: 8px;
      border: 1px solid #ccc; font-size: 14px;
    }
    input:focus {border-color: #648db3; outline: none;}
    .btn-login {
      width: 100%; padding: 12px; background: #648db3; border: none;
      border-radius: 8px; color: white; font-size: 15px;
      font-weight: 600; cursor: pointer;
    }
    .btn-login:hover {background: #557a97;}
    .error {color: red; font-size: 13px; margin-bottom: 10px;}
  </style>
</head>
<body>
  <div class="login-container">
    <div class="logo">🌊 SIMATSU</div>
    <div class="title">Login Admin</div>

    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="POST">
      <div class="form-group">
        <label>Username Admin</label>
        <input type="text" name="username" required>
      </div>

      <div class="form-group">
        <label>Kata Sandi</label>
        <input type="password" name="password" required>
      </div>

      <button type="submit" name="login" class="btn-login">Masuk</button>
    </form>
    <p style="margin-top:15px"><a href="simb.html" style="color:#648db3;">← Kembali ke Beranda</a></p>
  </div>
</body>
</html>
