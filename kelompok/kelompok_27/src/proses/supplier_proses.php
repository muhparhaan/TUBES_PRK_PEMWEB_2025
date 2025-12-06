<?php
include('../config/koneksi.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action        = $_POST['action'] ?? '';
    $nama_supplier = $_POST['nama_supplier'] ?? '';
    $no_hp         = $_POST['no_hp'] ?? '';
    $kategori      = $_POST['kategori'] ?? '';
    $id_supplier   = intval($_POST['id_supplier'] ?? 0);

    if ($action === 'tambah') {

        $stmt = $conn->prepare("INSERT INTO suppliers (nama_supplier, no_hp, kategori) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nama_supplier, $no_hp, $kategori);

        if ($stmt->execute()) {
            header("Location: ../admin/master_supplier.php?status=sukses&pesan=Data penitip berhasil ditambahkan!");
        } else {
            header("Location: ../admin/master_supplier.php?status=error&pesan=Gagal menambah data: " . $stmt->error);
        }

        $stmt->close();
        exit;

    } elseif ($action === 'ubah') {

        $stmt = $conn->prepare("UPDATE suppliers SET nama_supplier=?, no_hp=?, kategori=? WHERE id_supplier=?");
        $stmt->bind_param("sssi", $nama_supplier, $no_hp, $kategori, $id_supplier);

        if ($stmt->execute()) {
            header("Location: ../admin/master_supplier.php?status=sukses&pesan=Data penitip berhasil diperbarui!");
        } else {
            header("Location: ../admin/master_supplier.php?status=error&pesan=Gagal memperbarui data: " . $stmt->error);
        }

        $stmt->close();
        exit;
    }
}


if (isset($_GET['action']) && $_GET['action'] === 'arsip' && isset($_GET['id'])) {

    $id = intval($_GET['id']);

    $stmt = $conn->prepare("UPDATE suppliers SET is_active='0' WHERE id_supplier=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: ../admin/master_supplier.php?status=sukses&pesan=Penitip berhasil dinonaktifkan!");
    } else {
        header("Location: ../admin/master_supplier.php?status=error&pesan=Gagal menonaktifkan: " . $stmt->error);
    }

    exit;
}

?>
