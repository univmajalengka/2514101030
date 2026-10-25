<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'rani_minang';

mysqli_report(MYSQLI_REPORT_OFF);
$koneksi = new mysqli($host, $user, $pass, $db);

if ($koneksi->connect_errno) {
    die('Koneksi database gagal');
}
$koneksi->set_charset('utf8mb4');