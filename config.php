<?php

require 'vendor/autoload.php';

use Aws\S3\S3Client;

// =========================
// DATABASE RDS
// =========================

$host = "ENDPOINT-RDS";
$user = "admin";
$pass = "PASSWORD-RDS";
$db   = "sekolah";

$conn = mysqli_connect($host,$user,$pass,$db);

if(!$conn){

    die("Database gagal konek");

}

// =========================
// AWS S3
// =========================

$s3 = new S3Client([

    'version' => 'latest',
    'region'  => 'us-east-1'

]);

$bucket = "siswa-upload-bucket";

?>
