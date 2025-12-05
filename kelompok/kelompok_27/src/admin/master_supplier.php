<?php
include('../config/koneksi.php');
include('../layout/header.php');

$query = "SELECT id_supplier, nama_supplier, no_hp, kategori FROM suppliers WHERE is_active = '1' ORDER BY id_supplier DESC";
$result = mysqli_query($conn, $query);

$suppliers = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $suppliers[] = $row;
    }
}

$status = $_GET['status'] ?? '';
$pesan = $_GET['pesan'] ?? '';
?>

<style>
    html, body {
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
    }
    
    .content-wrapper {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        margin-left: 280px;
        min-height: 100vh;
        padding: 30px;
        width: calc(100% - 280px);
    }
    
    .content-header h1 {
        color: #1B3C53;
        font-weight: 700;
        margin-bottom: 20px;
    }
    
    .card {
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        overflow: hidden;
    }
    
    .card-header {
        background: linear-gradient(135deg, #1B3C53 0%, #2F5C83 100%);
        border: none;
        color: white;
    }
    
    .card-header .card-title {
        color: white;
        margin: 0;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #1B3C53 0%, #2F5C83 100%);
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(27, 60, 83, 0.3);
    }
    
    .table thead th {
        background: #f8f9fa;
        color: #1B3C53;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .badge-info {
        background-color: #0dcaf0;
    }
    
    .badge-warning {
        background-color: #ffc107;
        color: #333;
    }
</style>

<div class="wrapper" style="display: flex; min-height: 100vh;">
    <?php include('../layout/sidebar.php'); ?>
    
    <div class="content-wrapper" style="flex: 1; margin-left: 0; width: auto; padding: 30px;">
        <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-truck"></i> Master Data Supplier/Vendor</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Master Supplier</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <?php if ($status && $pesan): ?>
        <div class="alert alert-<?= $status == 'sukses' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($pesan); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <section class="content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Supplier Aktif</h3>
            </div>
            <div class="card-body">
                <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalTambahUbah">
                    <i class="fas fa-plus"></i> Tambah Supplier
                </button>
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Supplier</th>
                            <th>No HP</th>
                            <th>Kategori</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($suppliers as $supplier): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($supplier['nama_supplier']); ?></td>
                            <td><?= htmlspecialchars($supplier['no_hp']); ?></td>
                            <td>
                                <?php 
                                    $badge_class = ($supplier['kategori'] == 'internal') ? 'badge-info' : 'badge-warning';
                                ?>
                                <span class="badge <?= $badge_class ?>"><?= ucfirst(htmlspecialchars($supplier['kategori'])); ?></span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning btnUbah" 
                                        data-id="<?= $supplier['id_supplier']; ?>" 
                                        data-nama="<?= htmlspecialchars($supplier['nama_supplier']); ?>" 
                                        data-hp="<?= htmlspecialchars($supplier['no_hp']); ?>" 
                                        data-kategori="<?= htmlspecialchars($supplier['kategori']); ?>" 
                                        data-toggle="modal" data-target="#modalTambahUbah">
                                    <i class="fas fa-edit"></i> Ubah
                                </button>
                                <a href="../proses/supplier_proses.php?action=arsip&id=<?= $supplier['id_supplier']; ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Anda yakin ingin mengarsipkan Supplier ini?')">
                                    <i class="fas fa-archive"></i> Arsip
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    </div>
<div class="modal fade" id="modalTambahUbah" tabindex="-1" role="dialog" aria-labelledby="modalTambahUbahLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header" style="background: linear-gradient(135deg, #1B3C53 0%, #2F5C83 100%); border: none; color: white;">
                <h5 class="modal-title" id="modalTambahUbahLabel" style="color: white; font-weight: 600;">Tambah Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="../proses/supplier_proses.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_supplier" id="id_supplier">
                    <input type="hidden" name="action" id="action" value="tambah"> 

                    <div class="form-group">
                        <label for="nama_supplier" style="font-weight: 600; color: #1B3C53;"><i class="fas fa-building"></i> Nama Supplier</label>
                        <input type="text" class="form-control" id="nama_supplier" name="nama_supplier" required>
                    </div>
                    <div class="form-group">
                        <label for="no_hp" style="font-weight: 600; color: #1B3C53;"><i class="fas fa-phone"></i> No HP</label>
                        <input type="text" class="form-control" id="no_hp" name="no_hp">
                    </div>
                    <div class="form-group">
                        <label for="kategori" style="font-weight: 600; color: #1B3C53;"><i class="fas fa-tag"></i> Kategori</label>
                        <select class="form-control" id="kategori" name="kategori" required>
                            <option value="internal">Internal (Titipan Internal)</option>
                            <option value="eksternal">Eksternal (Titipan Luar)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #dee2e6;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitModal">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('.btn-primary[data-target="#modalTambahUbah"]').on('click', function() {
        $('#modalTambahUbahLabel').text('Tambah Supplier');
        $('#action').val('tambah');
        $('#id_supplier').val('');
        $('#nama_supplier').val('');
        $('#no_hp').val('');
        $('#kategori').val('internal');
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