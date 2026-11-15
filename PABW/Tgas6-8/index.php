<?php
session_start();
require_once 'koneksi.php';

function hitungDiskon($totalBelanja) {
    $diskon = 0;

    if ($totalBelanja >= 100000) {
        $diskon = 0.1 * $totalBelanja;
    } elseif ($totalBelanja >= 50000) {
        $diskon = 0.05 * $totalBelanja;
    }

    return $diskon;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaPelanggan = htmlspecialchars(trim($_POST['nama_pelanggan']));
    $totalBelanja  = (int) $_POST['total_belanja'];

    $diskon     = hitungDiskon($totalBelanja);
    $totalBayar = $totalBelanja - $diskon;

    $stmt = $conn->prepare("INSERT INTO transaksi (nama_pelanggan, total_belanja, diskon, total_bayar) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siii", $namaPelanggan, $totalBelanja, $diskon, $totalBayar);
    $stmt->execute();
    $stmt->close();

    $_SESSION['hasil'] = [
        'nama'    => $namaPelanggan,
        'belanja' => $totalBelanja,
        'diskon'  => $diskon,
        'bayar'   => $totalBayar
    ];

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$hasil = $_SESSION['hasil'] ?? null;
unset($_SESSION['hasil']);

// mengambil data untuk riwayat
$riwayat = $conn->query("SELECT * FROM transaksi ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hitung Diskon</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <div class="card">
        <h2>ALAT PENGHITUNG DISKON</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="nama_pelanggan">Nama Pelanggan</label>
                <input type="text" id="nama_pelanggan" name="nama_pelanggan" placeholder="Masukkan nama..." required>
            </div>

            <div class="form-group">
                <label for="total_belanja">Total Belanja (Rp)</label>
                <input type="number" id="total_belanja" name="total_belanja" min="0" placeholder="Contoh: 120000" required>
            </div>

            <button type="submit" class="btn">Hitung & Simpan</button>
        </form>

        <?php if ($hasil): ?>
            <div class="result-box">
                <h3>Rincian Pembayaran (<?= $hasil['nama'] ?>)</h3>
                <div class="result-row">
                    <span>Total Belanja:</span>
                    <strong>Rp <?= number_format($hasil['belanja'], 0, ',', '.') ?></strong>
                </div>
                <div class="result-row discount">
                    <span>Diskon:</span>
                    <strong>- Rp <?= number_format($hasil['diskon'], 0, ',', '.') ?></strong>
                </div>
                <hr>
                <div class="result-row total">
                    <span>Total yang Harus Dibayar:</span>
                    <strong>Rp <?= number_format($hasil['bayar'], 0, ',', '.') ?></strong>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>Riwayat Transaksi</h3>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pelanggan</th>
                        <th>Total Belanja</th>
                        <th>Diskon</th>
                        <th>Total Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($riwayat && $riwayat->num_rows > 0): ?>
                        <?php $no = 1; while ($row = $riwayat->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                                <td>Rp <?= number_format($row['total_belanja'], 0, ',', '.') ?></td>
                                <td>Rp <?= number_format($row['diskon'], 0, ',', '.') ?></td>
                                <td><strong>Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></strong></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="empty">Belum ada data transaksi.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
<?php $conn->close(); ?>