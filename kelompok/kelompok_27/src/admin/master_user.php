<?php
include('../config/koneksi.php');
include('../layout/header.php');

$query = "SELECT * FROM users ORDER BY role ASC, username ASC";
$result = mysqli_query($conn, $query);

$users = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
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
    .card-header { border: none; padding: 20px 25px; }
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
    .modal-content { border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
    .modal-header { background: linear-gradient(45deg, #1B3C53, #2F5C83); border-radius: 20px 20px 0 0; padding: 25px 30px; }
    .modal-title { font-weight: 800; color: white !important; font-size: 1.25rem; }
    .close { color: white !important; opacity: 1; font-size: 1.5rem; text-shadow: none; background: transparent; border: none;}
    
    .form-label { font-weight: 700; color: #2c3e50; margin-bottom: 8px; display: block; font-size: 0.95rem; }
    .form-control, .form-select { border-radius: 10px !important; padding: 12px 15px !important; background-color: #f8f9fa; border: 1px solid #e0e0e0 !important; font-size: 1rem; color: #333; font-weight: 500; }
    .form-control:focus, .form-select:focus { border-color: #1B3C53; box-shadow: 0 0 0 3px rgba(27, 60, 83, 0.1); background-color: #fff; }
</style>

<div class="wrapper-flex">
    <?php include('../layout/sidebar.php'); ?>
    
    <div class="content-wrapper">
        <section class="content-header mb-4">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-sm-12">
                        <h1 style="color: #1B3C53; font-weight: 800; font-size: 2rem; letter-spacing: -1px;">
                            <i class="fas fa-users-cog mr-2"></i> Manajemen User
                        </h1>
                        <p class="text-muted m-0" style="font-size: 1rem;">Kelola akun Administrator dan Staff/Kasir</p>
                    </div>
                </div>
            </div>
        </section>

        <?php if ($status && $pesan): ?>
            <div class="alert alert-<?php echo ($status == 'sukses') ? 'success' : 'danger'; ?> alert-dismissible fade show mx-4" role="alert" style="border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                <strong><?php echo ($status == 'sukses') ? 'Sukses:' : 'Gagal:'; ?></strong> <?php echo $pesan; ?>
                <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close" style="color: #000 !important; top: 0;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        
        <section class="content">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-list-alt me-2" style="color: #1B3C53;"></i>
                        <h3 class="card-title">Daftar Pengguna Aktif</h3>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="resetModal(); showModal();">
                        <i class="fas fa-user-plus mr-1"></i> Tambah User
                    </button>
                </div>
                <div class="card-body">
                    <table id="example1" class="table table-hover">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">#</th>
                                <th>Username</th>
                                <th>Role / Hak Akses</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($users as $u): ?>
                            <tr>
                                <td class="text-center font-weight-bold" style="color: #1B3C53;"><?= $no++; ?></td>
                                <td style="font-weight: 600; color: #2c3e50;"><?= htmlspecialchars($u['username']); ?></td>
                                <td>
                                    <?php if ($u['role'] == 'admin'): ?>
                                        <span class="badge" style="background: linear-gradient(45deg, #e74c3c, #c0392b); color: white;">
                                            <i class="fas fa-user-shield mr-1"></i> Administrator
                                        </span>
                                    <?php else: ?>
                                        <span class="badge" style="background: linear-gradient(45deg, #3498db, #2980b9); color: white;">
                                            <i class="fas fa-cash-register mr-1"></i> Staff / Kasir
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm btnUbah mr-1" 
                                            type="button"
                                            data-id="<?= $u['id_user']; ?>" 
                                            data-username="<?= htmlspecialchars($u['username']); ?>" 
                                            data-role="<?= $u['role']; ?>" 
                                            title="Edit User"
                                            onclick="handleEditClick(this)">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    
                                    <?php if($u['id_user'] != $_SESSION['id_user']): ?>
                                        <a href="../proses/user_proses.php?action=hapus&id=<?= $u['id_user']; ?>" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Yakin ingin menghapus user <?= $u['username']; ?>? Akses akan hilang permanen.')"
                                           title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    <?php endif; ?>
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
                    <i class="fas fa-user-plus mr-2"></i> Tambah User Baru
                </h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formUser" action="../proses/user_proses.php" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id_user" id="id_user">
                    <input type="hidden" name="action" id="action" value="tambah"> 

                    <div class="form-group mb-4">
                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="username" name="username" 
                            placeholder="Contoh: kasir_pagi" required autocomplete="off">
                    </div>

                    <div class="form-group mb-4">
                        <label for="role" class="form-label">Role / Hak Akses <span class="text-danger">*</span></label>
                        <select class="form-control form-select" id="role" name="role" required>
                            <option value="staff" selected>Staff / Kasir</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" 
                            placeholder="Masukkan password..." autocomplete="new-password">
                        <small class="text-muted mt-2 d-block px-1" id="passwordHelp">
                            <i class="fas fa-info-circle mr-1"></i> Untuk user baru wajib diisi.
                        </small>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius: 0 0 20px 20px; padding: 20px 30px;">
                    <div class="row w-100 m-0">
                        <div class="col-6 pl-0">
                            <button type="button" class="btn btn-secondary text-dark w-100" style="background: #e9ecef; border:none; color: #000; font-weight: 600; border-radius: 10px;" data-dismiss="modal" data-bs-dismiss="modal">
                                <i class="fas fa-times-circle mr-2"></i> Batal
                            </button>
                        </div>
                        <div class="col-6 pr-0">
                            <button type="submit" class="btn btn-primary btn-block font-weight-bold shadow-sm w-100" id="btnSubmitModal" style="border-radius: 10px;">
                                <i class="fas fa-save mr-2"></i> Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('../layout/footer.php'); ?>

<script>
function resetModal() {
    document.getElementById('modalTambahUbahLabel').innerHTML = '<i class="fas fa-user-plus mr-2"></i> Tambah User Baru';
    document.getElementById('action').value = 'tambah';
    document.getElementById('id_user').value = '';
    document.getElementById('username').value = '';
    document.getElementById('password').value = ''; 
    document.getElementById('password').required = true; 
    document.getElementById('role').value = 'staff';
    document.getElementById('passwordHelp').innerHTML = '<i class="fas fa-info-circle mr-1"></i> Wajib diisi untuk user baru.';
    document.getElementById('btnSubmitModal').innerHTML = '<i class="fas fa-save mr-2"></i> Simpan User';
}

function showModal() {
    var modalEl = document.getElementById('modalTambahUbah');
    if (typeof window.bootstrap !== 'undefined' && typeof window.bootstrap.Modal === 'function') {
        var bsModal = new window.bootstrap.Modal(modalEl);
        bsModal.show();
    } else {
        $(modalEl).modal('show');
    }
}

function handleEditClick(el) {
    var id = el.getAttribute('data-id');
    var username = el.getAttribute('data-username');
    var role = el.getAttribute('data-role');

    document.getElementById('modalTambahUbahLabel').innerHTML = '<i class="fas fa-edit mr-2"></i> Edit User';
    document.getElementById('action').value = 'ubah';
    document.getElementById('id_user').value = id;
    document.getElementById('username').value = username;
    document.getElementById('role').value = role;
    
    var passInput = document.getElementById('password');
    passInput.value = '';
    passInput.required = false; 
    passInput.placeholder = "(Biarkan kosong jika tidak diganti)";
    
    document.getElementById('passwordHelp').innerHTML = '<i class="fas fa-exclamation-triangle mr-1 text-warning"></i> Kosongkan jika password tidak ingin diubah.';
    document.getElementById('btnSubmitModal').innerHTML = '<i class="fas fa-check-circle mr-2"></i> Update User';

    showModal();
}
</script>