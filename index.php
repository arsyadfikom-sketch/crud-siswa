<?php

// ==========================
// KONEKSI DATABASE RDS
// ==========================

$host = "localhost";
$user = "root";
$pass = "";
$db   = "sekolah";

$conn = mysqli_connect($host, $user, $pass, $db);

if(!$conn){
    die("Koneksi gagal : " . mysqli_connect_error());
}

// ==========================
// BUAT TABEL JIKA BELUM ADA
// ==========================

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS siswa (

    id INT AUTO_INCREMENT PRIMARY KEY,
    nis VARCHAR(20),
    nama VARCHAR(100),
    jenis_kelamin VARCHAR(20),
    alamat TEXT,
    jurusan VARCHAR(100),
    foto VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

)");

// ==========================
// FOLDER UPLOAD
// ==========================

if(!is_dir("upload")){
    mkdir("upload");
}

// ==========================
// TAMBAH DATA
// ==========================

if(isset($_POST['simpan'])){

    $nis = $_POST['nis'];
    $nama = $_POST['nama'];
    $jk = $_POST['jk'];
    $alamat = $_POST['alamat'];
    $jurusan = $_POST['jurusan'];

    $foto = $_FILES['foto']['name'];
    $tmp  = $_FILES['foto']['tmp_name'];

    $nama_foto = time().'_'.$foto;

    move_uploaded_file($tmp, "upload/".$nama_foto);

    mysqli_query($conn, "INSERT INTO siswa VALUES(

        NULL,
        '$nis',
        '$nama',
        '$jk',
        '$alamat',
        '$jurusan',
        '$nama_foto',
        NOW()

    )");

    header("Location:index.php");
}

// ==========================
// HAPUS DATA
// ==========================

if(isset($_GET['hapus'])){

    $id = $_GET['hapus'];

    $data = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM siswa WHERE id='$id'"));

    if(file_exists("upload/".$data['foto'])){
        unlink("upload/".$data['foto']);
    }

    mysqli_query($conn, "DELETE FROM siswa WHERE id='$id'");

    header("Location:index.php");
}

// ==========================
// EDIT DATA
// ==========================

if(isset($_POST['update'])){

    $id = $_POST['id'];

    $nis = $_POST['nis'];
    $nama = $_POST['nama'];
    $jk = $_POST['jk'];
    $alamat = $_POST['alamat'];
    $jurusan = $_POST['jurusan'];

    $queryFoto = "";

    if($_FILES['foto']['name'] != ""){

        $foto = $_FILES['foto']['name'];
        $tmp  = $_FILES['foto']['tmp_name'];

        $nama_foto = time().'_'.$foto;

        move_uploaded_file($tmp, "upload/".$nama_foto);

        $queryFoto = ", foto='$nama_foto'";
    }

    mysqli_query($conn, "UPDATE siswa SET

        nis='$nis',
        nama='$nama',
        jenis_kelamin='$jk',
        alamat='$alamat',
        jurusan='$jurusan'
        $queryFoto

        WHERE id='$id'
    
    ");

    header("Location:index.php");
}

?>

<!DOCTYPE html>
<html>
<head>

    <title>CRUD Data Siswa</title>

    <style>

        body{
            font-family:Arial;
            background:#f4f4f4;
            margin:0;
            padding:30px;
        }

        .container{
            background:white;
            padding:20px;
            border-radius:10px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
        }

        h2{
            margin-top:0;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
        }

        table th, table td{
            border:1px solid #ddd;
            padding:10px;
            text-align:center;
        }

        table th{
            background:#007bff;
            color:white;
        }

        input, textarea, select{
            width:100%;
            padding:10px;
            margin-top:5px;
            margin-bottom:15px;
        }

        .btn{
            padding:10px 15px;
            border:none;
            color:white;
            border-radius:5px;
            cursor:pointer;
            text-decoration:none;
        }

        .btn-simpan{
            background:green;
        }

        .btn-edit{
            background:orange;
        }

        .btn-hapus{
            background:red;
        }

        img{
            border-radius:5px;
        }

    </style>

</head>
<body>

<div class="container">

<?php

// ==========================
// FORM EDIT
// ==========================

if(isset($_GET['edit'])){

    $id = $_GET['edit'];

    $edit = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM siswa WHERE id='$id'"));

?>

<h2>Edit Data Siswa</h2>

<form method="POST" enctype="multipart/form-data">

    <input type="hidden" name="id" value="<?= $edit['id']; ?>">

    <label>NIS</label>
    <input type="text" name="nis" value="<?= $edit['nis']; ?>" required>

    <label>Nama</label>
    <input type="text" name="nama" value="<?= $edit['nama']; ?>" required>

    <label>Jenis Kelamin</label>

    <select name="jk">

        <option <?= ($edit['jenis_kelamin']=='Laki-laki') ? 'selected':''; ?>>
            Laki-laki
        </option>

        <option <?= ($edit['jenis_kelamin']=='Perempuan') ? 'selected':''; ?>>
            Perempuan
        </option>

    </select>

    <label>Alamat</label>
    <textarea name="alamat"><?= $edit['alamat']; ?></textarea>

    <label>Jurusan</label>
    <input type="text" name="jurusan" value="<?= $edit['jurusan']; ?>">

    <label>Foto</label>
    <input type="file" name="foto">

    <button type="submit" name="update" class="btn btn-edit">
        Update
    </button>

</form>

<?php } else { ?>

<h2>Tambah Data Siswa</h2>

<form method="POST" enctype="multipart/form-data">

    <label>NIS</label>
    <input type="text" name="nis" required>

    <label>Nama</label>
    <input type="text" name="nama" required>

    <label>Jenis Kelamin</label>

    <select name="jk">

        <option>Laki-laki</option>
        <option>Perempuan</option>

    </select>

    <label>Alamat</label>
    <textarea name="alamat"></textarea>

    <label>Jurusan</label>
    <input type="text" name="jurusan">

    <label>Foto</label>
    <input type="file" name="foto" required>

    <button type="submit" name="simpan" class="btn btn-simpan">
        Simpan
    </button>

</form>

<?php } ?>

<hr>

<h2>Data Siswa</h2>

<table>

    <tr>
        <th>No</th>
        <th>Foto</th>
        <th>NIS</th>
        <th>Nama</th>
        <th>JK</th>
        <th>Alamat</th>
        <th>Jurusan</th>
        <th>Aksi</th>
    </tr>

    <?php

    $no = 1;

    $query = mysqli_query($conn, "SELECT * FROM siswa ORDER BY id DESC");

    while($data = mysqli_fetch_array($query)){

    ?>

    <tr>

        <td><?= $no++; ?></td>

        <td>
            <img src="upload/<?= $data['foto']; ?>" width="80">
        </td>

        <td><?= $data['nis']; ?></td>
        <td><?= $data['nama']; ?></td>
        <td><?= $data['jenis_kelamin']; ?></td>
        <td><?= $data['alamat']; ?></td>
        <td><?= $data['jurusan']; ?></td>

        <td>

            <a class="btn btn-edit" href="?edit=<?= $data['id']; ?>">
                Edit
            </a>

            <a class="btn btn-hapus"
               href="?hapus=<?= $data['id']; ?>"
               onclick="return confirm('Yakin hapus data?')">
               Hapus
            </a>

        </td>

    </tr>

    <?php } ?>

</table>

</div>

</body>
</html>