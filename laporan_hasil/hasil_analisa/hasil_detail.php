<?php
session_start();
include_once("../../auth_check.php");
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: ../../login");
    exit;
}


$id_hasil = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_hasil <= 0) {
    die("ID tidak valid");
}

// hasil utama
$dataHasil = query("
    SELECT h.*, s.nama_siswa 
    FROM hasil h
    JOIN siswa s ON h.id_siswa = s.id_siswa
    WHERE h.id_hasil = $id_hasil
");

if (empty($dataHasil)) {
    die("Data tidak ditemukan");
}

$dataHasil = $dataHasil[0];

// detail
$queryDetail = query("
    SELECT hd.*, rf.keterangan 
    FROM hasil_detail hd
    JOIN rule_fuzzy rf ON hd.id_rule = rf.id_rule
    WHERE hd.id_hasil = $id_hasil
");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Detail Hasil Fuzzy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-4">

        <h3 class="mb-4">Detail Hasil Perhitungan Fuzzy Sugeno</h3>

        <!-- DATA UTAMA -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Data Hasil
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th>Nama Siswa</th>
                        <td><?= htmlspecialchars($dataHasil['nama_siswa']); ?></td>
                    </tr>
                    <tr>
                        <th>Nilai Fuzzy</th>
                        <td><strong><?= $dataHasil['nilai_fuzzy']; ?></strong></td>
                    </tr>
                    <tr>
                        <th>Keterangan</th>
                        <td>
                            <span class="badge bg-success">
                                <?= $dataHasil['keterangan']; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td><?= date('d-m-Y H:i', strtotime($dataHasil['tanggal'])); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- DETAIL PERHITUNGAN -->
        <div class="card">
            <div class="card-header bg-dark text-white">
                Detail Perhitungan (α dan Z)
            </div>
            <div class="card-body">

                <table class="table table-bordered table-striped">
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
        </div>

        <!-- BUTTON -->
        <div class="mt-3">
            <a href="hasil.php" class="btn btn-secondary">Kembali</a>
        </div>

    </div>

</body>

</html>