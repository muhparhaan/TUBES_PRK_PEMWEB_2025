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

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
    
    .wrapper-flex { display: flex; min-height: 100vh; width: 100%; }
    .content-wrapper { background: #f4f6f9; flex: 1; padding: 30px; min-height: 100vh; }
    
    .card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); background: white; overflow: hidden; margin-bottom: 20px; }
    .card-header { background: white; border-bottom: 1px solid #f0f0f0; padding: 20px 25px; }
    .card-title { font-weight: 700; color: #1B3C53; font-size: 1.25rem; margin: 0; }

    .table thead th { background: linear-gradient(90deg, #1B3C53, #2F5C83); color: white; border: none; font-weight: 500; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 15px; }
    .table tbody td { vertical-align: middle; padding: 15px; color: #333; border-bottom: 1px solid #f9f9f9; font-size: 0.95rem; font-weight: 500; }
    .table tbody tr:hover { background-color: #f8fbff; transform: scale(1.002); transition: all 0.2s ease; box-shadow: 0 4px 10px rgba(0,0,0,0.05); z-index: 10; position: relative; }

    .btn { border-radius: 50px; padding: 8px 20px; font-weight: 600; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.2s; }
    .btn:hover { transform: translateY(-2px); }
    .btn-primary { background: linear-gradient(45deg, #1B3C53, #4a90e2); border: none; }
    .btn-warning { background: linear-gradient(45deg, #ffb300, #ffca28); border: none; color: #333; }
    .btn-danger { background: linear-gradient(45deg, #e53935, #ef5350); border: none; }
    .badge { padding: 8px 12px; border-radius: 8px; font-weight: 600; }
    .badge-info { background: #e3f2fd; color: #1565c0; }
    .badge-warning { background: #fff8e1; color: #f57f17; }

    .modal-content { border-radius: 15px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
    .modal-header { background: linear-gradient(45deg, #1B3C53, #2F5C83); color: white; border-radius: 15px 15px 0 0; padding: 20px 30px; }
    
    .form-label {
        font-weight: 800 !important; 
        color: #000000ff !important; 
        margin-bottom: 8px;
        display: block;
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    #modalTambahUbahLabel {
    color: #242323ff !important;
    font-weight: 900;
}



    
    .form-control {
        border-radius: 8px;
        padding: 12px 15px;
        background-color: #fff;
        border: 2px solid #ccc; 
        font-size: 1rem;
        color: #000; 
        font-weight: 600;
    }
    
    .form-control:focus {
        border-color: #1B3C53; 
        box-shadow: 0 0 0 4px rgba(27, 60, 83, 0.1);
    }
    
    select.form-control {
        height: auto !important;
        padding: 12px 15px;
    }

.custom-select-icon {
    appearance: none;         
    -webkit-appearance: none;
    -moz-appearance: none;
    padding-right: 40px !important; 
    font-weight: 600;
}

.dropdown-icon {
    position: absolute;
    right: 18px;
    bottom: 14px;
    font-size: 18px;
    pointer-events: none; 
    color: #1B3C53; 
    font-weight: bold;
}

</style>

<div class="wrapper-flex">
    <?php include('../layout/sidebar.php'); ?>
    
    <div class="content-wrapper">
        <section class="content-header mb-4">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-sm-12">
                        <h1 style="color: #1B3C53; font-weight: 800; font-size: 1.8rem;">
                            <i class="fas fa-boxes mr-2"></i> Data Penitip Barang
                        </h1>
                        <p class="text-muted m-0">Kelola Data Penitip Barang (Internal & Eksternal)</p>
                    </div>
                </div>
            </div>
        </section>


        
        <section class="content">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Daftar Penitip Aktif</h3>
                    
                    <button type="button" class="btn btn-primary" 
                            data-toggle="modal" data-target="#modalTambahUbah"
                            data-bs-toggle="modal" data-bs-target="#modalTambahUbah"
                            onclick="resetModal()">
                        <i class="fas fa-plus-circle mr-1"></i> Tambah Penitip
                    </button>
                </div>
                <div class="card-body">
                    <table id="example1" class="table table-hover">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>Nama Penitip</th>
                                <th>Kontak (HP)</th>
                                <th class="text-center">Kategori</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($suppliers as $supplier): ?>
                            <tr>
                                <td class="text-center font-weight-bold" style="color: #1B3C53;"><?= $no++; ?></td>
                                <td style="font-weight: 600; font-size: 1rem; color: #000;"><?= htmlspecialchars($supplier['nama_supplier']); ?></td>
                                <td><i class="fas fa-phone-alt text-muted me-2"></i><?= htmlspecialchars($supplier['no_hp']); ?></td>
                                <td class="text-center">
                                    <?php 
                                        $badge_class = ($supplier['kategori'] == 'internal') ? 'badge-info' : 'badge-warning';
                                        $label = ($supplier['kategori'] == 'internal') ? 'Mahasiswa/Internal' : 'Vendor Luar';
                                    ?>
                                    <span class="badge <?= $badge_class ?>">
                                        <?= $label; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning btnUbah mr-1" 
                                            data-id="<?= $supplier['id_supplier']; ?>" 
                                            data-nama="<?= htmlspecialchars($supplier['nama_supplier']); ?>" 
                                            data-hp="<?= htmlspecialchars($supplier['no_hp']); ?>" 
                                            data-kategori="<?= htmlspecialchars($supplier['kategori']); ?>" 
                                            data-toggle="modal" data-target="#modalTambahUbah"
                                            data-bs-toggle="modal" data-bs-target="#modalTambahUbah"
                                            title="Edit Data">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <a href="../proses/supplier_proses.php?action=arsip&id=<?= $supplier['id_supplier']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Yakin ingin menonaktifkan?')"
                                       title="Nonaktifkan">
                                        <i class="fas fa-archive"></i>
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
                <h5 class="modal-title" id="modalTambahUbahLabel">
                    <i class="fas fa-user-plus mr-2"></i> Tambah Penitip Baru
                </h5>
                <button type="button" class="close text-dark" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="../proses/supplier_proses.php" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id_supplier" id="id_supplier">
                    <input type="hidden" name="action" id="action" value="tambah"> 

                    <div class="form-group mb-4">
                        <label for="nama_supplier" class="form-label">NAMA LENGKAP / TOKO <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_supplier" name="nama_supplier" 
                               placeholder="Masukkan nama penitip..." required>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label for="no_hp" class="form-label">NO WHATSAPP / HP <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="no_hp" name="no_hp" 
                               placeholder="Contoh: 0812xxxx" required>
                    </div>
                    
                   <div class="form-group mb-2" style="position: relative;">
                        <label for="kategori" class="form-label">KATEGORI PENITIP <span class="text-danger">*</span></label>

                        <select class="form-control custom-select-icon" id="kategori" name="kategori" required>
                        <option value="" disabled selected>-- Pilih Kategori --</option>
                        <option value="internal">Internal (Mahasiswa/Prodi)</option>
                        <option value="eksternal">Eksternal (Vendor Luar)</option>
                         </select>
                        <span class="dropdown-icon">&#9662;</span>
                </div>

                <div class="modal-footer bg-light" style="border-radius: 0 0 15px 15px; padding: 15px 25px;">
                    <button type="button" class="btn btn-secondary text-dark" style="background: #e9ecef; border:none; color: #000; font-weight: 600;" data-dismiss="modal" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4" id="btnSubmitModal">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include('../layout/footer.php'); 
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function resetModal() {
    document.getElementById("modalTambahUbahLabel").innerHTML =
        '<i class="fas fa-user-plus mr-2"></i> Tambah Penitip Baru';

    document.getElementById("btnSubmitModal").innerText = "Simpan Data";

    document.getElementById("id_supplier").value = "";
    document.getElementById("action").value = "tambah";
    document.getElementById("nama_supplier").value = "";
    document.getElementById("no_hp").value = "";
    document.getElementById("kategori").value = "";
}

document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll('.btnUbah').forEach(btn => {
        btn.addEventListener('click', function () {

            let id     = this.getAttribute("data-id");
            let nama   = this.getAttribute("data-nama");
            let hp     = this.getAttribute("data-hp");
            let kategori = this.getAttribute("data-kategori");

            document.getElementById("modalTambahUbahLabel").innerHTML =
                '<i class="fas fa-pen mr-2"></i> Edit Penitip';

            document.getElementById("btnSubmitModal").innerText = "Update Data";

            document.getElementById("id_supplier").value = id;
            document.getElementById("nama_supplier").value = nama;
            document.getElementById("no_hp").value = hp;
            document.getElementById("kategori").value = kategori;

            document.getElementById("action").value = "ubah";
        });
    });
});
</script>