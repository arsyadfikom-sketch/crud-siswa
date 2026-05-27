<?php

require 'vendor/autoload.php';

use Aws\S3\S3Client;

// =========================
// DATABASE RDS
// =========================

$host = "siswa-db.c83ya4kmsi7u.us-east-1.rds.amazonaws.com";
$user = "admin";
$pass = "admin2026";
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
