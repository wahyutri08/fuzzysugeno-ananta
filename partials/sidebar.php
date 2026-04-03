<?php
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    header('HTTP/1.1 403 Forbidden');
    include("../errors/404.html");
    exit();
}
// sidebar.php (partial) — tidak perlu logic aktif di PHP, aktif handle by JS!
$id = $_SESSION["id"];
$role = $_SESSION["role"];
$user = query("SELECT * FROM users WHERE id = $id")[0];



?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= base_url('dashboard') ?>" class="brand-link">
        <img src="<?= base_url('assets/dist/img/logo2.png') ?>" alt="Yokke" sty class="brand-image" style="opacity: .9">
        <span class="brand-text font-weight-bold ml-2 h6"> SMKS BINONG PERMAI</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image mt-2">
                <img src="<?= base_url('assets/dist/img/profile/' . htmlspecialchars($user['avatar'])) ?>" class="brand-image img-circle elevation-2" alt="User Image" style="width: 40px; height: 40px;">
            </div>
            <div class="info">
                <a href="<?= base_url('dashboard') ?>" class="d-block ">
                    <span style="font-size: 14px;"><?= htmlspecialchars($user["name"]); ?></span>
                    <h6><span style="font-size: 14px;"><?= $role; ?></span></h6>
                </a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <li class="nav-header">MENU</li>
                <li class="nav-item">
                    <a href="<?= base_url('dashboard') ?>" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-edit"></i>
                        <p>
                            Master Data
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('master_data/siswa') ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Siswa</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('master_data/penilaian') ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Penilaian</p>
                            </a>
                        </li>
                        <?php if ($role === 'Admin') : ?>
                            <li class="nav-item">
                                <a href="<?= base_url('master_data/variabel') ?>" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Variabel</p>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php if ($role === 'Admin') : ?>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>
                                Fuzzy Sugeno
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('fuzzy_sugeno/himpunan_fuzzy') ?>" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Himpunan Fuzzy</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('fuzzy_sugeno/rule_fuzzy') ?>" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Rule Fuzzy</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <li class="nav-header">LAPORAN HASIL</li>
                <li class="nav-item">
                    <a href="../hasil_fuzzy" class="nav-link">
                        <i class="nav-icon fas fa-file-invoice"></i>
                        <p>Laporan Hasil Analisa</p>
                    </a>
                </li>
                <li class="nav-header">SETTINGS</li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user"></i>
                        <p>
                            Account
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('profile') ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Profile</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('change_password') ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Change Password</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php if ($role === 'Admin') : ?>
                    <li class="nav-item">
                        <a href="<?= base_url('user_management') ?>" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>User Management</p>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a href="<?= base_url('logout') ?>" class="nav-link" id="btnLogout">
                        <i class="nav-icon fas fa-power-off"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>