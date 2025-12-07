<?php
// PASTIKAN PATH INI BENAR!
include('../config/koneksi.php'); 
// Asumsi variabel koneksi di koneksi.php adalah $conn

// ----------------------
// 1. LOGIC UNTUK POST (TAMBAH & UBAH DATA)
// ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? ''; 
    
    // Ambil data dari form Tambah/Ubah Barang
    $nama_barang = $_POST['nama_barang'] ?? '';
    $harga_jual = $_POST['harga_jual'] ?? 0;
    $stok = $_POST['stok'] ?? 0;
    $id_supplier = $_POST['id_supplier'] ?? NULL; 
    $id_barang = $_POST['id_barang'] ?? 0; 
    
    // Logika untuk menangani Barang Koperasi: ID Supplier 0 atau "0" harus menjadi NULL
    if (empty($id_supplier) || $id_supplier == '0') {
        $id_supplier = NULL;
    }

    switch ($action) {
        case 'tambah':
            tambahBarang($conn, $nama_barang, $harga_jual, $stok, $id_supplier);
            break;
        case 'ubah':
ubahBarang($conn, $id_barang, $nama_barang, $harga_jual, $stok, $id_supplier);            break;
        default:
            header("Location: ../admin/master_barang.php?status=error&pesan=Aksi tidak dikenal");
            exit();
    }
}

// ----------------------
// 2. LOGIC UNTUK GET (ARSIP/SOFT DELETE)
// ----------------------
if (isset($_GET['action']) && $_GET['action'] === 'arsip' && isset($_GET['id'])) {
    // Logika SOFT DELETE akan dipanggil di Step I.4
}

// ----------------------
// 3. DEFINISI FUNGSI (Akan diisi di step selanjutnya)
// ----------------------
// ----------------------
// 3. DEFINISI FUNGSI
// ----------------------

function tambahBarang($conn, $nama_barang, $harga_jual, $stok, $id_supplier) {
    // Menggunakan prepared statement untuk INSERT data barang
    $stmt = $conn->prepare("INSERT INTO barang (nama_barang, harga_jual, stok, id_supplier) VALUES (?, ?, ?, ?)");
    
    // Binding parameter: 'sdis' (string, decimal/double, integer, string/supplier_id)
    $stmt->bind_param("sdis", $nama_barang, $harga_jual, $stok, $id_supplier); 
    
    if ($stmt->execute()) {
        header("Location: ../admin/master_barang.php?status=sukses&pesan=Barang berhasil ditambahkan!");
    } else {
        header("Location: ../admin/master_barang.php?status=error&pesan=Gagal menambahkan Barang: " . $stmt->error);
    }
    $stmt->close();
    exit();
}

function ubahBarang($conn, $id_barang, $nama_barang, $harga_jual, $stok, $id_supplier) {
    // Menggunakan prepared statement untuk UPDATE data barang berdasarkan id_barang
    $stmt = $conn->prepare("UPDATE barang SET nama_barang = ?, harga_jual = ?, stok = ?, id_supplier = ? WHERE id_barang = ?");
    
    // Binding parameter: 'sdsi' (barang, jual, stok, supplier ID) + 'i' (barang ID)
    $stmt->bind_param("sdsii", $nama_barang, $harga_jual, $stok, $id_supplier, $id_barang); 
    
    if ($stmt->execute()) {
        header("Location: ../admin/master_barang.php?status=sukses&pesan=Barang berhasil diubah!");
    } else {
        header("Location: ../admin/master_barang.php?status=error&pesan=Gagal mengubah Barang: " . $stmt->error);
    }
    $stmt->close();
    exit();
}
// Placeholder untuk fungsi ubahBarang dan arsipBarang tetap ada di bawah ini.
?>