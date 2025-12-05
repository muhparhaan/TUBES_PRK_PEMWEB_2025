<?php
include('../config/koneksi.php'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? ''; 
    $nama_supplier = $_POST['nama_supplier'] ?? '';
    $no_hp = $_POST['no_hp'] ?? '';
    $kategori = $_POST['kategori'] ?? ''; 
    $id_supplier = $_POST['id_supplier'] ?? 0;
    
    switch ($action) {
        case 'tambah':
            tambahSupplier($koneksi, $nama_supplier, $no_hp, $kategori);
            break;
        case 'ubah':
            ubahSupplier($koneksi, $id_supplier, $nama_supplier, $no_hp, $kategori);
            break;
        default:
            header("Location: ../admin/master_supplier.php?status=error&pesan=Aksi tidak dikenal");
            exit();
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'arsip' && isset($_GET['id'])) {
    arsipSupplier($koneksi, $_GET['id']);
}


function tambahSupplier($koneksi, $nama_supplier, $no_hp, $kategori) {
    $stmt = $koneksi->prepare("INSERT INTO suppliers (nama_supplier, no_hp, kategori) VALUES (?, ?, ?)");
    
    $stmt->bind_param("sss", $nama_supplier, $no_hp, $kategori); 
    
    if ($stmt->execute()) {
        header("Location: ../admin/master_supplier.php?status=sukses&pesan=Supplier berhasil ditambahkan!");
    } else {
        header("Location: ../admin/master_supplier.php?status=error&pesan=Gagal menambahkan Supplier: " . $stmt->error);
    }
    $stmt->close();
    exit();
}

function ubahSupplier($koneksi, $id_supplier, $nama_supplier, $no_hp, $kategori) {
    $stmt = $koneksi->prepare("UPDATE suppliers SET nama_supplier = ?, no_hp = ?, kategori = ? WHERE id_supplier = ?");

    $stmt->bind_param("sssi", $nama_supplier, $no_hp, $kategori, $id_supplier);

    if ($stmt->execute()) {
        header("Location: ../admin/master_supplier.php?status=sukses&pesan=Supplier berhasil diubah!");
    } else {
        header("Location: ../admin/master_supplier.php?status=error&pesan=Gagal mengubah Supplier: " . $stmt->error);
    }
    $stmt->close();
    exit();
}

function arsipSupplier($koneksi, $id_supplier) {
    $stmt = $koneksi->prepare("UPDATE suppliers SET is_active = '0' WHERE id_supplier = ?");
    
    $stmt->bind_param("i", $id_supplier);
    
    if ($stmt->execute()) {
        header("Location: ../admin/master_supplier.php?status=sukses&pesan=Supplier berhasil diarsipkan!");
    } else {
        header("Location: ../admin/master_supplier.php?status=error&pesan=Gagal mengarsipkan Supplier: " . $stmt->error);
    }
    $stmt->close();
    exit();
}

?>