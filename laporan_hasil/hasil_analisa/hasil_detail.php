<?php
session_start();
include_once("../../auth_check.php");
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: ../../login");
    exit;
}


$id_hasil = (int)($_GET['id_hasil'] ?? 0);
if ($id_hasil <= 0) {
    http_response_code(404);
    exit;
}

if (isset($_GET["id_hasil"]) && is_numeric($_GET["id_hasil"])) {
    $id_hasil = $_GET["id_hasil"];
} else {
    http_response_code(404);
    exit;
}
// hasil utama
$dataHasil = query("
    SELECT h.*, s.nama_siswa,s.nis, s.kelas, s.alamat 
    FROM hasil h
    JOIN siswa s ON h.id_siswa = s.id_siswa
    WHERE h.id_hasil = $id_hasil
");

if (empty($dataHasil)) {
    http_response_code(404);
    exit;
}
$dataHasil = $dataHasil[0];

// detail
$queryDetail = query("
    SELECT hd.*, rf.keterangan 
    FROM hasil_detail hd
    JOIN rule_fuzzy rf ON hd.id_rule = rf.id_rule
    WHERE hd.id_hasil = $id_hasil
");

$title = "{$dataHasil['nama_siswa']}";
require_once '../../partials/header.php';
?>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <?php include '../../partials/overlay.php'; ?>
    <div class="wrapper">

        <!-- Navbar -->
        <?php include '../../partials/navbar.php'; ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php include '../../partials/sidebar.php'; ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Detail Hasil Perhitungan Fuzzy Sugeno</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../dashboard">Home</a></li>
                                <li class="breadcrumb-item">Laporan Hasil</li>
                                <li class="breadcrumb-item">Hasil Analisa</li>
                                <li class="breadcrumb-item">Hasil Detail</li>
                                <li class="breadcrumb-item"><?= $title;  ?></li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <div class="card card-info">
                                <div class="card-header">
                                    <div class="card-tools">
                                        &nbsp;
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                    <h3 class="card-title"><i class="fas fa-laptop"></i>&nbsp; Informasi Data Hasil</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body p-0">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th>NIS</th>
                                                <td><?= htmlspecialchars($dataHasil['nis']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Nama Siswa</th>
                                                <td><?= htmlspecialchars($dataHasil['nama_siswa']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Kelas</th>
                                                <td><?= htmlspecialchars($dataHasil['kelas']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Alamat</th>
                                                <td><?= htmlspecialchars($dataHasil['alamat']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Nilai Fuzzy</th>
                                                <td><strong><?= $dataHasil['nilai_fuzzy']; ?></strong></td>
                                            </tr>
                                            <tr>
                                                <th>Keterangan</th>
                                                <td>
                                                    <?php if (($dataHasil["keterangan"] ?? '') === 'Layak'): ?>
                                                        <span class="badge bg-success">
                                                            <?= htmlspecialchars((string)$dataHasil["keterangan"]) ?>
                                                        </span>
                                                    <?php elseif (($dataHasil["keterangan"] ?? '') === 'Dipertimbangkan'): ?>
                                                        <span class="badge bg-info">
                                                            <?= htmlspecialchars((string)$dataHasil["keterangan"]) ?>
                                                        </span>
                                                    <?php elseif (($dataHasil["keterangan"] ?? '') === 'Tidak Layak'): ?>
                                                        <span class="badge bg-danger">
                                                            <?= htmlspecialchars((string)$dataHasil["keterangan"]) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Tanggal</th>
                                                <td><?= date('d-m-Y H:i', strtotime($dataHasil['tanggal'])); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                    <!-- Table Hasil Detail -->
                    <div class="row">
                        <div class="col">
                            <div class="card card-dark">
                                <div class="card-header">
                                    <div class="card-tools">
                                        &nbsp;
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                    <h3 class="card-title"><i class="fas fa-table"></i>&nbsp; Detail Perhitungan (α dan Z)</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body p-0 table-responsive">
                                    <table class="table table-bordered table-striped ">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th>No</th>
                                                <th>Rule</th>
                                                <th>Alpha (α)</th>
                                                <th>Z</th>
                                                <th>α * Z</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;
                                            $total_alpha = 0;
                                            $total_alpha_z = 0;
                                            foreach ($queryDetail as $row) :
                                                $alpha_z = $row['alpha'] * $row['z'];
                                                $total_alpha += $row['alpha'];
                                                $total_alpha_z += $alpha_z;
                                            ?>
                                                <tr>
                                                    <td><?= $no++; ?></td>
                                                    <td><?= $row['keterangan']; ?></td>
                                                    <td><?= number_format($row['alpha'], 4); ?></td>
                                                    <td><?= $row['z']; ?></td>
                                                    <td><?= number_format($alpha_z, 4); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="2">Total</th>
                                                <th><?= number_format($total_alpha, 4); ?></th>
                                                <th>-</th>
                                                <th><?= number_format($total_alpha_z, 4); ?></th>
                                            </tr>
                                            <tr>
                                                <th colspan="4">Hasil Akhir (Σα*z / Σα)</th>
                                                <th>
                                                    <?= $total_alpha != 0
                                                        ? number_format($total_alpha_z / $total_alpha, 4)
                                                        : 0; ?>
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </div>
            <div class="content" id="result-table">
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Main Footer -->
        <?php include '../../partials/footer.php'; ?>
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    <?php require_once '../../partials/scripts.php'; ?>

</body>

</html>