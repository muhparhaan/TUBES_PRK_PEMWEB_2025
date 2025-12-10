<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'staff') {
    header("Location: ../index.php");
    exit();
}

include '../config/koneksi.php';
include '../layout/header.php';
include '../layout/sidebar.php';

$id_user = $_SESSION['id_user'];
$tanggal_hari_ini = date('Y-m-d');

$query = "SELECT 
            COUNT(*) as total_struk,
            SUM(total_bayar) as total_omzet,
            SUM(CASE WHEN metode_pembayaran = 'cash' THEN total_bayar ELSE 0 END) as total_cash,
            SUM(CASE WHEN metode_pembayaran = 'qris' THEN total_bayar ELSE 0 END) as total_qris
          FROM transaksi 
          WHERE DATE(tanggal_transaksi) = '$tanggal_hari_ini' 
          AND id_user = '$id_user'";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

$total_struk = $data['total_struk'] ?? 0;
$total_omzet = $data['total_omzet'] ?? 0;
$total_cash = $data['total_cash'] ?? 0;
$total_qris = $data['total_qris'] ?? 0;
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Dashboard Staff</h2>
        <a href="transaksi.php" class="btn btn-primary btn-lg">
            <i class="fas fa-shopping-cart"></i> Transaksi Baru (POS)
        </a>
    </div>

    <div class="alert alert-info">
        <strong>Status Closing:</strong> Gunakan data di bawah ini untuk mencocokkan uang fisik di laci sebelum pulang.
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Total Omzet Hari Ini</div>
                <div class="card-body">
                    <h4 class="card-title">Rp <?= number_format($total_omzet, 0, ',', '.') ?></h4>
                    <p class="card-text">Total semua pemasukan.</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Uang Tunai (Cash)</div>
                <div class="card-body">
                    <h4 class="card-title">Rp <?= number_format($total_cash, 0, ',', '.') ?></h4>
                    <p class="card-text">Wajib ada di laci kasir.</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">Pembayaran QRIS</div>
                <div class="card-body">
                    <h4 class="card-title">Rp <?= number_format($total_qris, 0, ',', '.') ?></h4>
                    <p class="card-text">Cek mutasi rekening/e-wallet.</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-light mb-3">
                <div class="card-header">Jumlah Struk</div>
                <div class="card-body">
                    <h4 class="card-title"><?= $total_struk ?> Transaksi</h4>
                    <p class="card-text">Total pelayanan hari ini.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>