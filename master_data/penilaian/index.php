<?php
session_start();
include_once("../../auth_check.php");
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: ../../login");
    exit;
}

$user_id = $_SESSION['id'];
$role = $_SESSION['role'];

if ($role == 'Admin') {
    $siswa = query("SELECT siswa.*, penilaian.nilai_uts, penilaian.nilai_uas, penilaian.keaktifan
                    FROM siswa
                    LEFT JOIN penilaian 
                    ON siswa.id_siswa = penilaian.id_siswa");
} elseif ($role == 'Staff') {
    $siswa = query("SELECT siswa.*, penilaian.nilai_uts, penilaian.nilai_uas, penilaian.keaktifan
                    FROM siswa
                    LEFT JOIN penilaian ON siswa.id_siswa = penilaian.id_siswa
                    WHERE siswa.user_id = $user_id");
}

$title = "Penilaian Siswa";
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
                            <h1 class="m-0"><?= $title;  ?></h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../dashboard">Home</a></li>
                                <li class="breadcrumb-item">Menu</li>
                                <li class="breadcrumb-item">Master Data</li>
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
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-edit"></i>&nbsp; <?= $title;  ?></h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body table-responsive">
                                    <table id="example1" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 8px;">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input custom-control-input-danger"
                                                            type="checkbox" id="checkAll">
                                                        <label for="checkAll" class="custom-control-label"></label>
                                                    </div>
                                                </th>
                                                <th class="text-center">NIS</th>
                                                <th class="text-center">Nama Siswa</th>
                                                <th class="text-center">Nilai UTS</th>
                                                <th class="text-center">Nilai UAS</th>
                                                <th class="text-center">Keaktifan</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($siswa as $i => $row) : ?>
                                                <tr>
                                                    <td class="text-center" style="width: 8px;">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input custom-control-input-danger checkbox-item"
                                                                type="checkbox"
                                                                id="check<?= htmlspecialchars($row['id_siswa']); ?>"
                                                                value="<?= htmlspecialchars($row['id_siswa']); ?>">
                                                            <label for="check<?= htmlspecialchars($row['id_siswa']); ?>" class="custom-control-label"></label>
                                                        </div>
                                                    </td>
                                                    <td class="text-center"><?= htmlspecialchars($row['nis'] ?? '-') ?></td>
                                                    <td class="text-center"><?= htmlspecialchars($row['nama_siswa'] ?? '-') ?></td>
                                                    <td class="text-center"><?= htmlspecialchars($row['nilai_uts'] ?? '-') ?></td>
                                                    <td class="text-center"><?= htmlspecialchars($row['nilai_uas'] ?? '-') ?></td>
                                                    <td class="text-center"><?= htmlspecialchars($row['keaktifan'] ?? '-') ?></td>
                                                    <td class="text-center">
                                                        <div class="dropdown">
                                                            <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton1" data-toggle="dropdown" aria-expanded="false">
                                                                Action
                                                            </button>
                                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                                <li><a class="dropdown-item" href="<?= base_url('master_data/penilaian/edit_nilai/' . $row['id_siswa']) ?>"><i class="fas fa-edit"></i> Edit</a></li>
                                                                <li><a href="#"
                                                                        class="dropdown-item tombol-hapus"
                                                                        data-id="<?= $row['id_siswa']; ?>">
                                                                        <i class="far fa-trash-alt"></i> Delete
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
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

    <script>
        $(function() {
            $("#example1").DataTable({
                "paging": true,
                "lengthChange": true,
                "pageLength": 10,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true,
                "responsive": false,
                "buttons": ["excel", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });
    </script>
    <script>
        $(document).ready(function() {

            // CHECK ALL
            $('#checkAll').on('change', function() {
                $('.checkbox-item').prop('checked', this.checked);
                toggleDeleteButton();
            });

            // CHECK SATUAN
            $(document).on('change', '.checkbox-item', function() {
                $('#checkAll').prop(
                    'checked',
                    $('.checkbox-item:checked').length === $('.checkbox-item').length
                );
                toggleDeleteButton();
            });

            // 🔥 TOGGLE CLASS DISABLED
            function toggleDeleteButton() {
                if ($('.checkbox-item:checked').length > 0) {
                    $('#btnDelete').removeClass('disabled');
                } else {
                    $('#btnDelete').addClass('disabled');
                }
            }

            // 🗑️ CLICK DELETE (CEGAH JIKA DISABLED)
            $('#btnDelete').on('click', function(e) {
                if ($(this).hasClass('disabled')) {
                    e.preventDefault();
                    return;
                }

                e.preventDefault();

                let ids = [];
                $('.checkbox-item:checked').each(function() {
                    ids.push($(this).val());
                });

                Swal.fire({
                    title: 'Are You Sure?',
                    text: ids.length + ' Data Will be Deleted',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Delete!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'delete_color_bulk.php',
                            type: 'POST',
                            data: {
                                ids: ids
                            },

                            beforeSend: function() {
                                $('#pageLoader').show(); // 🔥 MUNCULKAN OVERLAY
                                $('#btnDelete').addClass('disabled');
                            },

                            complete: function() {
                                $('#pageLoader').hide(); // 🔥 SEMBUNYIKAN OVERLAY
                                $('#btnDelete').removeClass('disabled');
                            },

                            success: function(res) {
                                let response = JSON.parse(res);

                                if (response.status === 'success') {
                                    Swal.fire('Deleted!', response.message, 'success')
                                        .then(() => location.reload());
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },

                            error: function() {
                                Swal.fire('Error', 'Server error', 'error');
                            }
                        });
                    }
                });
            });

        });
    </script>
    <script>
        $(document).on('click', '.tombol-hapus', function(e) {
            e.preventDefault();

            let id = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "Data will be deleted",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: '<?= base_url('master_data/penilaian/delete_nilai') ?>',
                        type: 'POST',
                        data: {
                            id_siswa: id
                        },

                        beforeSend: function() {
                            $('#pageLoader').show();
                        },

                        complete: function() {
                            $('#pageLoader').hide();
                        },

                        success: function(res) {

                            if (res.status === 'success') {
                                Swal.fire('Deleted!', res.message, 'success')
                                    .then(() => location.reload());
                            } else if (res.status === 'redirect') {
                                window.location.href = '../login';
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        },

                        error: function(xhr) {
                            console.log(xhr.responseText);
                            Swal.fire('Error', 'Server Error', 'error');
                        }
                    });

                }
            });
        });
    </script>
</body>

</html>