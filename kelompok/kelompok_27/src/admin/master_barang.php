<?php
include('../config/koneksi.php');
include('../layout/header.php');

$query = "
    SELECT 
        b.*, 
        s.nama_supplier 
    FROM barang b
    LEFT JOIN suppliers s ON b.id_supplier = s.id_supplier
    WHERE b.is_active = '1'
    ORDER BY b.id_barang DESC
";
$result = mysqli_query($conn, $query); 

$barangs = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $barangs[] = $row;
    }
}

$query_suppliers = "SELECT id_supplier, nama_supplier FROM suppliers WHERE is_active = '1' ORDER BY nama_supplier ASC";
$result_suppliers = mysqli_query($conn, $query_suppliers);
$suppliers_list = [];
if ($result_suppliers) {
    while ($row = mysqli_fetch_assoc($result_suppliers)) {
        $suppliers_list[] = $row;
    }
}


$status = $_GET['status'] ?? '';
$pesan = $_GET['pesan'] ?? '';
?>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
    .wrapper-flex { display: flex; min-height: 100vh; width: 100%; }
    .content-wrapper { background: #f4f6f9; flex: 1; padding: 30px; min-height: 100vh; }
    .card { border: none; border-radius: 15px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08); background: white; overflow: hidden; margin-bottom: 20px; }
    .card-header {  border: none; padding: 20px 25px; }
    .card-title { font-weight: 700; color: #1B3C53; font-size: 1.1rem; margin: 0; text-transform: uppercase; letter-spacing: 1px; }
    .table thead th { background: linear-gradient(90deg, #1B3C53, #2F5C83); color: white; border: none; font-weight: 500; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 15px; }
    .table tbody td { vertical-align: middle; padding: 15px; color: #444; border-bottom: 1px solid #f2f2f2; font-size: 0.95rem; font-weight: 500; }
    .table tbody tr:hover { background-color: #f1f7fd; transition: all 0.2s ease; }
    .btn { border-radius: 50px; padding: 8px 20px; font-weight: 600; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.2s; }
    .btn:hover { transform: translateY(-2px); }
    .btn-primary { background: linear-gradient(45deg, #1B3C53, #4a90e2); border: none; }
    .btn-warning { background: linear-gradient(45deg, #f1c40f, #f39c12); border: none; color: white !important; }
    .btn-danger { background: linear-gradient(45deg, #e74c3c, #c0392b); border: none; }
    .badge { padding: 8px 12px; border-radius: 30px; font-weight: 600; font-size: 0.75rem; }
    .badge-info { background: #e3f2fd; color: #1565c0; border: 1px solid #bbdefb; }
    .badge-warning { background: #fff8e1; color: #f57f17; border: 1px solid #ffecb3; }
    .badge-secondary { background: #e9ecef; color: #343a40; border: 1px solid #dee2e6; } 
    .modal-content { border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
    .modal-header { background: linear-gradient(45deg, #1B3C53, #2F5C83); border-radius: 20px 20px 0 0; padding: 25px 30px; }
    .modal-title { font-weight: 800; color: white !important; font-size: 1.25rem; }
    .close { color: black !important; opacity: 1; font-size: 1.5rem; text-shadow: none; }
    .form-label { font-weight: 700; color: #2c3e50; margin-bottom: 8px; display: block; font-size: 0.95rem; text-transform: none; }
    .form-control { border-radius: 10px !important; padding: 14px 15px !important; background-color: #f8f9fa; border: 1px solid #e0e0e0 !important; font-size: 1rem; color: #333; font-weight: 500; height: auto; }
    .form-control:focus { border-color: #1B3C53; box-shadow: 0 0 0 3px rgba(27, 60, 83, 0.1); background-color: #fff; }
    .alert {position: relative; padding-right: 45px !important;}
    .alert .close {position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none;border: none;font-size: 20px;opacity: 0.7;}

</style>

<div class="wrapper-flex">
    <?php include('../layout/sidebar.php'); ?>
    
    <div class="content-wrapper">
        <section class="content-header mb-4">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-sm-12">
                        <h1 style="color: #1B3C53; font-weight: 800; font-size: 2rem; letter-spacing: -1px;">
                            <i class="fas fa-box-open mr-2"></i> Master Data Barang Titipan
                        </h1>
                        <p class="text-muted m-0" style="font-size: 1rem;">Kelola inventori, harga, dan supplier untuk setiap barang</p>
                    </div>
                </div>
            </div>
        </section>

        <?php if ($status && $pesan): ?>
            <script>
            document.addEventListener('DOMContentLoaded', function(){
                var serverMsg = <?= json_encode($pesan); ?>;
                var serverStatus = <?= json_encode($status); ?>;

                var alertDiv = document.createElement('div');
                alertDiv.className = 'alert ' + (serverStatus === 'sukses' ? 'alert-success' : 'alert-danger') + ' alert-dismissible fade show';
                alertDiv.setAttribute('role','alert');
                alertDiv.style.borderRadius = '10px';
                alertDiv.style.margin = '0 0 20px 0';
                alertDiv.style.boxShadow = '0 6px 16px rgba(0,0,0,0.04)';
                alertDiv.style.margin = '25px';
                alertDiv.style.marginRight = '25px';


                var strong = document.createElement('strong');
                strong.innerText = serverStatus === 'sukses' ? 'Sukses: ' : 'Gagal: ';
                var text = document.createTextNode(' ' + serverMsg);
                alertDiv.appendChild(strong);
                alertDiv.appendChild(text);

                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'close';
                btn.setAttribute('data-dismiss','alert');
                btn.setAttribute('aria-label','Close');
                btn.innerHTML = '<span aria-hidden="true">&times;</span>';
                btn.onclick = function(){
                    if (alertDiv.parentNode) alertDiv.parentNode.removeChild(alertDiv);
                };
                alertDiv.appendChild(btn);

                var contentHeader = document.querySelector('.content-header');
                if (contentHeader && contentHeader.parentNode) {
                    contentHeader.parentNode.insertBefore(alertDiv, contentHeader.nextSibling);
                } else {
                    var contentWrapper = document.querySelector('.content-wrapper');
                    if (contentWrapper) contentWrapper.insertBefore(alertDiv, contentWrapper.firstChild);
                }

                setTimeout(function(){
                    if (alertDiv.parentNode) alertDiv.parentNode.removeChild(alertDiv);
                }, 3500);
            });
            </script>
        <?php endif; ?>
        
        <section class="content">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-boxes me-2" style="color: #1B3C53;"></i>
                        <h3 class="card-title">Daftar Barang Aktif</h3>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="resetModal(); showModal();">
                        <i class="fas fa-plus-circle mr-1"></i> Tambah Barang
                    </button>
                </div>
                <div class="card-body">
                    <table id="example1" class="table table-hover">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">#</th>
                                <th>Nama Barang</th>
                                <th>Harga Jual</th>
                                <th>Stok</th>
                                <th>Supplier/Vendor</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($barangs as $barang): ?>
                            <tr>
                                <td class="text-center font-weight-bold" style="color: #1B3C53;"><?= $no++; ?></td>
                                <td style="font-weight: 600; color: #2c3e50;"><?= htmlspecialchars($barang['nama_barang']); ?></td>
                                <td>Rp <?= number_format($barang['harga_jual'], 0, ',', '.'); ?></td>
                                <td><?= htmlspecialchars($barang['stok']); ?></td>
                                <td>
                                    <?php 
                                        if ($barang['id_supplier'] === NULL) {
                                            echo '<span class="badge badge-secondary"><i class="fas fa-building mr-1"></i> Koperasi (Internal)</span>';
                                        } else {
                                            echo '<span class="badge badge-warning"><i class="fas fa-truck-moving mr-1"></i> ' . htmlspecialchars($barang['nama_supplier']) . '</span>';
                                        }
                                    ?>
                                </td>
                                <td class="text-center">
                                            <button class="btn btn-warning btn-sm btnUbah mr-1" 
                                                type="button"
                                                data-id="<?= $barang['id_barang']; ?>" 
                                                data-nama="<?= htmlspecialchars($barang['nama_barang'], ENT_QUOTES); ?>" 
                                                data-jual="<?= $barang['harga_jual']; ?>" 
                                                data-stok="<?= htmlspecialchars($barang['stok']); ?>" 
                                                data-supplier="<?= $barang['id_supplier']; ?>" 
                                                title="Edit Data"
                                                onclick="handleEditClick(this)">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <a href="../proses/barang_proses.php?action=arsip&id=<?= $barang['id_barang']; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Anda yakin ingin menghapus Barang ini?')"
                                       title="Arsipkan">
                                        <i class="fas fa-trash-alt"></i>
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
</div>

<div class="modal fade" id="modalTambahUbah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahUbahLabel" style="color: white; font-weight: 800; font-size: 1.25rem;">
                    <i class="fas fa-box mr-2"></i> Tambah Barang Baru
                </h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="color: white; opacity: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formBarang" action="../proses/barang_proses.php" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id_barang" id="id_barang">
                    <input type="hidden" name="action" id="action" value="tambah"> 
                    <input type="hidden" name="ajax" value="1">

                    <div class="form-group mb-4">
                        <label for="nama_barang" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_barang" name="nama_barang" 
                            placeholder="Nama produk (contoh: Keripik Balado)" required autocomplete="off" style="border-radius: 10px;">
                    </div>

                    <div class="form-group mb-4">
                        <label for="harga_jual" class="form-label">Harga Jual (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="harga_jual" name="harga_jual" 
                            placeholder="Contoh: 15000" required autocomplete="off" min="0" style="border-radius: 10px;">
                    </div>
                    
                    <div class="form-group mb-4">
                        <label for="stok" class="form-label">Stok Awal <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="stok" name="stok" 
                            placeholder="Jumlah stok..." required autocomplete="off" min="1" style="border-radius: 10px;">
                    </div>

                    <div class="form-group mb-3">
                        <label for="id_supplier" class="form-label">Supplier / Penitip <span class="text-danger">*</span></label>
                        <select class="form-control" id="id_supplier" name="id_supplier" required style="border-radius: 10px;">
                            <option value="0" selected>-- Barang Koperasi (Internal) --</option>
                            <?php foreach ($suppliers_list as $s): ?>
                                <option value="<?= $s['id_supplier']; ?>"><?= htmlspecialchars($s['nama_supplier']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted mt-2 d-block px-1">Pilih "Barang Koperasi" jika barang bukan titipan vendor.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius: 0 0 20px 20px; padding: 20px 30px;">
                    <div class="row w-100 m-0">
                        <div class="col-6 pl-0">
                            <button type="button" class="btn btn-secondary text-dark" style="background: #e9ecef; border:none; color: #000; font-weight: 600; border-radius: 10px;" data-dismiss="modal" data-bs-dismiss="modal">
                                <i class="fas fa-times-circle mr-2"></i> Batal
                            </button>
                        </div>
                        <div class="col-6 pr-0">
                            <button type="submit" class="btn btn-primary btn-block font-weight-bold shadow-sm" id="btnSubmitModal" style="border-radius: 10px;">
                                <i class="fas fa-save mr-2"></i> Simpan Data
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include('../layout/footer.php'); 
?>

<script>
function resetModal() {
    var titleEl = document.getElementById('modalTambahUbahLabel');
    if (titleEl) titleEl.innerHTML = '<i class="fas fa-box mr-2"></i> Tambah Barang Baru';

    var actionEl = document.getElementById('action');
    if (actionEl) actionEl.value = 'tambah';

    var idEl = document.getElementById('id_barang'); if (idEl) idEl.value = '';
    var namaEl = document.getElementById('nama_barang'); if (namaEl) namaEl.value = '';
    var jualEl = document.getElementById('harga_jual'); if (jualEl) jualEl.value = '';
    var stokEl = document.getElementById('stok'); if (stokEl) stokEl.value = '';
    var supplierEl = document.getElementById('id_supplier'); if (supplierEl) supplierEl.value = '0';

    var btnEl = document.getElementById('btnSubmitModal');
    if (btnEl) btnEl.innerHTML = '<i class="fas fa-save mr-2"></i> Simpan Data';

    try { showModal(); } catch(e) { }
}

function showModal() {
    var modalEl = document.getElementById('modalTambahUbah');
    if (!modalEl) return;

    var existing = document.querySelectorAll('.modal-backdrop');
    existing.forEach(function(node){ node.parentNode.removeChild(node); });

    if (typeof window.jQuery !== 'undefined' && typeof window.jQuery.fn.modal === 'function') {
        window.jQuery('#modalTambahUbah').modal('show');
        return;
    }

    if (typeof window.bootstrap !== 'undefined' && typeof window.bootstrap.Modal === 'function') {
        var bsModal = new window.bootstrap.Modal(modalEl);
        bsModal.show();
        return;
    }

    modalEl.style.display = 'block';
    modalEl.classList.add('show');
    var backdrop = document.createElement('div');
    backdrop.className = 'modal-backdrop show';
    document.body.appendChild(backdrop);
}

function closeModal() {
    var modalEl = document.getElementById('modalTambahUbah');
    if (!modalEl) return;

    if (typeof window.jQuery !== 'undefined' && typeof window.jQuery.fn.modal === 'function') {
        window.jQuery('#modalTambahUbah').modal('hide');
        return;
    }

    if (typeof window.bootstrap !== 'undefined' && typeof window.bootstrap.Modal === 'function') {
        var bs = window.bootstrap.Modal.getInstance(modalEl);
        if (bs) bs.hide();
        return;
    }

    modalEl.classList.remove('show');
    modalEl.style.display = 'none';
    var existing = document.querySelectorAll('.modal-backdrop');
    existing.forEach(function(node){ node.parentNode.removeChild(node); });
}

function handleEditClick(el) {
    try {
        var id = el.getAttribute('data-id');
        var nama = el.getAttribute('data-nama');
        var jual = el.getAttribute('data-jual');
        var stok = el.getAttribute('data-stok');
        var supplier = el.getAttribute('data-supplier');

        console.log('handleEditClick - id:', id, 'nama:', nama);

        var title = document.getElementById('modalTambahUbahLabel');
        if (title) title.innerHTML = '<i class="fas fa-edit mr-2"></i> Edit Data Barang';

        var actionInput = document.getElementById('action');
        if (actionInput) actionInput.value = 'ubah';

        var idInput = document.getElementById('id_barang');
        if (idInput) idInput.value = id || '';

        var namaInput = document.getElementById('nama_barang');
        if (namaInput) namaInput.value = nama || '';

        var jualInput = document.getElementById('harga_jual');
        if (jualInput) jualInput.value = jual || '';

        var stokInput = document.getElementById('stok');
        if (stokInput) stokInput.value = stok || '';

        var supplierInput = document.getElementById('id_supplier');
        if (supplierInput) supplierInput.value = supplier || '0';

        var btn = document.getElementById('btnSubmitModal');
        if (btn) btn.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Perbarui Data';

        if (typeof window.jQuery !== 'undefined' && typeof window.jQuery.fn.modal === 'function') {
            window.jQuery('#modalTambahUbah').modal('show');
            return;
        }

        if (typeof window.bootstrap !== 'undefined' && typeof window.bootstrap.Modal === 'function') {
            var modalEl = document.getElementById('modalTambahUbah');
            if (modalEl) {
                var bsModal = new window.bootstrap.Modal(modalEl);
                bsModal.show();
                return;
            }
        }

        var modal = document.getElementById('modalTambahUbah');
        if (modal) {
            modal.style.display = 'block';
            modal.classList.add('show');
            var backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop show';
            document.body.appendChild(backdrop);
        }

    } catch (err) {
        console.error('handleEditClick error', err);
    }
}

$(document).ready(function() {
    $('.btn-primary[data-target="#modalTambahUbah"]').on('click', function() {
        resetModal();
    });
    
    $(document).on('click', '.btnUbah', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $btn = $(this);
        var id = $btn.data('id');
        var nama = $btn.data('nama');
        var jual = $btn.data('jual');
        var stok = $btn.data('stok');
        var supplier = $btn.data('supplier');

        console.log('Edit clicked - id:', id, 'nama:', nama); 

        document.getElementById('modalTambahUbahLabel').innerHTML = '<i class="fas fa-edit mr-2"></i> Edit Data Barang';
        $('#action').val('ubah');
        $('#id_barang').val(id);
        $('#nama_barang').val(nama);
        $('#harga_jual').val(jual);
        $('#stok').val(stok);
        $('#id_supplier').val(supplier || '0'); 
        document.getElementById('btnSubmitModal').innerHTML = '<i class="fas fa-check-circle mr-2"></i> Perbarui Data';

        if (typeof $.fn.modal === 'function') {
            $('#modalTambahUbah').modal('show');
        }
    });
    
    if ($("#example1").length) {
        $("#example1").DataTable({
          "responsive": true, 
          "lengthChange": false, 
          "autoWidth": false,
          "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
          "language": {
              "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json"
          }
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    }
    
    if ($('#status-alert').length) {
        $('#status-alert').delay(4000).fadeOut('slow', function() {
            $(this).remove();
        });
    }

    $('#formBarang').on('submit', function(e) {
        try {
            console.log('Form submit - action=', $('#action').val());
        } catch(err) {
            console.warn('Unable to read action input', err);
        }
    });
});
</script>