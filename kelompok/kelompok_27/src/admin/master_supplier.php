<?php
// PASTIKAN PATH INI BENAR!
include('../layout/header.php'); 
include('../layout/sidebar.php'); 
include('../config/koneksi.php'); 

// LOGIKA READ (Mengambil data supplier aktif) - Commit 6.2
$query = "SELECT id_supplier, nama_supplier, no_hp, kategori FROM suppliers WHERE is_active = '1' ORDER BY id_supplier DESC";
$result = mysqli_query($koneksi, $query);

$suppliers = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $suppliers[] = $row;
    }
}

// Notifikasi Status (Commit 6.6)
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
        
        <?php if ($status && $pesan): ?>
            <div class="alert alert-<?= $status == 'sukses' ? 'success' : 'danger'; ?> alert-dismissible fade show mx-3" role="alert">
                <?= htmlspecialchars($pesan); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        
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
<script>
$(document).ready(function() {
    // 1. Logic untuk tombol Tambah
    $('.btn-primary[data-target="#modalTambahUbah"]').on('click', function() {
        $('#modalTambahUbahLabel').text('Tambah Supplier');
        $('#action').val('tambah');
        $('#id_supplier').val('');
        $('#nama_supplier').val('');
        $('#no_hp').val('');
        $('#kategori').val('internal');
        $('#btnSubmitModal').text('Simpan');
    });

    // 2. Logic untuk tombol Ubah (Commit 6.5)
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
    
    // 3. Inisialisasi DataTable (Commit 6.5)
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
});
</script>

<?php
include('../layout/footer.php'); 
?>