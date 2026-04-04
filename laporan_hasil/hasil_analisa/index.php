<?php
session_start();
include_once("../../auth_check.php");
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: ../../login");
    exit;
}


$query = query("
    SELECT h.*, s.nama_siswa 
    FROM hasil h
    JOIN siswa s ON h.id_siswa = s.id_siswa
    ORDER BY h.id_hasil DESC
");
?>

<div class="container mt-4">
    <h3>Data Hasil Fuzzy</h3>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Nilai</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>

            <?php $no = 1;
            foreach ($query as $row) : ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $row['nama_siswa']; ?></td>
                    <td><?= $row['nilai_fuzzy']; ?></td>
                    <td><?= $row['keterangan']; ?></td>
                    <td>
                        <a href="hasil_detail.php?id=<?= $row['id_hasil']; ?>"
                            class="btn btn-info btn-sm">
                            Detail
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>

        </tbody>
    </table>
</div>