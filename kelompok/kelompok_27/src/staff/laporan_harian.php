<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'staff') {
    header("Location: ../index.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$tgl_hari_ini = date('Y-m-d'); 

include '../layout/header.php';

$q_cash = mysqli_query($conn, "SELECT SUM(total_bayar) as total FROM transaksi 
                               WHERE id_user='$id_user' 
                               AND DATE(tanggal_transaksi)='$tgl_hari_ini' 
                               AND metode_pembayaran='cash'");
$d_cash = mysqli_fetch_assoc($q_cash);
$total_cash = $d_cash['total'] ?: 0;

$q_qris = mysqli_query($conn, "SELECT SUM(total_bayar) as total FROM transaksi 
                               WHERE id_user='$id_user' 
                               AND DATE(tanggal_transaksi)='$tgl_hari_ini' 
                               AND metode_pembayaran='qris'");
$d_qris = mysqli_fetch_assoc($q_qris);
$total_qris = $d_qris['total'] ?: 0;

$query_detail = "SELECT 
                    t.no_faktur, 
                    t.tanggal_transaksi, 
                    t.metode_pembayaran,
                    b.nama_barang, 
                    dt.qty, 
                    dt.subtotal,
                    s.nama_supplier, 
                    s.kategori
                 FROM detail_transaksi dt
                 JOIN transaksi t ON dt.id_transaksi = t.id_transaksi
                 JOIN barang b ON dt.id_barang = b.id_barang
                 LEFT JOIN suppliers s ON b.id_supplier = s.id_supplier
                 WHERE DATE(t.tanggal_transaksi) = '$tgl_hari_ini' 
                 AND t.id_user = '$id_user'
                 ORDER BY t.tanggal_transaksi DESC, t.id_transaksi DESC";

$result_detail = mysqli_query($conn, $query_detail);

$list_transaksi = [];
$rekap_supplier = [];
$grand_total_rincian = 0;

if ($result_detail) {
    while ($row = mysqli_fetch_assoc($result_detail)) {
        $list_transaksi[] = $row;
        $grand_total_rincian += $row['subtotal'];

        if ($row['nama_supplier'] != NULL) {
            $nama_sup = $row['nama_supplier'];
            $kategori = $row['kategori'];
            $omzet    = $row['subtotal'];

            $persen_fee = ($kategori == 'internal') ? 0.10 : 0.15;
            $fee_toko   = $omzet * $persen_fee;
            $wajib_setor = $omzet - $fee_toko;

            if (!isset($rekap_supplier[$nama_sup])) {
                $rekap_supplier[$nama_sup] = [
                    'kategori' => $kategori,
                    'qty_terjual' => 0,
                    'total_omzet' => 0,
                    'total_setor' => 0
                ];
            }

            $rekap_supplier[$nama_sup]['qty_terjual'] += $row['qty'];
            $rekap_supplier[$nama_sup]['total_omzet'] += $omzet;
            $rekap_supplier[$nama_sup]['total_setor'] += $wajib_setor;
        }
    }
}
?>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
    .wrapper-flex { display: flex; min-height: 100vh; width: 100%; }
    .content-wrapper { background: #f4f6f9; flex: 1; padding: 30px; min-height: 100vh; }
    
    /* Card Style Admin */
    .card { border: none; border-radius: 15px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08); background: white; overflow: hidden; margin-bottom: 20px; }
    .card-header { border: none; padding: 20px 25px; background: white; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; }
    .card-title { font-weight: 800; color: #1B3C53; font-size: 1.1rem; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }

    /* Table Header Gradient Style Admin */
    .table thead th { 
        background: linear-gradient(90deg, #1B3C53, #2F5C83); 
        color: white; 
        border: none; 
        font-weight: 600; 
        text-transform: uppercase; 
        font-size: 0.85rem; 
        padding: 15px; 
        cursor: pointer; 
        transition: background 0.3s;
    }
    .table thead th:hover { background: linear-gradient(90deg, #2F5C83, #1B3C53); }
    
    .table tbody td { vertical-align: middle; padding: 15px; color: #444; border-bottom: 1px solid #f2f2f2; font-size: 0.9rem; }
    
    /* Table Footer Admin Style */
    .table tfoot th { background-color: #f8f9fa; border-top: 2px solid #1B3C53; color: #1B3C53; font-weight: 800; padding: 15px; font-size: 1rem; }

    /* Widgets */
    .widget-card { border-radius: 15px; padding: 25px; color: white; position: relative; overflow: hidden; box-shadow: 0 10px 20px rgba(0,0,0,0.1); transition: transform 0.2s; height: 100%; }
    .widget-card h3 { font-weight: 800; font-size: 2rem; margin-bottom: 5px; }
    .widget-card p { font-size: 0.9rem; opacity: 0.9; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; }

    @media print {
        .sidebar, .btn, .no-print, .content-header p, .navbar, footer { display: none !important; }
        .wrapper-flex { display: block; }
        .content-wrapper { padding: 0; background: white; margin: 0; width: 100%; }
        .card { box-shadow: none; border: 1px solid #ddd; margin-bottom: 20px; page-break-inside: avoid; }
        .table thead th { background: #333 !important; color: white !important; -webkit-print-color-adjust: exact; }
    }
</style>

<div class="wrapper-flex">
    
    <?php include '../layout/sidebar.php'; ?>

    <div class="content-wrapper">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 style="color: #1B3C53; font-weight: 800; font-size: 2rem; letter-spacing: -1px;">
                    <i class="fas fa-file-invoice-dollar mr-2"></i> Laporan Harian
                </h1>
                <p class="text-muted m-0">Shift: <b><?= ucfirst($_SESSION['username']); ?></b> | Tanggal: <b><?= date('d F Y'); ?></b></p>
            </div>
            <div class="no-print">
                <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 shadow-sm" style="background: linear-gradient(45deg, #1B3C53, #4a90e2); border:none; font-weight:600;">
                    <i class="fas fa-print me-2"></i> Cetak Laporan
                </button>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="widget-card" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                    <p>Uang Tunai (Cash)</p>
                    <h3>Rp <?= number_format($total_cash, 0, ',', '.'); ?></h3>
                    <small style="opacity: 0.8;"><i class="fas fa-wallet me-1"></i> Fisik di Laci Kasir</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="widget-card" style="background: linear-gradient(135deg, #2980b9, #3498db);">
                    <p>Non-Tunai (QRIS)</p>
                    <h3>Rp <?= number_format($total_qris, 0, ',', '.'); ?></h3>
                    <small style="opacity: 0.8;"><i class="fas fa-qrcode me-1"></i> Masuk Rekening</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="widget-card" style="background: linear-gradient(135deg, #2c3e50, #34495e);">
                    <p>Total Omzet Shift Ini</p>
                    <h3>Rp <?= number_format($total_cash + $total_qris, 0, ',', '.'); ?></h3>
                    <small style="opacity: 0.8;"><i class="fas fa-chart-line me-1"></i> Total Penjualan Hari Ini</small>
                </div>
            </div>
        </div>

        <section class="content mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list me-2"></i> Rincian Barang Terjual</h3>
                    <button onclick="exportToExcel('tableRincian', 'Laporan_Rincian_<?= date('Ymd'); ?>')" class="btn btn-sm btn-success text-white no-print" style="border-radius: 50px; font-weight:600;">
                        <i class="fas fa-file-excel me-1"></i> Export Excel
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="tableRincian">
                            <thead>
                                <tr>
                                    <th class="ps-4" onclick="sortTable('tableRincian', 0)">No Faktur <i class="fas fa-sort float-end small mt-1 opacity-50"></i></th>
                                    <th onclick="sortTable('tableRincian', 1)">Jam <i class="fas fa-sort float-end small mt-1 opacity-50"></i></th>
                                    <th onclick="sortTable('tableRincian', 2)">Nama Barang <i class="fas fa-sort float-end small mt-1 opacity-50"></i></th>
                                    <th class="text-center" onclick="sortTable('tableRincian', 3)">Qty <i class="fas fa-sort float-end small mt-1 opacity-50"></i></th>
                                    <th onclick="sortTable('tableRincian', 4)">Metode <i class="fas fa-sort float-end small mt-1 opacity-50"></i></th>
                                    <th class="text-end pe-4" onclick="sortTable('tableRincian', 5)">Subtotal <i class="fas fa-sort float-end small mt-1 opacity-50"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($list_transaksi)): ?>
                                    <?php foreach($list_transaksi as $row): ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-primary small"><?= $row['no_faktur']; ?></td>
                                            <td class="small"><?= date('H:i', strtotime($row['tanggal_transaksi'])); ?></td>
                                            <td>
                                                <?= $row['nama_barang']; ?>
                                                <?php if($row['nama_supplier']): ?>
                                                    <div class="small text-muted fst-italic"><i class="fas fa-box-open me-1" style="font-size:0.7rem;"></i> Titipan: <?= $row['nama_supplier']; ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center fw-bold"><?= $row['qty']; ?></td>
                                            <td>
                                                <?php if($row['metode_pembayaran'] == 'cash'): ?>
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success" style="padding: 5px 10px;">CASH</span>
                                                <?php else: ?>
                                                    <span class="badge bg-info bg-opacity-10 text-primary border border-primary" style="padding: 5px 10px;">QRIS</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end fw-bold pe-4">Rp <?= number_format($row['subtotal']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada transaksi hari ini.</td></tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-end pe-4 text-uppercase">TOTAL PENJUALAN :</th>
                                    <th class="text-end pe-4">Rp <?= number_format($grand_total_rincian); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="content">
            <div class="card border-warning" style="border: 1px solid #ffeeba;">
                <div class="card-header" style="background-color: #fff3cd;">
                    <h3 class="card-title text-dark"><i class="fas fa-hand-holding-usd me-2"></i> Rekap Tagihan Supplier</h3>
                    <button onclick="exportToExcel('tableRekap', 'Laporan_Rekap_Supplier_<?= date('Ymd'); ?>')" class="btn btn-sm btn-dark text-white no-print" style="border-radius: 50px; font-weight:600;">
                        <i class="fas fa-file-excel me-1"></i> Export Excel
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="tableRekap">
                            <thead>
                                <tr>
                                    <th class="ps-4" onclick="sortTable('tableRekap', 0)">Supplier / Penitip <i class="fas fa-sort float-end small mt-1 opacity-50"></i></th>
                                    <th class="text-center" onclick="sortTable('tableRekap', 1)">Kategori <i class="fas fa-sort float-end small mt-1 opacity-50"></i></th>
                                    <th class="text-center" onclick="sortTable('tableRekap', 2)">Total Qty <i class="fas fa-sort float-end small mt-1 opacity-50"></i></th>
                                    <th class="text-end" onclick="sortTable('tableRekap', 3)">Omzet Kotor <i class="fas fa-sort float-end small mt-1 opacity-50"></i></th>
                                    <th class="text-end pe-4" onclick="sortTable('tableRekap', 4)">Wajib Disetor <i class="fas fa-sort float-end small mt-1 opacity-50"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($rekap_supplier)): ?>
                                    <?php $grand_setor = 0; ?>
                                    <?php foreach($rekap_supplier as $nama => $data): ?>
                                        <?php $grand_setor += $data['total_setor']; ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-dark"><?= $nama; ?></td>
                                            <td class="text-center">
                                                <?php if($data['kategori'] == 'internal'): ?>
                                                    <span class="badge bg-secondary">Internal</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark">Eksternal</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center"><?= $data['qty_terjual']; ?></td>
                                            <td class="text-end text-muted">Rp <?= number_format($data['total_omzet']); ?></td>
                                            <td class="text-end fw-bold text-danger pe-4" style="font-size: 1.05rem;">
                                                Rp <?= number_format($data['total_setor']); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center py-5 text-muted">Tidak ada penjualan barang titipan hari ini.</td></tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end pe-4 text-uppercase text-danger">TOTAL WAJIB DISETOR :</th>
                                    <th class="text-end pe-4 text-danger">Rp <?= number_format($grand_setor ?? 0); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <small class="text-muted fst-italic"><i class="fas fa-info-circle me-1"></i> Harap pisahkan uang <b>"Wajib Disetor"</b> di amplop terpisah untuk diserahkan ke Admin/Supplier.</small>
                </div>
            </div>
        </section>

    </div>
</div>

<?php include '../layout/footer.php'; ?>

<script>
    function sortTable(tableId, n) {
        var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
        table = document.getElementById(tableId);
        switching = true;
        dir = "asc"; 

        while (switching) {
            switching = false;
            rows = table.rows;
            for (i = 1; i < (rows.length - 1); i++) {
                shouldSwitch = false;
                if(rows[i].parentNode.nodeName === 'TFOOT' || rows[i+1].parentNode.nodeName === 'TFOOT') continue;

                x = rows[i].getElementsByTagName("TD")[n];
                y = rows[i + 1].getElementsByTagName("TD")[n];
                
                let xContent = x.innerHTML.toLowerCase();
                let yContent = y.innerHTML.toLowerCase();
                let xNum = parseFloat(xContent.replace(/[^0-9.-]+/g,""));
                let yNum = parseFloat(yContent.replace(/[^0-9.-]+/g,""));

                if(!isNaN(xNum) && !isNaN(yNum)) {
                    if (dir == "asc") { if (xNum > yNum) { shouldSwitch = true; break; } } 
                    else if (dir == "desc") { if (xNum < yNum) { shouldSwitch = true; break; } }
                } else {
                    if (dir == "asc") { if (xContent > yContent) { shouldSwitch = true; break; } } 
                    else if (dir == "desc") { if (xContent < yContent) { shouldSwitch = true; break; } }
                }
            }
            if (shouldSwitch) {
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
                switchcount ++;      
            } else {
                if (switchcount == 0 && dir == "asc") {
                    dir = "desc";
                    switching = true;
                }
            }
        }
    }

    function exportToExcel(tableId, filename = 'download') {
        let downloadLink;
        let dataType = 'application/vnd.ms-excel';
        let tableSelect = document.getElementById(tableId);
        let tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
        
        filename = filename ? filename + '.xls' : 'excel_data.xls';
        downloadLink = document.createElement("a");
        document.body.appendChild(downloadLink);
        
        if(navigator.msSaveOrOpenBlob){
            var blob = new Blob(['\ufeff', tableHTML], { type: dataType });
            navigator.msSaveOrOpenBlob( blob, filename);
        } else {
            downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
            downloadLink.download = filename;
            downloadLink.click();
        }
    }
</script>