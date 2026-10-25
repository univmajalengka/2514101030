<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html#pemesanan');
    exit;
}

function e($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$nama  = trim($_POST['nama'] ?? '');
$nomor = trim($_POST['nomor'] ?? '');

$error = [];
if ($nama === '' || mb_strlen($nama) > 60) {
    $error[] = 'Nama pemesan wajib diisi (maksimal 60 karakter).';
}
if (!preg_match('/^[0-9+\-\s]{9,16}$/', $nomor)) {
    $error[] = 'Nomor pemesan tidak valid (9-16 digit).';
}

$sukses = false;
if (empty($error)) {
    // Prepared statement agar aman dari SQL injection
    $stmt = $koneksi->prepare('INSERT INTO pesanan (nama, nomor) VALUES (?, ?)');
    $stmt->bind_param('ss', $nama, $nomor);
    if ($stmt->execute()) {
        $sukses = true;
    } else {
        $error[] = 'Pesanan gagal disimpan. Coba lagi beberapa saat.';
    }
    $stmt->close();
}
$koneksi->close();

if ($sukses) {
    header('Location: index.html?status=selesai#pemesanan');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan - Rani Minang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="banner">
        <h1>Rani Minang</h1>
        <p>Masakan Padang</p>
    </header>

    <nav class="navbar">
        <a href="index.html#beranda">Beranda</a>
        <a href="index.html#about">About</a>
        <a href="index.html#menu">Menu</a>
        <a href="index.html#galeri">Galeri</a>
        <a href="index.html#pemesanan">Pemesanan</a>
    </nav>

    <main class="hasil-pesan">
            <h2>Pesanan Belum Dapat Diproses</h2>
            <ul>
                <?php foreach ($error as $pesan): ?>
                    <li><?= e($pesan) ?></li>
                <?php endforeach; ?>
            </ul>
            <a class="tombol" href="javascript:history.back()">Kembali ke Form</a>
    </main>
</body>
</html>