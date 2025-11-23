<?php
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Tsunami - SIMATSU</title>
    
    <!-- Menggunakan Bootstrap 5 untuk desain yang mirip dengan screenshot kamu -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome untuk Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body { background-color: #f8f9fc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-header { background-color: #4e73df; color: white; font-weight: bold; }
        .btn-primary { background-color: #4e73df; border-color: #4e73df; }
        .btn-primary:hover { background-color: #2e59d9; }
        .table thead { background-color: #4e73df; color: white; }
    </style>
</head>
<body>

<div class="container mt-5 mb-5">
    
    <!-- Judul Halaman -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-secondary"><i class="fas fa-water"></i> Manajemen Data Tsunami</h2>
        <!-- Tombol Tambah memicu Modal -->
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fas fa-plus"></i> Tambah Data
        </button>
    </div>

    <!-- Tabel Data -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0">Daftar Kejadian Tsunami</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Waktu Kejadian</th>
                            <th>Lokasi</th>
                            <th>Magnitudo</th>
                            <th>Kedalaman</th>
                            <th>Dampak</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($koneksi, "SELECT * FROM data_tsunami ORDER BY tanggal DESC");
                        while ($row = mysqli_fetch_assoc($query)) {
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td>
                                <strong><?= date('d-m-Y', strtotime($row['tanggal'])); ?></strong><br>
                                <small class="text-muted"><?= $row['waktu']; ?> WIB</small>
                            </td>
                            <td><?= $row['lokasi']; ?></td>
                            <td class="text-center"><span class="badge bg-warning text-dark"><?= $row['magnitudo']; ?> SR</span></td>
                            <td class="text-center"><?= $row['kedalaman']; ?></td>
                            <td><?= $row['dampak']; ?></td>
                            <td class="text-center">
                                <!-- Tombol Edit -->
                                <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <!-- Tombol Hapus -->
                                <a href="prosesadmin.php?hapus=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Modal Edit Data (Looping untuk setiap baris) -->
                        <div class="modal fade" id="modalEdit<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-info text-white">
                                        <h5 class="modal-title">Edit Data Tsunami</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="prosesadmin.php" method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                            <div class="mb-3">
                                                <label>Tanggal</label>
                                                <input type="date" name="tanggal" class="form-control" value="<?= $row['tanggal']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Waktu (WIB)</label>
                                                <input type="time" name="waktu" class="form-control" value="<?= $row['waktu']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Lokasi</label>
                                                <input type="text" name="lokasi" class="form-control" value="<?= $row['lokasi']; ?>" required>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label>Magnitudo (SR)</label>
                                                    <input type="number" step="0.1" name="magnitudo" class="form-control" value="<?= $row['magnitudo']; ?>" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label>Kedalaman (ex: 10 km)</label>
                                                    <input type="text" name="kedalaman" class="form-control" value="<?= $row['kedalaman']; ?>" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label>Dampak / Keterangan</label>
                                                <textarea name="dampak" class="form-control" rows="3" required><?= $row['dampak']; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" name="update_data" class="btn btn-info text-white">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- End Modal Edit -->

                        <?php } ?>
                        
                        <?php if(mysqli_num_rows($query) == 0) { ?>
                            <tr><td colspan="7" class="text-center">Belum ada data tsunami.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Data -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Tambah Data Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="prosesadmin.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Waktu (WIB)</label>
                        <input type="time" name="waktu" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Lokasi</label>
                        <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Perairan Selatan Jawa" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Magnitudo (SR)</label>
                            <input type="number" step="0.1" name="magnitudo" class="form-control" placeholder="0.0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Kedalaman</label>
                            <input type="text" name="kedalaman" class="form-control" placeholder="Contoh: 10 km" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Dampak / Keterangan</label>
                        <textarea name="dampak" class="form-control" rows="3" placeholder="Deskripsi singkat kejadian..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="simpan_data" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tombol Keluar Floating (Pojok Kiri Bawah) -->
<a href="simb.html" class="btn btn-danger position-fixed bottom-0 start-0 m-4 shadow-lg" style="z-index: 1000; border-radius: 50px; padding: 10px 20px;">
    <i class="fas fa-sign-out-alt me-2"></i> Keluar
</a>

<!-- Script Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>