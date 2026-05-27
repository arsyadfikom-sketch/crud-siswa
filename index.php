<?php

include 'config.php';
include 'upload_s3.php';

if(isset($_POST['simpan'])){

    $nis      = $_POST['nis'];
    $nama     = $_POST['nama'];
    $jk       = $_POST['jk'];
    $alamat   = $_POST['alamat'];
    $jurusan  = $_POST['jurusan'];

    $foto = uploadFoto($s3,$bucket,$_FILES['foto']);

    mysqli_query($conn,"INSERT INTO siswa
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
    )");

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

}

input, textarea, select{

    width:100%;
    padding:10px;
    margin-top:5px;
    margin-bottom:10px;

}

table{

    width:100%;
    border-collapse:collapse;
    margin-top:20px;

}

table th, table td{

    border:1px solid #ddd;
    padding:10px;

}

table th{

    background:#007bff;
    color:white;

}

.btn{

    padding:10px;
    border:none;
    border-radius:5px;
    text-decoration:none;
    color:white;
    cursor:pointer;

}

.simpan{

    background:green;

}

.hapus{

    background:red;

}

</style>

</head>
<body>

<div class="container">

<h2>CRUD Data Siswa AWS</h2>

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
<input type="file" name="foto">

<button class="btn simpan" name="simpan">
Simpan
</button>

</form>

<hr>

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

$query = mysqli_query($conn,"SELECT * FROM siswa ORDER BY id DESC");

while($data = mysqli_fetch_assoc($query)){

?>

<tr>

<td><?php echo $no++; ?></td>

<td>

<img
src="<?php echo $data['foto']; ?>"
width="80"
>

</td>

<td><?php echo $data['nis']; ?></td>
<td><?php echo $data['nama']; ?></td>
<td><?php echo $data['jk']; ?></td>
<td><?php echo $data['alamat']; ?></td>
<td><?php echo $data['jurusan']; ?></td>

<td>

<a
href="delete.php?id=<?php echo $data['id']; ?>"
class="btn hapus"
onclick="return confirm('Hapus data?')"
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
