<?php
session_start();
require_once("../../auth_check.php");
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: ../../login");
    exit;
}

$user_id = $_SESSION['id'];
$role    = $_SESSION['role'];

$id_siswa = (int)($_GET['id_siswa'] ?? 0);
if ($id_siswa <= 0) {
    http_response_code(404);
    exit;
}

if ($role == 'Admin') {
    $siswa = query("
        SELECT siswa.*, penilaian.nilai_uts, penilaian.nilai_uas, penilaian.keaktifan
        FROM siswa
        LEFT JOIN penilaian 
        ON siswa.id_siswa = penilaian.id_siswa
        WHERE siswa.id_siswa = $id_siswa
        LIMIT 1
    ");
} elseif ($role == 'Staff') {
    $siswa = query("
        SELECT siswa.*, penilaian.nilai_uts, penilaian.nilai_uas, penilaian.keaktifan
        FROM siswa
        LEFT JOIN penilaian 
        ON siswa.id_siswa = penilaian.id_siswa
        WHERE siswa.id_siswa = $id_siswa
        AND siswa.user_id = $user_id
        LIMIT 1
    ");
}


if (empty($siswa)) {
    http_response_code(404);
    exit;
}
$siswa = $siswa[0];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $result = editNilai($_POST);
    if ($result > 0) {
        echo json_encode(["status" => "success", "message" => "Data Berhasil Diubah"]);
    } elseif ($result == -1) {
        echo json_encode(["status" => "error", "message" => "NIS Sudah Ada Sebelumnya"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Data Gagal Diubah"]);
    }
    exit;
}

$title = "{$siswa['nama_siswa']}";
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
                                <li class="breadcrumb-item">Penilaian</li>
                                <li class="breadcrumb-item">Edit</li>
                                <li class="breadcrumb-item"><?= $title; ?></li>
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
                        <!-- left column -->
                        <div class="col-md-12">
                            <!-- jquery validation -->
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-edit"></i>&nbsp; <?= $siswa["nama_siswa"];  ?></h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form method="POST" action="" id="quickForm">
                                    <input type="hidden" id="id_siswa" name="id_siswa" value="<?= htmlspecialchars($siswa["id_siswa"]); ?>">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nis">NIS:</label>
                                                    <input type="text" name="nis" class="form-control" id="nis" placeholder="nis" value="<?= htmlspecialchars($siswa['nis']); ?>" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="nama_siswa">Nama Siswa:</label>
                                                    <input type="text" name="nama_siswa" class="form-control" id="nama_siswa" placeholder="Nama Siswa" value="<?= htmlspecialchars($siswa['nama_siswa']); ?>" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="nilai_uts">Nilai UTS:</label>
                                                    <input type="number" name="nilai_uts" class="form-control" id="nilai_uts" placeholder="Nilai UTS" value="<?= htmlspecialchars($siswa['nilai_uts']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="nilai_uas">Nilai UAS:</label>
                                                    <input type="number" name="nilai_uas" class="form-control" id="nilai_uas" placeholder="Nilai UAS" value="<?= htmlspecialchars($siswa['nilai_uas']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="keaktifan">Keaktifan:</label>
                                                    <input type="number" name="keaktifan" class="form-control" id="keaktifan" placeholder="Keaktifan" value="<?= htmlspecialchars($siswa['keaktifan']); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-solid fa-check"></i> Submit</button>
                                        <button type="reset" class="btn btn-dark"> Reset</button>
                                    </div>
                                </form>
                            </div>
                            <!-- /.card -->
                        </div>
                        <!--/.col (left) -->
                        <!-- right column -->
                        <div class="col-md-6">

                        </div>
                        <!--/.col (right) -->
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
            // Inisialisasi validasi jQuery
            $('#quickForm').validate({
                rules: {
                    nis: {
                        required: true
                    },
                    nama_siswa: {
                        required: true
                    },
                    nilai_uts: {
                        required: true
                    },
                    nilai_uas: {
                        required: true
                    },
                    keaktifan: {
                        required: true
                    }
                },
                messages: {
                    nis: {
                        required: "Please enter an NIS"
                    },
                    nama_siswa: {
                        required: "Please enter an Nama_siswa"
                    },
                    nilai_uts: {
                        required: "Please enter an Nilai UTS"
                    },
                    nilai_uas: {
                        required: "Please enter an Nilai UAS"
                    },
                    keaktifan: {
                        required: "Please enter an Keaktifan"
                    },
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                }
            });

            // Submit dengan AJAX hanya jika valid
            $('#quickForm').on('submit', function(e) {
                e.preventDefault();

                if (!$(this).valid()) return; // Stop jika form tidak valid

                // 🔥 MUNCULKAN OVERLAY LANGSUNG
                $('#pageLoader').show();
                $('button[type="submit"]').prop('disabled', true);

                $.ajax({
                    url: '',
                    type: 'POST',
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    dataType: 'json', // 🔥 PENTING
                    success: function(res) {
                        $('#pageLoader').hide();
                        $('button[type="submit"]').prop('disabled', false);

                        if (res.status === 'success') {
                            Swal.fire('Success', res.message, 'success')
                                .then(() => window.location.href = '<?= base_url('master_data/penilaian') ?>');
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        $('#pageLoader').hide();
                        $('button[type="submit"]').prop('disabled', false);
                        console.log(xhr.responseText); // 🔥 DEBUG
                        Swal.fire('Error', 'Server Error', 'error');
                    }
                });
            });
        });
    </script>
</body>

</html>