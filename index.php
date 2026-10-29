<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Absensi Mahasiswa</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      justify-content: center;
      align-items: center;
      padding: 24px;
    }

    .menu-card {
      background: white;
      padding: 32px;
      border-radius: 12px;
      border: 1px solid #f1f5f9;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 420px;
      display: flex;
      flex-direction: column;
      gap: 20px;
      text-align: center;
    }

    .menu-card h1 {
      font-size: 1.5rem;
      font-weight: 700;
      color: #0f172a;
    }

    .menu-card p {
      font-size: 0.875rem;
      color: #64748b;
    }

    .menu-buttons {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .api-box {
      text-align: left;
    }

    .api-title {
      font-size: 0.75rem;
      font-weight: 600;
      color: #94a3b8;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-bottom: 8px;
    }

    .api-box pre {
      background-color: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 12px;
      font-size: 0.75rem;
      color: #334155;
      max-height: 240px;
      overflow: auto;
      white-space: pre-wrap;
      word-break: break-word;
    }
  </style>
</head>

<body>

  <div class="menu-card">
    <div>
      <h1>Absensi Mahasiswa</h1>
      <p>Pilih menu di bawah ini</p>
    </div>

    <div class="menu-buttons">
      <a href="create.php" class="btn-primary" style="background-color: #059669;">Tambah Data Absensi</a>
      <a href="read.php" class="btn-primary">Lihat Data Absensi</a>
      <a href="api/absensi.php" class="btn-primary" style="background-color: #475569;">Buka API Absensi</a>
    </div>

    <div class="api-box">
      <p class="api-title">Informasi API Absensi</p>
      <pre id="api-result">Memuat data...</pre>
    </div>
  </div>

  <script>
    fetch("api/absensi.php")
      .then(function(response) {
        return response.json();
      })
      .then(function(data) {
        document.getElementById("api-result").textContent = JSON.stringify(data, null, 2);
      })
      .catch(function() {
        document.getElementById("api-result").textContent = "Gagal memuat API Absensi.";
      });
  </script>

</body>

</html>