<?php
include 'koneksi.php';

// --- PROSES TAMBAH DATA ---
if (isset($_POST['simpan_data'])) {
    $tanggal = $_POST['tanggal'];
    $waktu = $_POST['waktu'];
    $lokasi = $_POST['lokasi'];
    $magnitudo = $_POST['magnitudo'];
    $kedalaman = $_POST['kedalaman'];
    $dampak = $_POST['dampak'];

    $query = "INSERT INTO data_tsunami (tanggal, waktu, lokasi, magnitudo, kedalaman, dampak) 
              VALUES ('$tanggal', '$waktu', '$lokasi', '$magnitudo', '$kedalaman', '$dampak')";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data berhasil ditambahkan!'); window.location='pageadmin.php';</script>";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($koneksi);
    }
}

// --- PROSES EDIT DATA ---
if (isset($_POST['update_data'])) {
    $id = $_POST['id'];
    $tanggal = $_POST['tanggal'];
    $waktu = $_POST['waktu'];
    $lokasi = $_POST['lokasi'];
    $magnitudo = $_POST['magnitudo'];
    $kedalaman = $_POST['kedalaman'];
    $dampak = $_POST['dampak'];

    $query = "UPDATE data_tsunami SET 
              tanggal='$tanggal', waktu='$waktu', lokasi='$lokasi', 
              magnitudo='$magnitudo', kedalaman='$kedalaman', dampak='$dampak' 
              WHERE id='$id'";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data berhasil diupdate!'); window.location='pageadmin.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}

// --- PROSES HAPUS DATA ---
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $query = "DELETE FROM data_tsunami WHERE id='$id'";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data berhasil dihapus!'); window.location='pageadmin.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>