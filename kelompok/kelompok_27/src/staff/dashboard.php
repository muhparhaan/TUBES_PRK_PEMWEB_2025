<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'staff') {
    header("Location: ../index.php");
    exit();
}

include '../config/koneksi.php';
include '../layout/header.php';

$id_user = $_SESSION['id_user'];
$tanggal_hari_ini = date('Y-m-d');

$query_stats = "SELECT 
            COUNT(*) as total_struk,
            SUM(total_bayar) as total_omzet,
            SUM(CASE WHEN metode_pembayaran = 'cash' THEN total_bayar ELSE 0 END) as total_cash,
            SUM(CASE WHEN metode_pembayaran = 'qris' THEN total_bayar ELSE 0 END) as total_qris
          FROM transaksi 
          WHERE DATE(tanggal_transaksi) = '$tanggal_hari_ini' 
          AND id_user = '$id_user'";

$result_stats = mysqli_query($conn, $query_stats);
$data = mysqli_fetch_assoc($result_stats);

$total_struk = $data['total_struk'] ?: 0;
$total_omzet = $data['total_omzet'] ?: 0;
$total_cash  = $data['total_cash'] ?: 0;
$total_qris  = $data['total_qris'] ?: 0;


$list_jam = [];
$list_trx = [];
for($i=8; $i<=21; $i++) {
    $jam_str = str_pad($i, 2, '0', STR_PAD_LEFT);
    $list_jam[] = "$jam_str:00";
    
    $q_jam = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi 
                                  WHERE DATE(tanggal_transaksi) = '$tanggal_hari_ini' 
                                  AND id_user = '$id_user' 
                                  AND HOUR(tanggal_transaksi) = '$i'");
    $d_jam = mysqli_fetch_assoc($q_jam);
    $list_trx[] = $d_jam['total'] ?: 0;
}

$query_sold_items = "SELECT 
                        b.nama_barang, 
                        b.harga_jual,
                        SUM(dt.qty) as qty_terjual, 
                        SUM(dt.subtotal) as total_uang
                     FROM detail_transaksi dt
                     JOIN transaksi t ON dt.id_transaksi = t.id_transaksi
                     JOIN barang b ON dt.id_barang = b.id_barang
                     WHERE DATE(t.tanggal_transaksi) = '$tanggal_hari_ini' 
                     AND t.id_user = '$id_user'
                     GROUP BY dt.id_barang
                     ORDER BY qty_terjual DESC"; 
$result_sold_items = mysqli_query($conn, $query_sold_items);
?>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
    .wrapper-flex { display: flex; min-height: 100vh; width: 100%; }
    .content-wrapper { background: #f4f6f9; flex: 1; padding: 30px; min-height: 100vh; }
    
    /* Widget Cards */
    .widget-card { border: none; border-radius: 15px; padding: 25px; color: white; position: relative; overflow: hidden; box-shadow: 0 10px 20px rgba(0,0,0,0.1); transition: transform 0.2s; height: 100%; }
    .widget-card:hover { transform: translateY(-5px); }
    .widget-card h3 { font-weight: 800; font-size: 1.8rem; margin-bottom: 5px; }
    .widget-card p { font-size: 0.9rem; opacity: 0.9; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0; }
    .widget-icon { position: absolute; right: 20px; top: 50%; transform: translateY(-50%); font-size: 3.5rem; opacity: 0.2; }
    
    /* Tombol Aksi */
    .btn-action { background: linear-gradient(45deg, #1B3C53, #4a90e2); color: white; border: none; padding: 12px 25px; border-radius: 50px; font-weight: 600; box-shadow: 0 5px 15px rgba(27, 60, 83, 0.3); font-size: 1rem; transition: all 0.3s; text-decoration: none; display: inline-block;}
    .btn-action:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(27, 60, 83, 0.4); color: white; }
    
    /* Card Styles */
    .card-modern { background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); overflow: hidden; height: 100%; border: none; }
    .card-modern-header { background: #fff; padding: 20px 25px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; }
    .card-modern-title { font-weight: 700; color: #1B3C53; margin: 0; font-size: 1rem; text-transform: uppercase; letter-spacing: 0.5px; }
    
    /* Tables */
    .table-custom { margin-bottom: 0; }
    .table-custom thead th { background-color: #f8f9fa; color: #6c757d; font-size: 0.8rem; text-transform: uppercase; border-top: none; }
    .table-custom tbody td { vertical-align: middle; font-size: 0.9rem; }
</style>

<div class="wrapper-flex">
    
    <?php include '../layout/sidebar.php'; ?>

    <div class="content-wrapper">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 style="color: #1B3C53; font-weight: 800; font-size: 2rem; letter-spacing: -1px;">
                    <i class="fas fa-home mr-2"></i> Dashboard Staff
                </h1>
                <p class="text-muted m-0">Halo, <b><?= ucfirst($_SESSION['username']); ?></b>! Berikut ringkasan shift Anda hari ini.</p>
            </div>
            <div>
                <a href="transaksi.php" class="btn-action">
                    <i class="fas fa-shopping-cart me-2"></i> Transaksi Baru
                </a>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="widget-card" style="background: linear-gradient(135deg, #1B3C53, #2F5C83);">
                    <p>Total Omzet</p>
                    <h3>Rp <?= number_format($total_omzet, 0, ',', '.'); ?></h3>
                    <div class="widget-icon"><i class="fas fa-coins"></i></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget-card" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                    <p>Uang Tunai (Laci)</p>
                    <h3>Rp <?= number_format($total_cash, 0, ',', '.'); ?></h3>
                    <div class="widget-icon"><i class="fas fa-money-bill-wave"></i></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget-card" style="background: linear-gradient(135deg, #8e44ad, #9b59b6);">
                    <p>Pembayaran QRIS</p>
                    <h3>Rp <?= number_format($total_qris, 0, ',', '.'); ?></h3>
                    <div class="widget-icon"><i class="fas fa-qrcode"></i></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget-card" style="background: linear-gradient(135deg, #e67e22, #f39c12);">
                    <p>Total Pelanggan</p>
                    <h3 class="mb-0"><?= $total_struk; ?> <span style="font-size: 1rem; font-weight:400;">Orang</span></h3>
                    <div class="widget-icon"><i class="fas fa-users"></i></div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card-modern">
                    <div class="card-modern-header">
                        <h5 class="card-modern-title"><i class="fas fa-chart-bar me-2"></i> Grafik Kepadatan Transaksi (Per Jam)</h5>
                    </div>
                    <div class="card-body p-3">
                        <canvas id="jamSibukChart" style="height: 250px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12">
                <div class="card-modern">
                    <div class="card-modern-header">
                        <h5 class="card-modern-title"><i class="fas fa-list-alt me-2"></i> Rincian Barang Terjual Hari Ini</h5>
                        <span class="badge bg-primary rounded-pill">Realtime Data</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-custom table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3" width="5%">No</th>
                                        <th width="40%">Nama Barang</th>
                                        <th width="20%" class="text-end">Harga Satuan</th>
                                        <th width="15%" class="text-center">Qty Terjual</th>
                                        <th width="20%" class="text-end pe-4">Total Pendapatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(mysqli_num_rows($result_sold_items) > 0): ?>
                                        <?php $no = 1; while($item = mysqli_fetch_assoc($result_sold_items)): ?>
                                        <tr>
                                            <td class="ps-4 text-muted"><?= $no++; ?></td>
                                            <td class="fw-bold text-dark"><?= $item['nama_barang']; ?></td>
                                            <td class="text-end text-muted">
                                                Rp <?= number_format($item['harga_jual'], 0, ',', '.'); ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info text-dark bg-opacity-10 border border-info px-4 py-2" style="font-size: 0.9rem;">
                                                    <?= $item['qty_terjual']; ?>
                                                </span>
                                            </td>
                                            <td class="text-end pe-4 fw-bold text-success" style="font-size: 1rem;">
                                                Rp <?= number_format($item['total_uang'], 0, ',', '.'); ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="fas fa-box-open fs-1 mb-3 text-light"></i><br>
                                                Belum ada barang yang terjual hari ini.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include '../layout/footer.php'; ?>

<script>
    const ctx = document.getElementById('jamSibukChart').getContext('2d');
    const jamSibukChart = new Chart(ctx, {
        type: 'bar', 
        data: {
            labels: <?php echo json_encode($list_jam); ?>, 
            datasets: [{
                label: 'Jumlah Transaksi',
                data: <?php echo json_encode($list_trx); ?>,
                backgroundColor: 'rgba(27, 60, 83, 0.7)',
                borderColor: 'rgba(27, 60, 83, 1)',
                borderWidth: 1,
                borderRadius: 4,
                barPercentage: 0.6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 } 
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>