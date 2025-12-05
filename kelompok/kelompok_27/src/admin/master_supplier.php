<?php
include('../layout/header.php'); 
include('../layout/sidebar.php'); 
include('../config/koneksi.php'); 

$status = $_GET['status'] ?? '';
$pesan = $_GET['pesan'] ?? '';


?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Master Data Supplier/Vendor</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Master Supplier</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Supplier Aktif</h3>
            </div>
            <div class="card-body">
                </div>
        </div>
    </section>
    </div>
<?php
include('../layout/footer.php'); 
?>