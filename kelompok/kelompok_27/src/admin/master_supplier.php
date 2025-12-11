<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$koneksi_path = __DIR__ . '/../config/koneksi.php';
$header_path = __DIR__ . '/../layout/header.php';
$sidebar_path = __DIR__ . '/../layout/sidebar.php';
$footer_path = __DIR__ . '/../layout/footer.php';

if (!file_exists($koneksi_path) || !file_exists($header_path)) {
    die("Error: File koneksi atau header tidak ditemukan!");
}

include($koneksi_path);
include($header_path);

$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'admin';


$query = "SELECT id_supplier, nama_supplier, no_hp, kategori 
          FROM suppliers 
          WHERE is_active = '1' 
          ORDER BY id_supplier DESC";
$result = mysqli_query($conn, $query);

$suppliers = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $suppliers[] = $row;
    }
} else {
    die("Error query: " . mysqli_error($conn));
}

$status = isset($_GET['status']) ? $_GET['status'] : '';
$pesan  = isset($_GET['pesan']) ? $_GET['pesan'] : '';
?>


<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
.wrapper-flex { display: flex; min-height: 100vh; width: 100%; }
.content-wrapper { background: #f4f6f9; flex: 1; padding: 30px; min-height: 100vh; }
.card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white; overflow: hidden; margin-bottom: 20px; }
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
.form-label { font-weight: 800 !important; color: #000 !important; margin-bottom: 8px; display: block; font-size: 1rem; text-transform: uppercase; letter-spacing: 0.5px; }
#modalTambahUbahLabel { color: #242323ff !important; font-weight: 900; }
.form-control { border-radius: 8px; padding: 12px 15px; background-color: #fff; border: 2px solid #ccc; font-size: 1rem; color: #000; font-weight: 600; }
.form-control:focus { border-color: #1B3C53; box-shadow: 0 0 0 4px rgba(27, 60, 83, 0.1); }
select.form-control { height: auto !important; padding: 12px 15px; }
.custom-select-icon { appearance: none; padding-right: 40px !important; font-weight: 600; }
.dropdown-icon { position: absolute; right: 18px; bottom: 14px; font-size: 18px; pointer-events: none; color: #1B3C53; font-weight: bold; }
</style>

<div class="wrapper-flex">
<?php
if (!file_exists($sidebar_path)) {
    die("Error: Sidebar tidak ditemukan!");
}
include($sidebar_path);
?>

<div class="content-wrapper">
    <section class="content-header mb-4">
        <div class="container-fluid">
            <h1 style="color: #1B3C53; font-weight: 800; font-size: 1.8rem;">
                <i class="fas fa-boxes mr-2"></i> Data Penitip Barang
            </h1>
            <p class="text-muted m-0">Kelola Data Penitip Barang (Internal & Eksternal)</p>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Daftar Penitip Aktif</h3>

                <?php if ($role === 'admin'): ?>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalTambahUbah" onclick="resetModal()">
                    <i class="fas fa-plus-circle mr-1"></i> Tambah Penitip
                </button>
                <?php endif; ?>
            </div>

            <div class="card-body">
                <table id="example1" class="table table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama Penitip</th>
                            <th>Kontak (HP)</th>
                            <th class="text-center">Kategori</th>
                            <?php if ($role === 'admin'): ?>
                            <th class="text-center">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($suppliers as $s): ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td><?= htmlspecialchars($s['nama_supplier']); ?></td>
                            <td><?= htmlspecialchars($s['no_hp']); ?></td>
                            <td class="text-center">
                                <?php
                                $badge = ($s['kategori'] === 'internal') ? 'badge-info' : 'badge-warning';
                                $label = ($s['kategori'] === 'internal') ? 'Mahasiswa/Internal' : 'Eksternal';
                                ?>
                                <span class="badge <?= $badge ?>"><?= $label ?></span>
                            </td>

                            <?php if ($role === 'admin'): ?>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-warning btnUbah"
                                    data-id="<?= $s['id_supplier']; ?>"
                                    data-nama="<?= htmlspecialchars($s['nama_supplier']); ?>"
                                    data-hp="<?= htmlspecialchars($s['no_hp']); ?>"
                                    data-kategori="<?= htmlspecialchars($s['kategori']); ?>"
                                    data-toggle="modal"
                                    data-target="#modalTambahUbah">
                                    <i class="fas fa-pen"></i>
                                </button>

                                <a href="../proses/supplier_proses.php?action=arsip&id=<?= $s['id_supplier']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menonaktifkan?')">
                                    <i class="fas fa-archive"></i>
                                </a>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
</div>

<?php if ($role === 'admin'): ?>
<div class="modal fade" id="modalTambahUbah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahUbahLabel">
                    <i class="fas fa-user-plus mr-2"></i> Tambah Penitip Baru
                </h5>
                <button type="button" class="close text-dark" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form action="../proses/supplier_proses.php" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" id="id_supplier" name="id_supplier">
                    <input type="hidden" id="action" name="action" value="tambah">

                    <div class="form-group mb-4">
                        <label class="form-label">NAMA LENGKAP / TOKO *</label>
                        <input type="text" class="form-control" id="nama_supplier" name="nama_supplier" required>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">NO WHATSAPP / HP *</label>
                        <input type="number" class="form-control" id="no_hp" name="no_hp" required>
                    </div>

                    <div class="form-group mb-2" style="position: relative;">
                        <label class="form-label">KATEGORI PENITIP *</label>
                        <select class="form-control custom-select-icon" id="kategori" name="kategori" required>
                            <option value="" disabled selected>-- Pilih Kategori --</option>
                            <option value="internal">Internal</option>
                            <option value="eksternal">Eksternal</option>
                        </select>
                        <span class="dropdown-icon">&#9662;</span>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitModal">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
if (!file_exists($footer_path)) {
    die("Error: Footer tidak ditemukan!");
}
include($footer_path);
?>

<script>
function resetModal() {
    document.getElementById("modalTambahUbahLabel").innerHTML = '<i class="fas fa-user-plus mr-2"></i> Tambah Penitip Baru';
    document.getElementById("btnSubmitModal").innerText = "Simpan Data";
    document.getElementById("id_supplier").value = "";
    document.getElementById("nama_supplier").value = "";
    document.getElementById("no_hp").value = "";
    document.getElementById("kategori").value = "";
    document.getElementById("action").value = "tambah";
}

document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btnUbah');
    if (btn) {
        document.getElementById("modalTambahUbahLabel").innerHTML = '<i class="fas fa-pen mr-2"></i> Edit Penitip';
        document.getElementById("btnSubmitModal").innerText = "Update Data";

        const id = btn.dataset.id;
        const nama = btn.dataset.nama;
        const hp = btn.dataset.hp;
        const kategori = btn.dataset.kategori;

        document.getElementById("id_supplier").value = id;
        document.getElementById("nama_supplier").value = nama;
        document.getElementById("no_hp").value = hp;
        document.getElementById("kategori").value = kategori.trim().toLowerCase();
        document.getElementById("action").value = "ubah";
    }
});
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
