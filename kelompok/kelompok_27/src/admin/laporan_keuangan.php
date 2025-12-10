<?php
include('../config/koneksi.php');
include('../layout/header.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

$tgl_awal  = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d');

$query = "SELECT t.no_faktur, t.tanggal_transaksi, d.qty, d.subtotal, 
                 b.nama_barang, b.harga_jual, 
                 s.nama_supplier, s.kategori, s.no_hp,
                 u.username
          FROM detail_transaksi d
          JOIN transaksi t ON d.id_transaksi = t.id_transaksi
          LEFT JOIN users u ON t.id_user = u.id_user 
          JOIN barang b ON d.id_barang = b.id_barang
          LEFT JOIN suppliers s ON b.id_supplier = s.id_supplier
          WHERE DATE(t.tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir'
          ORDER BY t.tanggal_transaksi DESC";

$result = mysqli_query($conn, $query);

$data_laporan = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data_laporan[] = $row;
    }
}

$total_omzet = 0;
$total_profit_toko = 0;
$total_setor_penitip = 0;

$rekap_tagihan = [];

$grand_total_qty = 0;
$grand_total_omzet = 0;
$grand_total_fee = 0;
$grand_total_setor = 0;

foreach ($data_laporan as $row) {
    $omzet = $row['subtotal'];
    $total_omzet += $omzet;

    if ($row['nama_supplier'] == NULL) {
        $total_profit_toko += $omzet;
        
        $grand_total_qty += $row['qty'];
        $grand_total_omzet += $omzet;
    } else {
        $nama_sup = $row['nama_supplier'];
        $hp_sup   = $row['no_hp'];
        $tgl_trx  = date('Y-m-d', strtotime($row['tanggal_transaksi'])); 
        
        if ($row['kategori'] == 'internal') {
            $fee = $omzet * 0.10; 
        } else {
            $fee = $omzet * 0.15; 
        }
        
        $hak_penitip = $omzet - $fee;

        $total_profit_toko += $fee;
        $total_setor_penitip += $hak_penitip;

        $key = $tgl_trx . '_' . $nama_sup;

        if (!isset($rekap_tagihan[$key])) {
            $rekap_tagihan[$key] = [
                'tanggal' => $tgl_trx,
                'nama' => $nama_sup,
                'no_hp' => $hp_sup,
                'total_qty' => 0,
                'total_omzet' => 0,
                'total_fee' => 0,
                'total_setor' => 0
            ];
        }

        $rekap_tagihan[$key]['total_qty'] += $row['qty'];
        $rekap_tagihan[$key]['total_omzet'] += $omzet;
        $rekap_tagihan[$key]['total_fee'] += $fee;
        $rekap_tagihan[$key]['total_setor'] += $hak_penitip;

        $grand_total_qty += $row['qty'];
        $grand_total_omzet += $omzet;
        $grand_total_fee += $fee;
        $grand_total_setor += $hak_penitip;
    }
}

krsort($rekap_tagihan);
?>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
    .wrapper-flex { display: flex; min-height: 100vh; width: 100%; }
    .content-wrapper { background: #f4f6f9; flex: 1; padding: 30px; min-height: 100vh; }
    
    .card { border: none; border-radius: 15px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08); background: white; overflow: hidden; margin-bottom: 20px; }
    .card-header { border: none; padding: 20px 25px; background: white; border-bottom: 1px solid #f0f0f0; }
    .card-title { font-weight: 800; color: #1B3C53; font-size: 1.1rem; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
    
    .widget-card { border-radius: 15px; padding: 25px; color: white; position: relative; overflow: hidden; box-shadow: 0 10px 20px rgba(0,0,0,0.1); transition: transform 0.2s; }
    .widget-card:hover { transform: translateY(-5px); }
    .widget-card h3 { font-weight: 800; font-size: 2rem; margin-bottom: 5px; }
    .widget-card p { font-size: 0.9rem; opacity: 0.9; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; }
    .widget-icon { position: absolute; right: 20px; top: 50%; transform: translateY(-50%); font-size: 4rem; opacity: 0.2; }
    
    .table thead th { background: linear-gradient(90deg, #1B3C53, #2F5C83); color: white; border: none; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; padding: 15px; cursor: pointer; }
    .table thead th:hover { background: linear-gradient(90deg, #2F5C83, #1B3C53); }
    .table tbody td { vertical-align: middle; padding: 15px; color: #444; border-bottom: 1px solid #f2f2f2; font-size: 0.9rem; }
    
    .table tfoot th { background-color: #f8f9fa; border-top: 2px solid #1B3C53; color: #1B3C53; font-weight: 800; padding: 15px; font-size: 1rem; }

    .form-control { border-radius: 10px; padding: 10px 15px; border: 1px solid #e0e0e0; }
    .btn { border-radius: 50px; font-weight: 600; padding: 8px 25px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .btn-primary { background: linear-gradient(45deg, #1B3C53, #4a90e2); border: none; }
    
    .badge { padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 0.75rem; }

    @media print {
        .sidebar, .btn, form, .content-header p, .widget-icon, .no-print { display: none !important; }
        .wrapper-flex { display: block; }
        .content-wrapper { padding: 0; background: white; }
        .card { box-shadow: none; border: 1px solid #ddd; page-break-inside: avoid; }
        .table thead th { background: #333 !important; color: white !important; -webkit-print-color-adjust: exact; }
        .table tfoot th { background: #eee !important; -webkit-print-color-adjust: exact; }
        body { background: white; }
    }
</style>

<div class="wrapper-flex">
    <?php include('../layout/sidebar.php'); ?>
    
    <div class="content-wrapper">
        <section class="content-header mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 style="color: #1B3C53; font-weight: 800; font-size: 2rem; letter-spacing: -1px;">
                        <i class="fas fa-chart-pie mr-2"></i> Laporan Keuangan
                    </h1>
                    <p class="text-muted m-0">Rekapitulasi omzet dan profit sharing per Periode.</p>
                </div>
                <div class="col-md-6">
                    <form method="GET" id="filterForm" class="d-flex justify-content-md-end bg-white p-2 rounded shadow-sm" style="border-radius: 50px !important;" onsubmit="return validateDates()">
                        <div class="d-flex align-items-center me-2">
                            <span class="text-muted small fw-bold me-2 ps-3">PERIODE:</span>
                            <input type="date" name="tgl_awal" id="tgl_awal" class="form-control form-control-sm border-0 bg-light" value="<?= $tgl_awal; ?>" style="width: 130px;">
                            <span class="mx-2 text-muted">-</span>
                            <input type="date" name="tgl_akhir" id="tgl_akhir" class="form-control form-control-sm border-0 bg-light" value="<?= $tgl_akhir; ?>" style="width: 130px;">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm rounded-circle" style="width: 35px; height: 35px; padding: 0;" title="Filter Data">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="widget-card" style="background: linear-gradient(135deg, #34495e, #2c3e50);">
                    <p>Total Omzet Masuk</p>
                    <h3>Rp <?= number_format($total_omzet, 0, ',', '.'); ?></h3>
                    <div class="widget-icon"><i class="fas fa-cash-register"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="widget-card" style="background: linear-gradient(135deg, #1B3C53, #4a90e2);">
                    <p>Profit Bersih Koperasi</p>
                    <h3>Rp <?= number_format($total_profit_toko, 0, ',', '.'); ?></h3>
                    <small style="opacity: 0.8;">(Untung Jual Barang Sendiri + Fee Titipan)</small>
                    <div class="widget-icon"><i class="fas fa-coins"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="widget-card" style="background: linear-gradient(135deg, #e67e22, #f39c12);">
                    <p>Wajib Setor ke Penitip</p>
                    <h3>Rp <?= number_format($total_setor_penitip, 0, ',', '.'); ?></h3>
                    <small style="opacity: 0.8;">(Uang Hak Milik Supplier)</small>
                    <div class="widget-icon"><i class="fas fa-hand-holding-usd"></i></div>
                </div>
            </div>
        </div>

        <section class="content mb-4">
            <div class="card border-primary" style="border: 1px solid #cfe2ff;">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: #ecf4ff;">
                    <div class="d-flex align-items-center">
                        <h3 class="card-title text-primary me-3"><i class="fas fa-file-invoice-dollar me-2"></i> Rekap Tagihan Supplier (Per Hari)</h3>
                        <input type="text" id="searchRekap" onkeyup="filterTable('rekapTable', 'searchRekap')" placeholder="Cari Supplier..." class="form-control form-control-sm no-print" style="width: 200px; border-color: #b6d4fe;">
                    </div>
                    <div class="btn-group">
                         <button onclick="exportToExcel('rekapTable', 'Laporan_Rekap_Supplier')" class="btn btn-sm btn-success me-2 text-white"><i class="fas fa-file-excel me-1"></i> Excel</button>
                        <button onclick="window.print()" class="btn btn-sm btn-outline-primary"><i class="fas fa-print me-1"></i> Cetak</button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0" id="rekapTable">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th class="ps-4" onclick="sortTable('rekapTable', 0)">Tanggal <i class="fas fa-sort small ms-1"></i></th>
                                    <th onclick="sortTable('rekapTable', 1)">Nama Supplier / Penitip <i class="fas fa-sort small ms-1"></i></th>
                                    <th>Kontak</th>
                                    <th class="text-center" onclick="sortTable('rekapTable', 3)">Qty <i class="fas fa-sort small ms-1"></i></th>
                                    <th class="text-end" onclick="sortTable('rekapTable', 4)">Omzet (Kotor) <i class="fas fa-sort small ms-1"></i></th>
                                    <th class="text-end text-success">Fee (Koperasi)</th>
                                    <th class="text-end pe-4 text-danger fw-bold">Wajib Bayar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($rekap_tagihan)): ?>
                                    <tr><td colspan="7" class="text-center py-5 text-muted">Belum ada penjualan barang titipan pada periode ini.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($rekap_tagihan as $sup): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark" style="background: #fff;">
                                            <?= date('d/m/Y', strtotime($sup['tanggal'])); ?>
                                        </td>
                                        <td class="fw-bold text-primary"><?= $sup['nama']; ?></td>
                                        <td><i class="fab fa-whatsapp text-success me-1"></i> <?= $sup['no_hp']; ?></td>
                                        <td class="text-center"><?= $sup['total_qty']; ?></td>
                                        <td class="text-end text-muted">Rp <?= number_format($sup['total_omzet']); ?></td>
                                        <td class="text-end text-success fw-bold">+ Rp <?= number_format($sup['total_fee']); ?></td>
                                        <td class="text-end pe-4 fw-bold text-danger" style="font-size: 1.1em;">
                                            Rp <?= number_format($sup['total_setor']); ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end pe-3 text-uppercase">Total Keseluruhan :</th>
                                    <th class="text-center"><?= number_format($grand_total_qty); ?></th>
                                    <th class="text-end">Rp <?= number_format($grand_total_omzet); ?></th>
                                    <th class="text-end text-success">Rp <?= number_format($grand_total_fee); ?></th>
                                    <th class="text-end text-danger pe-4">Rp <?= number_format($grand_total_setor); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white text-end">
                    <small class="text-muted fst-italic">Gunakan tabel ini sebagai acuan nominal transfer harian ke masing-masing penitip.</small>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <h3 class="card-title me-3"><i class="fas fa-list me-2"></i> Rincian Riwayat Transaksi</h3>
                        <input type="text" id="searchRincian" onkeyup="filterTable('rincianTable', 'searchRincian')" placeholder="Cari Barang/Faktur..." class="form-control form-control-sm no-print" style="width: 200px;">
                    </div>
                    <span class="badge bg-light text-dark border">Semua Barang</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="rincianTable">
                            <thead>
                                <tr>
                                    <th class="ps-4" onclick="sortTable('rincianTable', 0)">Tanggal & Faktur <i class="fas fa-sort small ms-1"></i></th>
                                    <th onclick="sortTable('rincianTable', 1)">Barang <i class="fas fa-sort small ms-1"></i></th>
                                    <th onclick="sortTable('rincianTable', 2)">Supplier <i class="fas fa-sort small ms-1"></i></th>
                                    <th class="text-center" onclick="sortTable('rincianTable', 3)">Qty <i class="fas fa-sort small ms-1"></i></th>
                                    <th class="text-end pe-4" onclick="sortTable('rincianTable', 4)">Subtotal <i class="fas fa-sort small ms-1"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($data_laporan)): ?>
                                    <tr><td colspan="5" class="text-center py-5 text-muted">Tidak ada transaksi.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($data_laporan as $row): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-primary small"><?= $row['no_faktur']; ?></div>
                                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($row['tanggal_transaksi'])); ?></small>
                                        </td>
                                        <td>
                                            <?= $row['nama_barang']; ?>
                                            <br>
                                            <small class="text-muted">Kasir: <?= ucfirst($row['username'] ?? '-'); ?></small>
                                        </td>
                                        <td>
                                            <?php if($row['nama_supplier']): ?>
                                                <span class="badge bg-warning text-dark bg-opacity-25"><?= $row['nama_supplier']; ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary">Koperasi</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?= $row['qty']; ?></td>
                                        <td class="text-end fw-bold pe-4">Rp <?= number_format($row['subtotal']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end pe-3 text-uppercase">Total Transaksi :</th>
                                    <th class="text-center"><?= number_format($grand_total_qty); ?></th>
                                    <th class="text-end pe-4">Rp <?= number_format($grand_total_omzet); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </section>

    </div>
</div>

<?php include('../layout/footer.php'); ?>

<script>
    function validateDates() {
        const tglAwal = document.getElementById('tgl_awal').value;
        const tglAkhir = document.getElementById('tgl_akhir').value;

        if (tglAwal && tglAkhir && tglAwal > tglAkhir) {
            alert('❌ Eror: Tanggal Awal tidak boleh lebih besar dari Tanggal Akhir!');
            return false;
        }
        return true;
    }

    function filterTable(tableId, inputId) {
        let input, filter, table, tr, td, i, txtValue;
        input = document.getElementById(inputId);
        filter = input.value.toUpperCase();
        table = document.getElementById(tableId);
        tr = table.getElementsByTagName("tr");

        for (i = 1; i < tr.length; i++) { 
            if(tr[i].parentNode.nodeName === 'TFOOT') continue;

            let rowVisible = false;
            let tds = tr[i].getElementsByTagName("td");
            
            for(let j = 0; j < tds.length; j++) {
                if (tds[j]) {
                    txtValue = tds[j].textContent || tds[j].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        rowVisible = true;
                        break; 
                    }
                }
            }

            if (rowVisible) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }

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
                    if (dir == "asc") {
                        if (xNum > yNum) { shouldSwitch = true; break; }
                    } else if (dir == "desc") {
                        if (xNum < yNum) { shouldSwitch = true; break; }
                    }
                } else {
                    if (dir == "asc") {
                        if (xContent > yContent) { shouldSwitch = true; break; }
                    } else if (dir == "desc") {
                        if (xContent < yContent) { shouldSwitch = true; break; }
                    }
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
            var blob = new Blob(['\ufeff', tableHTML], {
                type: dataType
            });
            navigator.msSaveOrOpenBlob( blob, filename);
        } else {
            downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
        
            downloadLink.download = filename;
            
            downloadLink.click();
        }
    }
</script>