<?php
// Cek halaman aktif untuk highlight menu
$page = basename($_SERVER['PHP_SELF']);
// Cek role untuk menampilkan menu yang sesuai
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
?>

<div class="sidebar p-4 d-flex flex-column">
    
    <div class="brand-logo mb-4 text-center">
        <i class="fas fa-store me-2"></i> POSMA <?= ucfirst($role); ?>
    </div>
    
    <ul class="nav flex-column flex-grow-1">
        
        <?php if ($role == 'admin'): ?>
            <li class="nav-item">
                <a href="../admin/dashboard.php" class="nav-link <?= ($page == 'dashboard.php') ? 'active' : ''; ?>">
                   <i class="fas fa-home me-3 text-center" style="width: 20px;"></i> Dashboard
                </a>
            </li>
        <?php else: ?>
            <li class="nav-item">
                <a href="../staff/transaksi.php" class="nav-link <?= ($page == 'transaksi.php') ? 'active' : ''; ?>">
                   <i class="fas fa-cash-register me-3 text-center" style="width: 20px;"></i> Transaksi
                </a>
            </li>
            <li class="nav-item">
                <a href="../staff/laporan_harian.php" class="nav-link <?= ($page == 'laporan_harian.php') ? 'active' : ''; ?>">
                   <i class="fas fa-file-invoice me-3 text-center" style="width: 20px;"></i> Laporan Saya
                </a>
            </li>
        <?php endif; ?>

        <li class="menu-header">Master Data</li>
        
        <li class="nav-item">
            <a href="../admin/master_supplier.php" class="nav-link <?= ($page == 'master_supplier.php') ? 'active' : ''; ?>">
               <i class="fas fa-truck me-3 text-center" style="width: 20px;"></i> Supplier
            </a>
        </li>
        <li class="nav-item">
            <a href="../admin/master_barang.php" class="nav-link <?= ($page == 'master_barang.php') ? 'active' : ''; ?>">
               <i class="fas fa-box me-3 text-center" style="width: 20px;"></i> Barang
            </a>
        </li>

        <?php if ($role == 'admin'): ?>
            <li class="menu-header">Administrasi</li>
            
            <li class="nav-item">
                <a href="../admin/master_user.php" class="nav-link <?= ($page == 'master_user.php') ? 'active' : ''; ?>">
                   <i class="fas fa-users me-3 text-center" style="width: 20px;"></i> Staff
                </a>
            </li>
            <li class="nav-item">
                <a href="../admin/laporan_keuangan.php" class="nav-link <?= ($page == 'laporan_keuangan.php') ? 'active' : ''; ?>">
                   <i class="fas fa-chart-pie me-3 text-center" style="width: 20px;"></i> Laporan & Profit
                </a>
            </li>
        <?php endif; ?>
    </ul>

    <div class="mt-auto pt-4 border-top border-secondary">
        <a href="../logout.php" class="nav-link text-danger bg-transparent fw-bold">
            <i class="fas fa-sign-out-alt me-3 text-center" style="width: 20px;"></i> Logout
        </a>
    </div>
</div>