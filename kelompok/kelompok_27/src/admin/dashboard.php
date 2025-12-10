<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

$tgl_hari_ini = date('Y-m-d');

$q_omzet = mysqli_query($conn, "SELECT SUM(total_bayar) as total FROM transaksi WHERE DATE(tanggal_transaksi) = '$tgl_hari_ini'");
$d_omzet = mysqli_fetch_assoc($q_omzet);
$omzet_hari_ini = $d_omzet['total'] ?: 0;

$q_sup = mysqli_query($conn, "SELECT COUNT(*) as total FROM suppliers WHERE is_active = '1'");
$d_sup = mysqli_fetch_assoc($q_sup);
$total_supplier = $d_sup['total'];

$q_barang = mysqli_query($conn, "SELECT COUNT(*) as total FROM barang WHERE is_active = '1'");
$d_barang = mysqli_fetch_assoc($q_barang);
$total_barang = $d_barang['total'];
?>

<?php include '../layout/header.php'; ?>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
    
    .wrapper-flex { display: flex; min-height: 100vh; width: 100%; }
    .content-wrapper { background: #f4f6f9; flex: 1; padding: 30px; min-height: 100vh; }
 
    .widget-card { border: none; border-radius: 15px; padding: 25px; color: white; position: relative; overflow: hidden; box-shadow: 0 10px 20px rgba(0,0,0,0.1); transition: transform 0.2s; height: 100%; }
    .widget-card:hover { transform: translateY(-5px); }
    .widget-card h3 { font-weight: 800; font-size: 2rem; margin-bottom: 5px; }
    .widget-card p { font-size: 0.9rem; opacity: 0.9; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0; }
    .widget-icon { position: absolute; right: 20px; top: 50%; transform: translateY(-50%); font-size: 3.5rem; opacity: 0.2; }

    .card { border: none; border-radius: 15px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08); background: white; overflow: hidden; margin-bottom: 20px; }
    .card-header { border: none; padding: 20px 25px; background: white; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; }
    .card-title { font-weight: 700; color: #1B3C53; font-size: 1.1rem; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }

    .table thead th { background: linear-gradient(90deg, #1B3C53, #2F5C83); color: white; border: none; font-weight: 500; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 15px; }
    .table tbody td { vertical-align: middle; padding: 15px; color: #444; border-bottom: 1px solid #f2f2f2; font-size: 0.9rem; font-weight: 500; }

    .list-group-item { border: none; border-bottom: 1px solid #f0f0f0; padding: 15px 20px; }
    .list-group-item:last-child { border-bottom: none; }
    
    .btn-rounded { border-radius: 50px; padding: 5px 15px; font-weight: 600; font-size: 0.8rem; }
</style>

<div class="wrapper-flex">
    
    <?php include '../layout/sidebar.php'; ?>
    
    <div class="content-wrapper">
        
        <section class="content-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 style="color: #1B3C53; font-weight: 800; font-size: 2rem; letter-spacing: -1px;">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                    </h1>
                    <p class="text-muted m-0">Halo, <b><?= ucfirst($_SESSION['username']); ?></b>! Berikut ringkasan toko hari ini.</p>
                </div>
                <div class="bg-white px-3 py-2 rounded shadow-sm text-muted small fw-bold border d-none d-md-block">
                    <i class="far fa-calendar-alt me-2"></i> <?= date('d F Y'); ?>
                </div>
            </div>
        </section>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="widget-card" style="background: linear-gradient(135deg, #1B3C53, #2F5C83);">
                    <p>Pendapatan Hari Ini</p>
                    <h3>Rp <?= number_format($omzet_hari_ini, 0, ',', '.'); ?></h3>
                    <div class="widget-icon"><i class="fas fa-wallet"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="widget-card" style="background: linear-gradient(135deg, #e67e22, #f39c12);">
                    <p>Total Supplier</p>
                    <h3><?= $total_supplier; ?> <span style="font-size: 1rem; font-weight: 400;">Mitra</span></h3>
                    <div class="widget-icon"><i class="fas fa-truck"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="widget-card" style="background: linear-gradient(135deg, #8e44ad, #9b59b6);">
                    <p>Total Produk</p>
                    <h3><?= $total_barang; ?> <span style="font-size: 1rem; font-weight: 400;">SKU</span></h3>
                    <div class="widget-icon"><i class="fas fa-box-open"></i></div>
                </div>
            </div>
        </div>

        <div class="row">
            
            <div class="col-md-8 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-exclamation-triangle me-2 text-danger"></i> Stok Menipis (< 5)</h3>
                        <a href="master_barang.php" class="btn btn-outline-primary btn-rounded">Lihat Semua</a>
                    </div>
                    <div class="card-body p-0">
                         <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Nama Barang</th>
                                        <th>Supplier</th>
                                        <th class="text-center">Sisa Stok</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $q_stok = mysqli_query($conn, "SELECT * FROM barang LEFT JOIN suppliers ON barang.id_supplier = suppliers.id_supplier WHERE stok <= 5 AND barang.is_active='1' ORDER BY stok ASC LIMIT 5");
                                    if(mysqli_num_rows($q_stok) > 0) {
                                        while($row = mysqli_fetch_assoc($q_stok)): 
                                    ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark"><?= $row['nama_barang']; ?></td>
                                        <td>
                                            <?php if($row['nama_supplier']): ?>
                                                <span class="badge bg-warning text-dark bg-opacity-25" style="font-size:0.75rem"><?= $row['nama_supplier']; ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary" style="font-size:0.75rem">Koperasi</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger rounded-pill px-3"><?= $row['stok']; ?></span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="master_barang.php" class="btn btn-primary btn-sm btn-rounded text-white"><i class="fas fa-plus-circle me-1"></i> Restock</a>
                                        </td>
                                    </tr>
                                    <?php endwhile; } else { ?>
                                    <tr><td colspan="4" class="text-center py-4 text-muted">Stok aman terkendali.</td></tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-crown me-2 text-warning"></i>Paling Banyak Terjual</h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php
                            $q_top = mysqli_query($conn, "SELECT b.nama_barang, SUM(dt.qty) as terjual FROM detail_transaksi dt JOIN barang b ON dt.id_barang = b.id_barang GROUP BY dt.id_barang ORDER BY terjual DESC LIMIT 5");
                            if(mysqli_num_rows($q_top) > 0) {
                                $rank = 1;
                                while($top = mysqli_fetch_assoc($q_top)):
                            ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-primary rounded-circle me-3 shadow-sm" style="width: 25px; height: 25px; display:flex; align-items:center; justify-content:center;"><?= $rank++; ?></div>
                                    <span class="fw-bold text-dark small"><?= $top['nama_barang']; ?></span>
                                </div>
                                <span class="badge bg-info text-dark bg-opacity-10 rounded-pill"><?= $top['terjual']; ?> terjual</span>
                            </li>
                            <?php endwhile; } else { ?>
                                <li class="list-group-item text-center text-muted py-4">Belum ada data penjualan.</li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                     <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-history me-2"></i> Transaksi Terakhir Masuk</h3>
                        <a href="laporan_keuangan.php" class="btn btn-outline-primary btn-rounded">Laporan Lengkap</a>
                    </div>
                    <div class="card-body p-0">
                         <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">No Faktur</th>
                                        <th>Waktu</th>
                                        <th>Kasir</th>
                                        <th>Metode</th>
                                        <th class="text-end pe-4">Total Bayar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $q_recent = mysqli_query($conn, "SELECT t.*, u.username FROM transaksi t JOIN users u ON t.id_user = u.id_user ORDER BY t.tanggal_transaksi DESC LIMIT 5");
                                    if(mysqli_num_rows($q_recent) > 0) {
                                        while($trx = mysqli_fetch_assoc($q_recent)):
                                    ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary small"><?= $trx['no_faktur']; ?></td>
                                        <td class="small text-muted"><?= date('d/m H:i', strtotime($trx['tanggal_transaksi'])); ?></td>
                                        <td><?= ucfirst($trx['username']); ?></td>
                                        <td>
                                            <?php if($trx['metode_pembayaran'] == 'cash'): ?>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success px-2">CASH</span>
                                            <?php else: ?>
                                                <span class="badge bg-info bg-opacity-10 text-primary border border-primary px-2">QRIS</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end fw-bold pe-4">Rp <?= number_format($trx['total_bayar']); ?></td>
                                    </tr>
                                    <?php endwhile; } else { ?>
                                    <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada transaksi.</td></tr>
                                    <?php } ?>
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