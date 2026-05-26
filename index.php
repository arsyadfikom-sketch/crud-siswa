<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ==========================
// KONEKSI DATABASE RDS
// ==========================

$host = "localhost";
$user = "root";
$pass = "";
$db   = "sekolah";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {

    die("Koneksi gagal : " . mysqli_connect_error());

}

// ==========================
// BUAT FOLDER UPLOAD
// ==========================

if (!file_exists("upload")) {

    mkdir("upload", 0777, true);

}

// ==========================
// SIMPAN DATA
// ==========================

if (isset($_POST['simpan'])) {

    $nis      = $_POST['nis'];
    $nama     = $_POST['nama'];
    $jk       = $_POST['jk'];
    $alamat   = $_POST['alamat'];
    $jurusan  = $_POST['jurusan'];

    $foto = "";

    // ==========================
    // UPLOAD FOTO
    // ==========================

    if ($_FILES['foto']['name'] != "") {

        $foto = time() . "_" . $_FILES['foto']['name'];

        move_uploaded_file(

            $_FILES['foto']['tmp_name'],
            "upload/" . $foto

        );
    }

    // ==========================
    // INSERT DATABASE
    // ==========================

    $query = "INSERT INTO siswa
    (
        nis,
        nama,
        jk,
        alamat,
        jurusan,
        foto
    )

    VALUES
    (
        '$nis',
        '$nama',
        '$jk',
        '$alamat',
        '$jurusan',
        '$foto'
    )";

    mysqli_query($conn, $query);

    header("Location:index.php");
}

// ==========================
// HAPUS DATA
// ==========================

if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    $ambil = mysqli_query($conn, "SELECT * FROM siswa WHERE id='$id'");

    $data = mysqli_fetch_assoc($ambil);

    if ($data['foto'] != "") {

        if (file_exists("upload/" . $data['foto'])) {

            unlink("upload/" . $data['foto']);

        }
    }

    mysqli_query($conn, "DELETE FROM siswa WHERE id='$id'");

    header("Location:index.php");
}

?>

<!DOCTYPE html>
<html>
<head>

    <title>CRUD Data Siswa AWS</title>

    <style>

        body{
            font-family:Arial;
            background:#f4f4f4;
            padding:20px;
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

        input, textarea, select{

            width:100%;
            padding:10px;
            margin-top:5px;
            margin-bottom:15px;
            border:1px solid #ccc;
            border-radius:5px;

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

        .btn{

            padding:10px 15px;
            border:none;
            border-radius:5px;
            color:white;
            text-decoration:none;
            cursor:pointer;

        }

        .simpan{
            background:green;
        }

        .hapus{
            background:red;
        }

        img{
            border-radius:5px;
        }

    </style>

</head>
<body>

<div class="container">

    <h2>CRUD Data Siswa</h2>

    <form method="POST" enctype="multipart/form-data">

        <label>NIS</label>
        <input type="text" name="nis" required>

        <label>Nama</label>
        <input type="text" name="nama" required>

        <label>Jenis Kelamin</label>

        <select name="jk">

            <option value="Laki-laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>

        </select>

        <label>Alamat</label>
        <textarea name="alamat"></textarea>

        <label>Jurusan</label>
        <input type="text" name="jurusan">

        <label>Foto</label>
        <input type="file" name="foto">

        <button type="submit" name="simpan" class="btn simpan">
            Simpan Data
        </button>

    </form>

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

        while($data = mysqli_fetch_assoc($query)){

        ?>

        <tr>

            <td><?php echo $no++; ?></td>

            <td>

                <?php if($data['foto'] != ""){ ?>

                    <img
                        src="upload/<?php echo $data['foto']; ?>"
                        width="80"
                    >

                <?php } ?>

            </td>

            <td><?php echo $data['nis']; ?></td>
            <td><?php echo $data['nama']; ?></td>
            <td><?php echo $data['jk']; ?></td>
            <td><?php echo $data['alamat']; ?></td>
            <td><?php echo $data['jurusan']; ?></td>

            <td>

                <a
                    class="btn hapus"
                    href="?hapus=<?php echo $data['id']; ?>"
                    onclick="return confirm('Yakin hapus data?')"
                >
                    Hapus
                </a>

            </td>

        </tr>

        <?php } ?>

    </table>

</div>

</body>
</html>
