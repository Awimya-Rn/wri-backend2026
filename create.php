<?php
include 'koneksi.php';

if (isset($_POST['simpan'])) {
  $nama    = $_POST['nama'];
  $nim     = $_POST['nim'];
  $status  = $_POST['status'];
  $tanggal = $_POST['tanggal'];

  $stmt = $pdo->prepare("INSERT INTO tb_absensi (nama, nim, status, tanggal) VALUES (?, ?, ?, ?)");
  $stmt->execute([$nama, $nim, $status, $tanggal]);
  header("Location: read.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Tambah Absensi</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      justify-content: center;
      align-items: center;
      padding: 24px;
    }

    .edit-card {
      background: white;
      padding: 32px;
      border-radius: 12px;
      border: 1px solid #f1f5f9;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 500px;
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .edit-card h2 {
      font-size: 1.25rem;
      font-weight: 700;
      color: #0f172a;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 6px;
      margin-bottom: 16px;
    }

    .form-label {
      font-size: 0.875rem;
      font-weight: 600;
      color: #475569;
    }

    .btn-group {
      display: flex;
      gap: 10px;
      margin-top: 10px;
    }

    .btn-secondary {
      background-color: #e2e8f0;
      color: #334155;
      border: none;
      padding: 10px 16px;
      border-radius: 8px;
      font-size: 0.875rem;
      font-weight: 500;
      cursor: pointer;
      text-decoration: none;
      text-align: center;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .btn-secondary:hover {
      background-color: #cbd5e1;
    }
  </style>
</head>

<body>

  <div class="edit-card">
    <h2>Tambah Data Absensi</h2>

    <form action="create.php" method="POST">
      <div class="form-group">
        <label class="form-label">Nama Mahasiswa</label>
        <input type="text" name="nama" class="form-control" required>
      </div>
      <div class="form-group">
        <label class="form-label">NIM</label>
        <input type="text" name="nim" class="form-control" required>
      </div>
      <div class="form-group">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
          <option value="Hadir">Hadir</option>
          <option value="Izin">Izin</option>
          <option value="Sakit">Sakit</option>
          <option value="Alpa">Alpa</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Tanggal</label>
        <input type="date" name="tanggal" class="form-control" required>
      </div>
      <div class="btn-group">
        <button type="submit" name="simpan" class="btn-primary" style="flex: 1; background-color: #059669;">Simpan Data</button>
        <a href="read.php" class="btn-secondary" style="flex: 1;">Batal</a>
      </div>
    </form>
  </div>

</body>

</html>