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
        </div>
    </section>
    </div>
<div class="modal fade" id="modalTambahUbah" tabindex="-1" role="dialog" aria-labelledby="modalTambahUbahLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahUbahLabel">Tambah Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="../proses/supplier_proses.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_supplier" id="id_supplier">
                    
                    <input type="hidden" name="action" id="action" value="tambah"> 

                    <div class="form-group">
                        <label for="nama_supplier">Nama Supplier</label>
                        <input type="text" class="form-control" id="nama_supplier" name="nama_supplier" required>
                    </div>
                    <div class="form-group">
                        <label for="no_hp">No HP</label>
                        <input type="text" class="form-control" id="no_hp" name="no_hp">
                    </div>
                    <div class="form-group">
                        <label for="kategori">Kategori</label>
                        <select class="form-control" id="kategori" name="kategori" required>
                            <option value="internal">Internal (Titipan Internal)</option>
                            <option value="eksternal">Eksternal (Titipan Luar)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitModal">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
include('../layout/footer.php'); 
?>
<script>
$(document).ready(function() {
    $('.btn-primary[data-target="#modalTambahUbah"]').on('click', function() {
        $('#modalTambahUbahLabel').text('Tambah Supplier');
        $('#action').val('tambah');
        $('#id_supplier').val('');
        $('#nama_supplier').val('');
        $('#no_hp').val('');
        $('#kategori').val('internal'); // Default kategori
        $('#btnSubmitModal').text('Simpan');
    });

    $('.btnUbah').on('click', function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var hp = $(this).data('hp');
        var kategori = $(this).data('kategori');

        $('#modalTambahUbahLabel').text('Ubah Supplier');
        $('#action').val('ubah');
        $('#id_supplier').val(id);
        $('#nama_supplier').val(nama);
        $('#no_hp').val(hp);
        $('#kategori').val(kategori);
        $('#btnSubmitModal').text('Ubah Data');
    });
    
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
});
</script>

<?php
include('../layout/footer.php'); 
?>