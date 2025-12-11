<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['id_user']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: ../staff/transaksi.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$total_bayar = $_POST['total_bayar'] ?? 0;
$metode = $_POST['metode_pembayaran'] ?? 'cash';
$tanggal_jam = date('Y-m-d H:i:s');

$no_faktur = "INV-" . date('YmdHis') . "-" . rand(10,99);

if (empty($_POST['id_barang'])) {
    echo "<script>alert('Keranjang kosong! Pilih barang dulu.'); window.history.back();</script>";
    exit();
}

mysqli_begin_transaction($conn);

try {
    $stmt_header = $conn->prepare("INSERT INTO transaksi (no_faktur, tanggal_transaksi, total_bayar, metode_pembayaran, id_user) VALUES (?, ?, ?, ?, ?)");
    $stmt_header->bind_param("ssdsi", $no_faktur, $tanggal_jam, $total_bayar, $metode, $id_user);
    
    if (!$stmt_header->execute()) {
        throw new Exception("Gagal simpan transaksi: " . $stmt_header->error);
    }
    
    $id_transaksi = $conn->insert_id;

    $list_id = $_POST['id_barang'];
    $list_qty = $_POST['qty'];
    $list_harga = $_POST['harga_satuan'];

    $stmt_detail = $conn->prepare("INSERT INTO detail_transaksi (id_transaksi, id_barang, harga_saat_transaksi, qty, subtotal) VALUES (?, ?, ?, ?, ?)");
    $stmt_update_stok = $conn->prepare("UPDATE barang SET stok = stok - ? WHERE id_barang = ?");

    for ($i = 0; $i < count($list_id); $i++) {
        $id_barang = $list_id[$i];
        $qty = $list_qty[$i];
        $harga = $list_harga[$i];
        $subtotal = $qty * $harga;

        $cek_stok = mysqli_query($conn, "SELECT stok FROM barang WHERE id_barang = '$id_barang'");
        $data_stok = mysqli_fetch_assoc($cek_stok);

        if ($data_stok['stok'] < $qty) {
            throw new Exception("Stok barang ID $id_barang tidak cukup! Sisa: " . $data_stok['stok']);
        }

        $stmt_detail->bind_param("iidid", $id_transaksi, $id_barang, $harga, $qty, $subtotal);
        if (!$stmt_detail->execute()) {
            throw new Exception("Gagal simpan detail: " . $stmt_detail->error);
        }

        $stmt_update_stok->bind_param("ii", $qty, $id_barang);
        if (!$stmt_update_stok->execute()) {
            throw new Exception("Gagal update stok barang ID $id_barang");
        }
    }

    mysqli_commit($conn);

    echo "<script>
            alert('TRANSAKSI BERHASIL!\\nNo Faktur: $no_faktur\\nTotal: Rp " . number_format($total_bayar,0,',','.') . "');
            window.location.href = '../staff/transaksi.php';
          </script>";

} catch (Exception $e) {
    mysqli_rollback($conn);
    
    echo "<script>
            alert('TRANSAKSI GAGAL!\\nPenyebab: " . addslashes($e->getMessage()) . "');
            window.history.back();
          </script>";
}
?>