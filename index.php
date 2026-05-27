<?php

require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

/*
=================================================
KONEKSI DATABASE RDS
=================================================
*/

$host = "RDS-ENDPOINT";
$user = "admin";
$pass = "PASSWORD-RDS";
$db   = "db_siswa";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal : " . mysqli_connect_error());
}

/*
=================================================
KONFIGURASI AWS S3
=================================================
*/

$bucketName = "NAMA-BUCKET-S3";

$s3 = new S3Client([
    'version' => 'latest',
    'region'  => 'ap-southeast-3',
    'credentials' => [
        'key'    => 'AWS_ACCESS_KEY',
        'secret' => 'AWS_SECRET_KEY'
    ]
]);

/*
=================================================
UPLOAD FOTO KE S3
=================================================
*/

function uploadFotoS3($file, $s3, $bucketName)
{
    try {

        $fileName = time() . "_" . basename($file['name']);

        $result = $s3->putObject([
            'Bucket'      => $bucketName,
            'Key'         => 'foto-siswa/' . $fileName,
            'SourceFile'  => $file['tmp_name'],
            'ContentType' => $file['type']
        ]);

        return $result['ObjectURL'];

    } catch (AwsException $e) {

        die("Upload S3 gagal : " . $e->getMessage());
    }
}

/*
=================================================
HAPUS FOTO DARI S3
=================================================
*/

function hapusFotoS3($url, $s3, $bucketName)
{
    if ($url == '') {
        return;
    }

    $path = parse_url($url, PHP_URL_PATH);

    $key = ltrim($path, '/');

    try {

        $s3->deleteObject([
            'Bucket' => $bucketName,
            'Key'    => $key
        ]);

    } catch (AwsException $e) {

        echo "Gagal hapus file S3";
    }
}

/*
=================================================
TAMBAH DATA
=================================================
*/

if (isset($_POST['tambah'])) {

    $nis     = htmlspecialchars($_POST['nis']);
    $nama    = htmlspecialchars($_POST['nama']);
    $kelas   = htmlspecialchars($_POST['kelas']);
    $alamat  = htmlspecialchars($_POST['alamat']);

    $fotoUrl = "";

    if ($_FILES['foto']['name'] != '') {

        $fotoUrl = uploadFotoS3(
            $_FILES['foto'],
            $s3,
            $bucketName
        );
    }

    $query = "INSERT INTO siswa
              (nis,nama,kelas,alamat,foto)
              VALUES
              ('$nis','$nama','$kelas','$alamat','$fotoUrl')";

    mysqli_query($conn, $query);

    header("Location:index.php");
    exit;
}

/*
=================================================
HAPUS DATA
=================================================
*/

if (isset($_GET['hapus'])) {

    $id = intval($_GET['hapus']);

    $data = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT * FROM siswa WHERE id='$id'")
    );

    hapusFotoS3($data['foto'], $s3, $bucketName);

    mysqli_query($conn, "DELETE FROM siswa WHERE id='$id'");

    header("Location:index.php");
    exit;
}

/*
=================================================
UPDATE DATA
=================================================
*/

if (isset($_POST['update'])) {

    $id      = intval($_POST['id']);
    $nis     = htmlspecialchars($_POST['nis']);
    $nama    = htmlspecialchars($_POST['nama']);
    $kelas   = htmlspecialchars($_POST['kelas']);
    $alamat  = htmlspecialchars($_POST['alamat']);

    $fotoLama = $_POST['foto_lama'];

    if ($_FILES['foto']['name'] != '') {

        hapusFotoS3($fotoLama, $s3, $bucketName);

        $fotoBaru = uploadFotoS3(
            $_FILES['foto'],
            $s3,
            $bucketName
        );

        $query = "UPDATE siswa SET
                    nis='$nis',
                    nama='$nama',
                    kelas='$kelas',
                    alamat='$alamat',
                    foto='$fotoBaru'
                  WHERE id='$id'";

    } else {

        $query = "UPDATE siswa SET
                    nis='$nis',
                    nama='$nama',
                    kelas='$kelas',
                    alamat='$alamat'
                  WHERE id='$id'";
    }

    mysqli_query($conn, $query);

    header("Location:index.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>

    <title>CRUD Data Siswa AWS S3</title>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>

        body{
            font-family:Arial;
            background:#f4f6f9;
            padding:20px;
        }

        .container{
            max-width:1100px;
            margin:auto;
            background:white;
            padding:20px;
            border-radius:10px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
        }

        h2{
            margin-bottom:20px;
        }

        .card{
            background:#fafafa;
            padding:20px;
            border-radius:10px;
            margin-bottom:20px;
        }

        input, textarea{
            width:100%;
            padding:10px;
            margin-top:5px;
            margin-bottom:15px;
            border:1px solid #ccc;
            border-radius:5px;
        }

        button{
            background:#007bff;
            color:white;
            border:none;
            padding:10px 20px;
            border-radius:5px;
            cursor:pointer;
        }

        table{
            width:100%;
            border-collapse:collapse;
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

        img{
            border-radius:8px;
            object-fit:cover;
        }

        .aksi a{
            text-decoration:none;
            padding:6px 10px;
            color:white;
            border-radius:5px;
        }

        .edit{
            background:orange;
        }

        .hapus{
            background:red;
        }

    </style>

</head>

<body>

<div class="container">

<?php

/*
=================================================
FORM EDIT
=================================================
*/

if (isset($_GET['edit'])) {

    $id = intval($_GET['edit']);

    $edit = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT * FROM siswa WHERE id='$id'")
    );

?>

<div class="card">

<h2>Edit Data Siswa</h2>

<form method="POST" enctype="multipart/form-data">

    <input type="hidden" name="id" value="<?= $edit['id'] ?>">
    <input type="hidden" name="foto_lama" value="<?= $edit['foto'] ?>">

    <label>NIS</label>
    <input type="text" name="nis"
           value="<?= $edit['nis'] ?>" required>

    <label>Nama</label>
    <input type="text" name="nama"
           value="<?= $edit['nama'] ?>" required>

    <label>Kelas</label>
    <input type="text" name="kelas"
           value="<?= $edit['kelas'] ?>" required>

    <label>Alamat</label>

    <textarea name="alamat" required><?= $edit['alamat'] ?></textarea>

    <label>Foto Lama</label><br><br>

    <?php if($edit['foto'] != ''){ ?>

        <img src="<?= $edit['foto'] ?>"
             width="120"
             height="120">

    <?php } ?>

    <br><br>

    <label>Ganti Foto</label>

    <input type="file" name="foto">

    <button type="submit" name="update">
        Update Data
    </button>

</form>

</div>

<?php } else { ?>

<!-- FORM TAMBAH -->

<div class="card">

<h2>Tambah Data Siswa</h2>

<form method="POST" enctype="multipart/form-data">

    <label>NIS</label>
    <input type="text" name="nis" required>

    <label>Nama</label>
    <input type="text" name="nama" required>

    <label>Kelas</label>
    <input type="text" name="kelas" required>

    <label>Alamat</label>
    <textarea name="alamat" required></textarea>

    <label>Foto</label>
    <input type="file" name="foto" required>

    <button type="submit" name="tambah">
        Simpan Data
    </button>

</form>

</div>

<?php } ?>

<!-- TABEL DATA -->

<h2>Data Siswa</h2>

<table>

<tr>
    <th>No</th>
    <th>Foto</th>
    <th>NIS</th>
    <th>Nama</th>
    <th>Kelas</th>
    <th>Alamat</th>
    <th>Aksi</th>
</tr>

<?php

$no = 1;

$data = mysqli_query($conn,
        "SELECT * FROM siswa ORDER BY id DESC");

while($row = mysqli_fetch_assoc($data)){

?>

<tr>

    <td><?= $no++ ?></td>

    <td>

        <?php if($row['foto'] != ''){ ?>

            <img
                src="<?= $row['foto'] ?>"
                width="80"
                height="80"
            >

        <?php } ?>

    </td>

    <td><?= $row['nis'] ?></td>
    <td><?= $row['nama'] ?></td>
    <td><?= $row['kelas'] ?></td>
    <td><?= $row['alamat'] ?></td>

    <td class="aksi">

        <a class="edit"
           href="?edit=<?= $row['id'] ?>">
           Edit
        </a>

        <a class="hapus"
           href="?hapus=<?= $row['id'] ?>"
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
