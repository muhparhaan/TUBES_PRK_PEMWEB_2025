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
    $id_supplier = $_POST['id_supplier'] ?? NULL; // Dapat berupa string '0' atau ID
    $id_barang = $_POST['id_barang'] ?? 0; // Hanya terisi jika aksi 'ubah'
    
    // Logika untuk menangani Barang Koperasi: ID Supplier 0 atau "0" harus menjadi NULL
    if (empty($id_supplier) || $id_supplier == '0') {
        $id_supplier = NULL;
    }

    switch ($action) {
        case 'tambah':
            tambahBarang($conn, $nama_barang, $harga_jual, $stok, $id_supplier);
            break;
        case 'ubah':
            ubahBarang($conn, $id_barang, $nama_barang, $harga_jual, $stok, $id_supplier);
            break;
        default:
            header("Location: ../admin/master_barang.php?status=error&pesan=Aksi tidak dikenal");
            exit();
    }
}

// ----------------------
// 2. LOGIC UNTUK GET (ARSIP/SOFT DELETE)
// ----------------------
if (isset($_GET['action']) && $_GET['action'] === 'arsip' && isset($_GET['id'])) {
    arsipBarang($conn, $_GET['id']);
}

// ----------------------
// 3. DEFINISI FUNGSI
// ----------------------

// Fungsi CREATE
function tambahBarang($conn, $nama_barang, $harga_jual, $stok, $id_supplier) {
    $stmt = $conn->prepare("INSERT INTO barang (nama_barang, harga_jual, stok, id_supplier) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdis", $nama_barang, $harga_jual, $stok, $id_supplier); 
    
    if ($stmt->execute()) {
        header("Location: ../admin/master_barang.php?status=sukses&pesan=Barang berhasil ditambahkan!");
    } else {
        header("Location: ../admin/master_barang.php?status=error&pesan=Gagal menambahkan Barang: " . $stmt->error);
    }
    $stmt->close();
    exit();
}

// Fungsi UPDATE
function ubahBarang($conn, $id_barang, $nama_barang, $harga_jual, $stok, $id_supplier) {
    $stmt = $conn->prepare("UPDATE barang SET nama_barang = ?, harga_jual = ?, stok = ?, id_supplier = ? WHERE id_barang = ?");
    $stmt->bind_param("sdsii", $nama_barang, $harga_jual, $stok, $id_supplier, $id_barang); 
    
    if ($stmt->execute()) {
        header("Location: ../admin/master_barang.php?status=sukses&pesan=Barang berhasil diubah!");
    } else {
        header("Location: ../admin/master_barang.php?status=error&pesan=Gagal mengubah Barang: " . $stmt->error);
    }
    $stmt->close();
    exit();
}

// Fungsi SOFT DELETE
function arsipBarang($conn, $id_barang) {
    $stmt = $conn->prepare("UPDATE barang SET is_active = '0' WHERE id_barang = ?");
    $stmt->bind_param("i", $id_barang);
    
    if ($stmt->execute()) {
        header("Location: ../admin/master_barang.php?status=sukses&pesan=Barang berhasil diarsipkan!");
    } else {
        header("Location: ../admin/master_barang.php?status=error&pesan=Gagal mengarsipkan Barang: " . $stmt->error);
    }
    $stmt->close();
    exit();
}
?>