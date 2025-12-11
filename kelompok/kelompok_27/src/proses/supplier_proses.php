<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../config/koneksi.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../admin/master_supplier.php?status=error&pesan=Anda tidak punya akses!");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    $nama_supplier = $_POST['nama_supplier'] ?? '';
    $no_hp         = $_POST['no_hp'] ?? '';
    $kategori      = $_POST['kategori'] ?? '';
    $id_supplier   = $_POST['id_supplier'] ?? 0;

    switch ($action) {
        case 'tambah':
            tambahSupplier($conn, $nama_supplier, $no_hp, $kategori);
            break;

        case 'ubah':
            ubahSupplier($conn, $id_supplier, $nama_supplier, $no_hp, $kategori);
            break;

        default:
            header("Location: ../admin/master_supplier.php?status=error&pesan=Aksi tidak dikenal");
            exit();
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'arsip' && isset($_GET['id'])) {
    arsipSupplier($conn, $_GET['id']);
}


function tambahSupplier($conn, $nama, $hp, $kategori) {
    $stmt = $conn->prepare("INSERT INTO suppliers (nama_supplier, no_hp, kategori, is_active) VALUES (?, ?, ?, '1')");
    
    $stmt->bind_param("sss", $nama, $hp, $kategori);

    if ($stmt->execute()) {
        header("Location: ../admin/master_supplier.php?status=sukses&pesan=Penitip berhasil ditambahkan!");
    } else {
        header("Location: ../admin/master_supplier.php?status=error&pesan=Gagal menambahkan: " . $stmt->error);
    }
    $stmt->close();
    exit();
}

function ubahSupplier($conn, $id, $nama, $hp, $kategori) {
    $stmt = $conn->prepare("UPDATE suppliers SET nama_supplier = ?, no_hp = ?, kategori = ? WHERE id_supplier = ?");
    
    $stmt->bind_param("sssi", $nama, $hp, $kategori, $id);

    if ($stmt->execute()) {
        header("Location: ../admin/master_supplier.php?status=sukses&pesan=Data penitip berhasil diubah!");
    } else {
        header("Location: ../admin/master_supplier.php?status=error&pesan=Gagal mengubah: " . $stmt->error);
    }
    $stmt->close();
    exit();
}

function arsipSupplier($conn, $id) {
    $stmt = $conn->prepare("UPDATE suppliers SET is_active = '0' WHERE id_supplier = ?");
    
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: ../admin/master_supplier.php?status=sukses&pesan=Penitip berhasil dinonaktifkan!");
    } else {
        header("Location: ../admin/master_supplier.php?status=error&pesan=Gagal menonaktifkan: " . $stmt->error);
    }
    $stmt->close();
    exit();
}
?>