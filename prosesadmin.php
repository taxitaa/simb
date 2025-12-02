<?php
// =================================================================
// FILE PEMROSESAN DATA (CRUD)
// File ini bertugas menerima kiriman data dari form (pageadmin.php)
// dan melakukan operasi Create, Update, atau Delete ke database.
// =================================================================

// [1. KONEKSI]
// Menghubungkan script ini dengan database MySQL.
// Pastikan file 'koneksi.php' sudah berisi kredensial yang benar.
include 'koneksi.php';

// =================================================================
// 1. LOGIKA TAMBAH DATA (CREATE)
// =================================================================
// Mengecek apakah tombol 'Simpan' (name="simpan_data") pada form Tambah Data ditekan?
if (isset($_POST['simpan_data'])) {
    
    // [A. TANGKAP DATA]
    // Mengambil data yang dikirimkan melalui method POST dari form.
    // Variabel sebelah kiri ($...) adalah variabel PHP.
    // Variabel dalam $_POST['...'] harus sesuai dengan attribute 'name' di input HTML.
    $tanggal    = $_POST['tanggal'];
    $waktu      = $_POST['waktu'];
    $lokasi     = $_POST['lokasi'];
    $magnitudo  = $_POST['magnitudo'];
    $kedalaman  = $_POST['kedalaman'];
    $dampak     = $_POST['dampak'];

    // [B. SUSUN QUERY]
    // Membuat perintah SQL untuk MEMASUKKAN (INSERT) data baru ke tabel 'data_tsunami'.
    // Struktur: INSERT INTO nama_tabel (kolom1, kolom2...) VALUES (nilai1, nilai2...)
    $query = "INSERT INTO data_tsunami (tanggal, waktu, lokasi, magnitudo, kedalaman, dampak) 
              VALUES ('$tanggal', '$waktu', '$lokasi', '$magnitudo', '$kedalaman', '$dampak')";
    
    // [C. EKSEKUSI QUERY]
    // Menjalankan query ke database menggunakan fungsi mysqli_query.
    if (mysqli_query($koneksi, $query)) {
        // [SUKSES]
        // Jika berhasil, tampilkan popup alert Javascript dan redirect kembali ke halaman admin.
        echo "<script>alert('Data berhasil ditambahkan!'); window.location='pageadmin.php';</script>";
    } else {
        // [GAGAL]
        // Jika gagal (misal: salah nama kolom), tampilkan pesan error MySQL untuk debugging.
        echo "Error: " . $query . "<br>" . mysqli_error($koneksi);
    }
}

// =================================================================
// 2. LOGIKA EDIT DATA (UPDATE)
// =================================================================
// Mengecek apakah tombol 'Update' (name="update_data") pada form Edit Data ditekan?
if (isset($_POST['update_data'])) {
    
    // [A. TANGKAP DATA]
    // ID sangat penting untuk menentukan baris mana yang akan diedit.
    // ID ini dikirim melalui input type="hidden" pada form edit.
    $id         = $_POST['id']; 
    $tanggal    = $_POST['tanggal'];
    $waktu      = $_POST['waktu'];
    $lokasi     = $_POST['lokasi'];
    $magnitudo  = $_POST['magnitudo'];
    $kedalaman  = $_POST['kedalaman'];
    $dampak     = $_POST['dampak'];

    // [B. SUSUN QUERY]
    // Membuat perintah SQL untuk MEMPERBARUI (UPDATE) data yang sudah ada.
    // WHERE id='$id' sangat krusial agar tidak semua data ikut terubah.
    $query = "UPDATE data_tsunami SET 
              tanggal='$tanggal', 
              waktu='$waktu', 
              lokasi='$lokasi', 
              magnitudo='$magnitudo', 
              kedalaman='$kedalaman', 
              dampak='$dampak' 
              WHERE id='$id'";

    // [C. EKSEKUSI QUERY]
    if (mysqli_query($koneksi, $query)) {
        // [SUKSES]
        echo "<script>alert('Data berhasil diupdate!'); window.location='pageadmin.php';</script>";
    } else {
        // [GAGAL]
        echo "Error: " . mysqli_error($koneksi);
    }
}

// =================================================================
// 3. LOGIKA HAPUS DATA (DELETE)
// =================================================================
// Mengecek apakah ada parameter 'hapus' di URL (misal: prosesadmin.php?hapus=5).
// Data dikirim melalui method GET (link/URL), bukan POST (form).
if (isset($_GET['hapus'])) {
    
    // [A. TANGKAP ID]
    // Mengambil nilai ID dari URL.
    $id = $_GET['hapus'];
    
    // [B. SUSUN QUERY]
    // Membuat perintah SQL untuk MENGHAPUS (DELETE) data berdasarkan ID.
    // Hati-hati: Tanpa WHERE, semua data di tabel akan terhapus!
    $query = "DELETE FROM data_tsunami WHERE id='$id'";
    
    // [C. EKSEKUSI QUERY]
    if (mysqli_query($koneksi, $query)) {
        // [SUKSES]
        echo "<script>alert('Data berhasil dihapus!'); window.location='pageadmin.php';</script>";
    } else {
        // [GAGAL]
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>