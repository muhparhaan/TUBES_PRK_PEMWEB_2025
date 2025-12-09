<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

$act = isset($_GET['act']) ? $_GET['act'] : '';

if ($act == 'tambah') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $role     = $_POST['role'];

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($cek) > 0) {
        header("Location: ../admin/master_user.php?status=duplicate");
        exit;
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (username, password, role) VALUES ('$username', '$password_hash', '$role')";
    
    if (mysqli_query($conn, $query)) {
        header("Location: ../admin/master_user.php?status=sukses");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

elseif ($act == 'edit') {
    $id       = $_POST['id_user'];
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role     = $_POST['role'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET username='$username', role='$role', password='$password_hash' WHERE id_user='$id'";
    } else {
        $query = "UPDATE users SET username='$username', role='$role' WHERE id_user='$id'";
    }

    if (mysqli_query($conn, $query)) {
        header("Location: ../admin/master_user.php?status=sukses");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}


elseif ($act == 'hapus') {
    $id = $_GET['id'];
    
    if ($id == $_SESSION['id_user']) {
        header("Location: ../admin/master_user.php?status=error_self");
        exit;
    }

    $query = "DELETE FROM users WHERE id_user='$id'";

    if (mysqli_query($conn, $query)) {
        header("Location: ../admin/master_user.php?status=sukses");
    } else {
        header("Location: ../admin/master_user.php?status=gagal");
    }
}
?>