<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: ../index.php");
    exit();
}

include '../config/koneksi.php';
include '../layout/header.php';

$query_barang = mysqli_query($conn, "SELECT * FROM barang WHERE stok > 0 AND is_active = '1'");
?>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
    .wrapper-flex { display: flex; min-height: 100vh; width: 100%; }
    .content-wrapper { background: #f4f6f9; flex: 1; padding: 30px; min-height: 100vh; }
    .card { border: none; border-radius: 15px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08); background: white; overflow: hidden; margin-bottom: 20px; }
    .card-header { border-bottom: 1px solid #f0f0f0; padding: 20px 25px; background: #fff; }
    .card-title { font-weight: 700; color: #1B3C53; font-size: 1.1rem; margin: 0; text-transform: uppercase; letter-spacing: 1px; }
    .table thead th { background: linear-gradient(90deg, #1B3C53, #2F5C83); color: white; border: none; font-weight: 500; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 15px; }
    .table tbody td { vertical-align: middle; padding: 15px; color: #444; border-bottom: 1px solid #f2f2f2; font-size: 0.95rem; font-weight: 500; }
    .table tbody tr:hover { background-color: #f1f7fd; transition: all 0.2s ease; }
    .btn { border-radius: 50px; padding: 8px 20px; font-weight: 600; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.2s; }
    .btn:hover { transform: translateY(-2px); }
    .btn-success { background: linear-gradient(45deg, #2ecc71, #27ae60); border: none; }
    .btn-primary { background: linear-gradient(45deg, #1B3C53, #4a90e2); border: none; }
    .btn-danger { background: linear-gradient(45deg, #e74c3c, #c0392b); border: none; }
    .badge { padding: 8px 12px; border-radius: 30px; font-weight: 600; font-size: 0.75rem; }
    .badge-info { background: #e3f2fd; color: #1565c0; border: 1px solid #bbdefb; }
    .form-control, .form-select { border-radius: 10px !important; padding: 10px 15px !important; border: 1px solid #e0e0e0 !important; font-size: 0.95rem; }
    .form-control:focus { border-color: #1B3C53; box-shadow: 0 0 0 3px rgba(27, 60, 83, 0.1); }
</style>

<div class="wrapper-flex">
    
    <?php include('../layout/sidebar.php'); ?>
    
    <div class="content-wrapper">
        
        <section class="content-header mb-4">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-sm-12">
                        <h1 style="color: #1B3C53; font-weight: 800; font-size: 2rem; letter-spacing: -1px;">
                            <i class="fas fa-cash-register mr-2"></i> Transaksi Kasir
                        </h1>
                        <p class="text-muted m-0" style="font-size: 1rem;">
                            Mode Penjualan (POS) - Pilih barang dan proses pembayaran
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="row">

                <div class="col-xl-7 col-lg-7 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title"><i class="fas fa-boxes me-2"></i> Katalog Barang</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="height: 550px; overflow-y: auto;">
                                <table class="table mb-0" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="sticky-top" style="z-index: 10;">
                                        <tr>
                                            <th>Nama Barang</th>
                                            <th width="15%" class="text-center">Stok</th>
                                            <th>Harga</th>
                                            <th width="15%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($item = mysqli_fetch_assoc($query_barang)) : ?>
                                        <tr>
                                            <td style="font-weight: 600; color: #2c3e50;"><?= $item['nama_barang'] ?></td>
                                            <td class="text-center">
                                                <span class="badge badge-info"><?= $item['stok'] ?> Unit</span>
                                            </td>
                                            <td style="color: #27ae60; font-weight: 700;">
                                                Rp <?= number_format($item['harga_jual'], 0, ',', '.') ?>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-success btn-sm btn-add btn-block shadow-sm" 
                                                    data-id="<?= $item['id_barang'] ?>" 
                                                    data-nama="<?= $item['nama_barang'] ?>" 
                                                    data-harga="<?= $item['harga_jual'] ?>"
                                                    data-stok="<?= $item['stok'] ?>">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-5 col-lg-5">
                    <div class="card h-100">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h3 class="card-title" style="color: #1B3C53;"><i class="fas fa-shopping-cart me-2"></i> Keranjang</h3>
                            <span class="badge badge-info" id="badgeCount" style="font-size: 0.9rem;">0 Item</span>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <form action="../proses/transaksi_proses.php" method="POST" id="formCheckout" class="h-100 d-flex flex-column">
                                
                                <div class="table-responsive flex-grow-1 mb-3" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-sm table-borderless">
                                        <thead style="background: #f8f9fa;">
                                            <tr>
                                                <th style="background: #f8f9fa; color: #333;">Item</th>
                                                <th width="20%" style="background: #f8f9fa; color: #333;">Qty</th>
                                                <th class="text-right" style="background: #f8f9fa; color: #333;">Total</th>
                                                <th style="background: #f8f9fa;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="keranjangBody">
                                            </tbody>
                                    </table>
                                    
                                    <div id="emptyMsg" class="text-center text-muted mt-5">
                                        <i class="fas fa-shopping-basket fa-3x mb-3" style="color: #dee2e6;"></i>
                                        <p class="font-weight-bold" style="color: #adb5bd;">Keranjang Masih Kosong</p>
                                    </div>
                                </div>

                                <div class="mt-auto pt-3 border-top">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="font-weight-bold text-gray-800 m-0">Total Tagihan</h5>
                                        <h3 class="font-weight-bold m-0" style="color: #1B3C53;">Rp <span id="displayTotal">0</span></h3>
                                        <input type="hidden" name="total_bayar" id="inputTotalBayar">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold small text-uppercase text-muted mb-2">Metode Pembayaran</label>
                                        <select name="metode_pembayaran" class="form-control" required style="height: auto;">
                                            <option value="cash">💵 Uang Tunai (Cash)</option>
                                            <option value="qris">📱 QRIS (Scan)</option>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-block btn-lg w-100 shadow" onclick="return confirm('Proses Transaksi?')">
                                        <i class="fas fa-print me-2"></i> BAYAR & CETAK
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div> </section>
    </div> </div> <script>
    let keranjang = [];

    const formatRupiah = (num) => {
        return new Intl.NumberFormat('id-ID').format(num);
    }

    document.querySelectorAll('.btn-add').forEach(btn => {
        btn.addEventListener('click', function() {
            let id = this.getAttribute('data-id');
            let nama = this.getAttribute('data-nama');
            let harga = parseInt(this.getAttribute('data-harga'));
            let maxStok = parseInt(this.getAttribute('data-stok'));

            let itemAda = keranjang.find(k => k.id === id);

            if (itemAda) {
                if (itemAda.qty < maxStok) {
                    itemAda.qty++;
                    itemAda.subtotal = itemAda.qty * harga;
                } else {
                    alert("Stok Habis! Sisa: " + maxStok);
                }
            } else {
                keranjang.push({ id, nama, harga, qty: 1, subtotal: harga, maxStok });
            }
            renderCart();
        });
    });

    function renderCart() {
        let tbody = document.getElementById('keranjangBody');
        let emptyMsg = document.getElementById('emptyMsg');
        let badge = document.getElementById('badgeCount');
        
        tbody.innerHTML = '';
        let grandTotal = 0;

        if (keranjang.length === 0) {
            emptyMsg.style.display = 'block';
        } else {
            emptyMsg.style.display = 'none';
        }
        
        badge.innerText = keranjang.length + " Item";

        keranjang.forEach((item, index) => {
            grandTotal += item.subtotal;
            let row = `
                <tr>
                    <td class="align-middle">
                        <div style="font-weight: 600; color: #2c3e50;">${item.nama}</div>
                        <input type="hidden" name="id_barang[]" value="${item.id}">
                        <input type="hidden" name="harga_satuan[]" value="${item.harga}">
                    </td>
                    <td class="align-middle">
                        <input type="number" name="qty[]" value="${item.qty}" min="1" max="${item.maxStok}" 
                               class="form-control px-1 text-center" style="height: 30px; font-size: 0.9rem;" 
                               onchange="updateQty(${index}, this.value)">
                    </td>
                    <td class="text-right align-middle" style="font-weight: 600;">
                        ${formatRupiah(item.subtotal)}
                    </td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-danger btn-sm py-0 px-2" 
                                style="border-radius: 5px; height: 30px;" onclick="hapusItem(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });

        document.getElementById('displayTotal').innerText = formatRupiah(grandTotal);
        document.getElementById('inputTotalBayar').value = grandTotal;
    }

    window.updateQty = function(index, val) {
        let item = keranjang[index];
        if (val > item.maxStok) val = item.maxStok;
        if (val < 1) val = 1;
        item.qty = parseInt(val);
        item.subtotal = item.qty * item.harga;
        renderCart();
    }

    window.hapusItem = function(index) {
        keranjang.splice(index, 1);
        renderCart();
    }
</script>

<?php include '../layout/footer.php'; ?>