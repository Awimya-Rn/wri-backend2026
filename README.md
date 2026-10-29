# Teori Dasar PHP dan CRUD: Create & Read (Absensi)

## 1. Pengenalan PHP dan CRUD

PHP adalah bahasa pemrograman yang berjalan di server dan dipakai untuk membuat halaman web dinamis, yaitu halaman yang isinya bisa berubah sesuai data di database. CRUD adalah empat operasi dasar pada data: Create, Read, Update, dan Delete. Materi ini membahas dua yang pertama, Create dan Read, memakai contoh aplikasi absensi mahasiswa.

| Huruf | Operasi | Artinya | Perintah SQL | Dibahas di materi ini |
| --- | --- | --- | --- | --- |
| C | Create | Menambah data baru | INSERT | Ya |
| R | Read | Mengambil dan menampilkan data | SELECT | Ya |
| U | Update | Mengubah data | UPDATE | Belum, pertemuan berikutnya |
| D | Delete | Menghapus data | DELETE | Belum, pertemuan berikutnya |

File yang dipakai dalam contoh:

| File | Fungsinya |
| --- | --- |
| index.php | Halaman menu berisi tombol ke create.php dan read.php |
| create.php | Form untuk menambah data absensi |
| read.php | Tabel untuk menampilkan data absensi |
| koneksi.php | Menghubungkan PHP ke database db_absensi |
| api/absensi.php | API yang mengembalikan data dalam format JSON |

Urutan belajarnya:

1. Membuat form input absensi (tampilan, atau Frontend).
2. Mengambil data dari database dengan SELECT (Backend).
3. Menampilkan data dalam tabel HTML.
4. Mematangkan form dan validasi input.
5. Menggabungkan hasil SELECT dengan tabel.
6. Merapikan tampilan tabel sebelum masuk ke materi edit data.

## 2. Cara Kerja PHP

PHP dijalankan oleh server, bukan oleh browser. Browser hanya menerima hasil akhirnya berupa HTML, sehingga pengguna tidak pernah melihat kode PHP kita.

Alur sederhana saat membuka read.php:

1. Pengguna membuka alamat read.php di browser (ini disebut request).
2. Server menerima request dan menjalankan kode PHP di dalam read.php.
3. PHP menghubungi database MySQL lewat koneksi.php, lalu menjalankan query SELECT.
4. PHP menyusun hasilnya menjadi HTML.
5. Server mengirim HTML itu ke browser, dan browser menampilkannya sebagai halaman.

Karena itu file PHP tidak bisa dibuka dengan klik dua kali. File harus dijalankan lewat server lokal seperti XAMPP, dan dibuka lewat alamat seperti `http://localhost/absensi/read.php`.

Dalam materi ini pekerjaan dibagi menjadi dua sisi:

| Sisi | Isinya | Contoh pada proyek absensi |
| --- | --- | --- |
| Frontend (FE) | Tampilan yang dilihat pengguna: HTML dan CSS | Form di create.php, tabel di read.php |
| Backend (BE) | Proses di server: PHP dan database | Query INSERT dan SELECT, koneksi database |

## 3. Template Form Input Absensi

Form adalah cara pengguna mengirim data ke server. Di HTML, form dibuat dengan tag `<form>` dan di dalamnya ada kolom isian seperti `<input>` dan `<select>`.

```html
<form action="create.php" method="POST">
    <input type="text" name="nama" required>
    <input type="text" name="nim" required>
    <select name="status" required>
        <option value="Hadir">Hadir</option>
        <option value="Izin">Izin</option>
        <option value="Sakit">Sakit</option>
        <option value="Alpa">Alpa</option>
    </select>
    <input type="date" name="tanggal" required>
    <button type="submit" name="simpan">Simpan Data</button>
</form>
```

Setiap atribut punya tugas sendiri:

| Atribut | Fungsinya | Contoh |
| --- | --- | --- |
| action | Alamat file tujuan data dikirim | action="create.php" |
| method | Cara data dikirim: POST atau GET | method="POST" |
| name | Nama kunci data, dibaca PHP | name="nama" |
| type | Jenis isian | text, date, submit |
| required | Isian wajib diisi sebelum form bisa dikirim | required |

Atribut `name` paling penting. Nilainya harus sama persis dengan kunci yang dibaca PHP. Jika HTML memakai `name="nama"`, PHP membacanya dengan `$_POST['nama']`. Kalau berbeda satu huruf saja, datanya tidak terbaca.

Atribut `required` adalah validasi di sisi browser (client-side). Ia membantu pengguna agar tidak lupa mengisi, tetapi bisa dilewati oleh orang yang mengirim data lewat cara lain. Karena itu validasi di server tetap diperlukan pada aplikasi sungguhan.

## 4. POST dan GET: Cara Data Dikirim ke PHP

POST dan GET adalah dua cara mengirim data dari browser ke server. PHP menangkap keduanya dengan variabel khusus yang bentuknya array.

| | POST | GET |
| --- | --- | --- |
| Data dikirim lewat | Badan request, tidak terlihat di alamat | Alamat URL, contoh `read.php?status=Hadir` |
| Ditangkap PHP dengan | `$_POST['nama']` | `$_GET['status']` |
| Cocok untuk | Menyimpan atau mengubah data (Create) | Mengambil atau memfilter data (Read) |
| Contoh di proyek | Form di create.php | Dropdown filter di read.php |

Kunci di dalam kurung siku adalah nilai atribut `name` dari input HTML-nya.

```php
$nama   = $_POST['nama'];
$status = $_GET['status'] ?? '';
```

Tanda `??` berarti: pakai nilai di kiri jika ada, kalau tidak ada pakai nilai di kanan. Pada filter, saat halaman pertama kali dibuka belum ada `status` di URL, sehingga `$status` diisi teks kosong agar tidak muncul pesan error.

PHP juga perlu tahu kapan form sudah dikirim. Tombol kirim diberi nama, lalu diperiksa dengan `isset()`:

```php
if (isset($_POST['simpan'])) {
    // kode untuk menyimpan data
}
```

`isset()` bernilai benar jika tombol bernama `simpan` sudah ditekan. Tanpa pemeriksaan ini, kode penyimpanan akan ikut berjalan setiap halaman hanya dibuka.

## 5. Koneksi Database dengan PDO

Sebelum PHP bisa menyimpan atau mengambil data, ia harus terhubung ke database. Hubungan ini ditulis sekali di file koneksi.php, lalu dipakai di file lain dengan `include`.

### Langkah 1: Menyiapkan database di phpMyAdmin

phpMyAdmin adalah aplikasi web untuk mengelola database MySQL lewat tampilan, tanpa mengetik perintah di terminal. Perlu dipahami bahwa phpMyAdmin tidak "disambungkan" ke PHP. Yang disambungkan ke PHP adalah database MySQL-nya, sedangkan phpMyAdmin dipakai untuk membuat dan melihat isi database tersebut.

1. Pasang XAMPP, lalu buka XAMPP Control Panel dan tekan **Start** pada **Apache** dan **MySQL**. Keduanya harus berwarna hijau.
2. Buka `http://localhost/phpmyadmin` di browser.
3. Klik **New** (atau **Baru**) di panel kiri, isi nama database dengan `db_absensi`, pilih collation `utf8mb4_general_ci`, lalu klik **Create**.
4. Pilih database `db_absensi`, buka tab **SQL**, tempel perintah berikut, lalu klik **Go**:

```sql
CREATE TABLE tb_absensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    nim VARCHAR(50) NOT NULL,
    status VARCHAR(10) NOT NULL,
    tanggal DATE NOT NULL
);
```

5. Tabel `tb_absensi` sekarang muncul di panel kiri. Kolom `id` terisi otomatis karena memakai `AUTO_INCREMENT`.
6. Simpan folder proyek di dalam folder `htdocs` milik XAMPP, misalnya `C:\xampp\htdocs\absensi`. Dengan begitu proyek bisa dibuka di `http://localhost/absensi/`.

### Langkah 2: Menulis koneksi di PHP

Nilai di koneksi.php harus sama dengan yang ada di phpMyAdmin dan XAMPP:

| Di koneksi.php | Nilai pada XAMPP bawaan | Asalnya dari |
| --- | --- | --- |
| `$host` | `localhost` | Server MySQL berjalan di komputer sendiri |
| `$user` | `root` | Pengguna bawaan MySQL, sama dengan yang dipakai untuk masuk phpMyAdmin |
| `$pass` | kosong (`""`) | XAMPP bawaan tidak memberi password pada root |
| `$db` | `db_absensi` | Nama database yang dibuat di langkah 1 |

PDO adalah cara PHP berbicara dengan banyak jenis database. Hasil koneksinya disimpan di variabel `$pdo`.

```php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_absensi";

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
```

Setiap bagian punya arti:

| Bagian | Artinya |
| --- | --- |
| `$host` | Lokasi server database, di komputer sendiri berarti localhost |
| `$user` dan `$pass` | Nama pengguna dan kata sandi MySQL (di XAMPP bawaan: root dan kosong) |
| `$db` | Nama database yang dipakai, yaitu db_absensi |
| `new PDO(...)` | Membuat sambungan ke database |

Di file lain, cukup tulis satu baris di bagian atas:

```php
include 'koneksi.php';
```

Setelah itu variabel `$pdo` langsung bisa dipakai. Dengan satu file koneksi, kalau password atau nama database berubah, hanya satu tempat yang perlu diubah.

### Langkah 3: Menguji koneksi

Buka `http://localhost/absensi/read.php`. Jika koneksi berhasil, halaman tampil dan tabel muncul (kosong jika belum ada data). Tambahkan satu data lewat create.php, lalu cek di phpMyAdmin: buka database `db_absensi`, klik tabel `tb_absensi`, lalu tab **Browse**. Data yang baru dikirim dari form harus muncul di sana.

Jika koneksi gagal, periksa pesan errornya:

| Pesan atau gejala | Penyebab | Cara memperbaiki |
| --- | --- | --- |
| Connection refused | MySQL belum dijalankan | Tekan **Start** pada MySQL di XAMPP Control Panel |
| Unknown database `db_absensi` | Database belum dibuat atau namanya berbeda | Buat database di phpMyAdmin, samakan dengan `$db` |
| Access denied for user | `$user` atau `$pass` salah | Samakan dengan akun MySQL; pada XAMPP bawaan: `root` dan password kosong |
| Table `tb_absensi` doesn't exist | Tabel belum dibuat | Jalankan perintah `CREATE TABLE` di tab SQL phpMyAdmin |
| MySQL tidak mau start | Port 3306 dipakai program lain | Matikan program tersebut, atau ganti port MySQL lalu tambahkan `port=3307` di DSN, misalnya `mysql:host=localhost;port=3307;dbname=$db` |

Perhatikan nama variabelnya. Koneksi pada proyek ini memakai `$pdo`. Jika create.php atau read.php memakai `$koneksi` dan fungsi `mysqli_...`, halaman akan error karena variabel itu tidak pernah dibuat di koneksi.php.

## 6. CREATE: Menyimpan Data dengan INSERT

Create berarti menambah baris baru ke tabel. Di SQL perintahnya `INSERT INTO`, dan di PHP perintah ini dijalankan setelah form dikirim.

```php
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
```

Langkah-langkahnya:

1. `include 'koneksi.php'` menyambungkan PHP ke database.
2. `isset($_POST['simpan'])` memastikan form benar-benar sudah dikirim.
3. Data dari form diambil dengan `$_POST` dan disimpan ke variabel.
4. `prepare()` menyiapkan perintah INSERT. Tanda tanya (`?`) adalah tempat kosong untuk nilai.
5. `execute()` mengisi tempat kosong itu berurutan dengan isi array, lalu menjalankan perintahnya.
6. `header("Location: read.php")` memindahkan pengguna ke halaman tabel, dan `exit` menghentikan sisa kode.

Urutan tanda tanya harus sama dengan urutan nilai di dalam array:

| Urutan | Tanda tanya mewakili | Nilai di execute | Kolom tabel |
| --- | --- | --- | --- |
| 1 | ? pertama | `$nama` | nama |
| 2 | ? kedua | `$nim` | nim |
| 3 | ? ketiga | `$status` | status |
| 4 | ? keempat | `$tanggal` | tanggal |

Kolom `id` tidak ditulis karena diisi otomatis oleh database (auto increment).

## 7. READ: Mengambil Data dengan SELECT

Read berarti mengambil data dari tabel untuk ditampilkan. Di SQL perintahnya `SELECT`. Tanda bintang (`*`) berarti semua kolom.

```php
include 'koneksi.php';

$data_query = $pdo->query("SELECT * FROM tb_absensi ORDER BY id DESC");
```

Perintah di atas mengambil semua baris dari tb_absensi. `ORDER BY id DESC` mengurutkan dari id terbesar, sehingga data terbaru tampil paling atas. Hasilnya disimpan di `$data_query`, lalu diambil satu baris demi satu dengan `fetch()` di dalam perulangan (dibahas di bagian 8).

### Menambahkan filter

Filter dikirim lewat URL, misalnya `read.php?status=Hadir`. PHP menangkapnya dengan `$_GET`, lalu memilih query yang sesuai:

```php
$status = $_GET['status'] ?? '';

if ($status != '') {
    $data_query = $pdo->prepare("SELECT * FROM tb_absensi WHERE status = ? ORDER BY id DESC");
    $data_query->execute([$status]);
} else {
    $data_query = $pdo->query("SELECT * FROM tb_absensi ORDER BY id DESC");
}
```

Jika filter ada, data dibatasi dengan `WHERE status = ?`. Jika tidak ada, semua data diambil.

Kapan memakai `query()` dan kapan `prepare()`:

| Situasi | Pakai | Alasan |
| --- | --- | --- |
| Query tetap, tidak membawa data dari pengguna | `query()` | Lebih singkat |
| Query membawa data dari pengguna (form atau URL) | `prepare()` lalu `execute()` | Aman dari SQL injection |

## 8. Template Tabel Absensi

Data dari database ditampilkan dalam tabel HTML. Tabel punya beberapa bagian yang masing-masing punya tugas:

| Tag | Fungsinya |
| --- | --- |
| `<table>` | Pembungkus seluruh tabel |
| `<thead>` | Bagian kepala tabel, tempat judul kolom |
| `<tbody>` | Bagian isi tabel, tempat baris data |
| `<tr>` | Satu baris |
| `<th>` | Sel judul kolom |
| `<td>` | Sel berisi data |

Baris data tidak ditulis satu per satu. Perulangan `while` di PHP dipakai agar satu baris HTML diulang sebanyak jumlah data:

```php
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
```

Cara membacanya:

1. `$data_query->fetch()` mengambil satu baris hasil query sebagai array, misalnya `$data['nama']`.
2. `while` terus mengulang selama masih ada baris. Saat data habis, `fetch()` bernilai kosong dan perulangan berhenti.
3. `<?= ... ?>` adalah singkatan untuk mencetak nilai ke halaman, sama dengan `<?php echo ... ?>`.
4. `$no++` memberi nomor urut 1, 2, 3, dan seterusnya. Nomor ini bukan id dari database.
5. Kunci seperti `'nama'` harus sama dengan nama kolom di tabel tb_absensi.

Agar tabel nyaman dilihat di layar kecil, bungkus tabel dengan `<div class="table-responsive">`. Di style.css kelas ini membuat tabel bisa digeser ke samping jika lebarnya melebihi layar. Pastikan juga ada tag `<meta name="viewport" content="width=device-width, initial-scale=1.0">` di bagian `<head>`.

## 9. Keamanan Dasar: SQL Injection dan Prepared Statement

SQL injection terjadi ketika isian pengguna ikut menjadi bagian dari perintah SQL, sehingga pengguna bisa mengubah isi perintahnya. Ini salah satu celah paling umum pada aplikasi PHP pemula.

Contoh cara yang rawan, karena nilai langsung ditempel ke dalam query:

```php
$query = "INSERT INTO tb_absensi (nama, nim, status, tanggal) VALUES ('$nama', '$nim', '$status', '$tanggal')";
```

Jika seseorang mengetik tanda kutip dan perintah SQL pada kolom nama, tanda kutip itu dapat menutup teks lebih awal, dan sisa ketikannya dibaca database sebagai perintah. Akibatnya data bisa rusak atau terhapus.

Cara yang aman memakai prepared statement. Perintah SQL dan datanya dikirim terpisah, sehingga isian pengguna selalu dianggap data, bukan perintah:

```php
$stmt = $pdo->prepare("INSERT INTO tb_absensi (nama, nim, status, tanggal) VALUES (?, ?, ?, ?)");
$stmt->execute([$nama, $nim, $status, $tanggal]);
```

| | Tempel langsung | Prepared statement |
| --- | --- | --- |
| Bentuk | `VALUES ('$nama', ...)` | `VALUES (?, ...)` dan `execute([...])` |
| Aman dari SQL injection | Tidak | Ya |
| Dipakai di proyek ini | Tidak | Ya |

Satu hal lagi untuk diingat. Saat data dicetak ke halaman (`<?= $data['nama']; ?>`), isian yang berisi tag HTML akan dijalankan oleh browser. Untuk aplikasi sungguhan, bungkus dengan `htmlspecialchars()` agar tampil sebagai teks biasa:

```php
<td><?= htmlspecialchars($data['nama']); ?></td>
```

## 10. Kesalahan Umum Pemula

| Gejala | Penyebab yang sering terjadi | Cara memperbaiki |
| --- | --- | --- |
| Muncul tulisan kode PHP mentah di browser | File dibuka dengan klik dua kali, bukan lewat server | Jalankan Apache di XAMPP, buka lewat `http://localhost/...` |
| Undefined variable `$koneksi` atau `$pdo` | Nama variabel koneksi tidak sama dengan yang dibuat di koneksi.php | Pakai nama variabel yang sama persis di semua file |
| Undefined array key `nama` | Nilai `name` di HTML berbeda dengan kunci di `$_POST` atau `$_GET` | Samakan penulisannya, termasuk huruf besar dan kecil |
| Data tidak masuk ke database | Form memakai `method="GET"` tetapi dibaca dengan `$_POST` | Samakan method form dengan `$_POST` atau `$_GET` |
| Halaman putih kosong | Ada error PHP yang tidak ditampilkan | Cek tanda titik koma, kurung, dan tanda kutip; lihat log error Apache |
| Filter status tidak jalan | Nilai `value` di option berbeda dengan isi kolom status | Samakan nilainya, misalnya Hadir, bukan hadir |
| Tabel tampil tetapi kosong | Tabel tb_absensi belum berisi data | Tambah data lewat create.php, lalu muat ulang read.php |
| Pesan Cannot modify header information | Ada teks atau spasi tercetak sebelum `header()` | Pastikan `header()` dijalankan sebelum HTML apa pun dicetak |

## 11. Ringkasan dan Latihan

Create dan Read adalah dua operasi pertama dalam CRUD: Create menyimpan data baru dengan form dan INSERT, Read mengambil data dengan SELECT lalu menampilkannya di tabel.

Poin yang perlu diingat:

- Form mengirim data lewat `action` dan `method`; setiap isian punya `name`.
- PHP membaca data dengan `$_POST['nama']` (form) atau `$_GET['status']` (URL).
- Koneksi database ditulis sekali di koneksi.php dan dipakai dengan `include`.
- INSERT dipakai untuk Create, SELECT untuk Read.
- Prepared statement (`prepare` dan `execute`) melindungi dari SQL injection.
- `while` dengan `fetch()` mengubah hasil query menjadi baris tabel.
- Atribut `required` memvalidasi di browser, bukan menggantikan validasi server.

Latihan:

- [ ] Tambahkan satu kolom baru, misalnya keterangan, di form create.php dan tabel read.php.
- [ ] Ubah urutan data di read.php menjadi dari yang paling lama (ASC).
- [ ] Tambahkan filter berdasarkan tanggal di read.php.
- [ ] Bungkus semua data yang dicetak di read.php dengan `htmlspecialchars()`.
- [ ] Coba isi form dengan tanda kutip pada kolom nama, lalu amati bahwa data tetap tersimpan aman.