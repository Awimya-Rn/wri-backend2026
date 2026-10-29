<?php
include 'koneksi.php';

$status = $_GET['status'] ?? '';

if ($status != '') {
  $data_query = $pdo->prepare("SELECT * FROM tb_absensi WHERE status = ? ORDER BY id DESC");
  $data_query->execute([$status]);
} else {
  $data_query = $pdo->query("SELECT * FROM tb_absensi ORDER BY id DESC");
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Absensi</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>

  <main>
    <div class="top-bar">
      <div>
        <h1>Data Absensi</h1>
        <p>Daftar kehadiran mahasiswa</p>
      </div>
      <a href="create.php" class="btn-primary">+ Tambah Data</a>
    </div>

    <div class="card">
      <div class="card-toolbar">
        <form action="read.php" method="GET">
          <select name="status" class="form-select" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="Hadir" <?= $status == 'Hadir' ? 'selected' : ''; ?>>Hadir</option>
            <option value="Izin" <?= $status == 'Izin' ? 'selected' : ''; ?>>Izin</option>
            <option value="Sakit" <?= $status == 'Sakit' ? 'selected' : ''; ?>>Sakit</option>
            <option value="Alpa" <?= $status == 'Alpa' ? 'selected' : ''; ?>>Alpa</option>
          </select>
        </form>
      </div>

      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>NIM</th>
              <th>Status</th>
              <th>Tanggal</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = 1; ?>
            <?php while ($data = $data_query->fetch()) { ?>
              <tr>
                <td><?= $no++; ?></td>
                <td><?= $data['nama']; ?></td>
                <td><?= $data['nim']; ?></td>
                <td><?= $data['status']; ?></td>
                <td><?= $data['tanggal']; ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

</body>

</html>