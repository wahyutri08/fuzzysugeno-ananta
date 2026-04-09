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

if (isset($_GET["id_siswa"]) && is_numeric($_GET["id_siswa"])) {
    $id_siswa = $_GET["id_siswa"];
} else {
    http_response_code(404);
    exit;
}

if ($role == 'Admin') {
    $siswa = query("SELECT * FROM siswa WHERE id_siswa = $id_siswa");
} elseif ($role == 'Staff') {
    $siswa = query("SELECT * FROM siswa WHERE id_siswa = $id_siswa AND user_id = $user_id");
}


if (empty($siswa)) {
    http_response_code(404);
    exit;
}
$siswa = $siswa[0];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $result = editSiswa($_POST);
    if ($result > 0) {
        echo json_encode(["status" => "success", "message" => "Data Successfully Changed"]);
    } elseif ($result == -1) {
        echo json_encode(["status" => "error", "message" => "NIS Already Existed"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Data Failed to Change"]);
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
                                <li class="breadcrumb-item">Siswa</li>
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
                            <div class="card card-danger">
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
                                                    <label for="nis">NIS: <span class="text-danger">*</span></label>
                                                    <input type="text" name="nis" class="form-control" id="nis" placeholder="nis" value="<?= htmlspecialchars($siswa['nis']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="nama_siswa">Nama Siswa: <span class="text-danger">*</span></label>
                                                    <input type="text" name="nama_siswa" class="form-control" id="nama_siswa" placeholder="Nama Siswa" value="<?= htmlspecialchars($siswa['nama_siswa']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="kelas">Kelas: <span class="text-danger">*</span></label>
                                                    <input type="text" name="kelas" class="form-control" id="kelas" placeholder="Kelas" value="<?= htmlspecialchars($siswa['kelas']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="alamat">Alamat: <span class="text-danger">*</span></label>
                                                    <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= htmlspecialchars($siswa["alamat"] ?? '') ?></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tanggal_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" name="tanggal_lahir" id="tanggal_lahir" placeholder="Tanggal Lahir" value="<?= htmlspecialchars($siswa["tanggal_lahir"] ?? '') ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Jenis Kelamin <span class="text-danger">*</span></label>
                                                    <br>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="jenis_kelamin" id="LAKI-LAKI" value="LAKI-LAKI" <?= htmlspecialchars($siswa["jenis_kelamin"]) == 'LAKI-LAKI' ? 'checked' : ''; ?>>
                                                        <label class="form-check-label" for="LAKI-LAKI">LAKI-LAKI</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="jenis_kelamin" id="PEREMPUAN" value="PEREMPUAN" <?= htmlspecialchars($siswa["jenis_kelamin"]) == 'PEREMPUAN' ? 'checked' : ''; ?>>
                                                        <label class="form-check-label" for="PEREMPUAN">PEREMPUAN</label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="no_telfon">No Telepon <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="no_telfon" id="no_telfon" placeholder="No Telepon" value="<?= htmlspecialchars($siswa["no_telfon"]) ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="email">Email <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="<?= htmlspecialchars($siswa["email"]) ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-danger"><i class="fas fa-solid fa-check"></i> Submit</button>
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
                    kelas: {
                        required: true
                    },
                    alamat: {
                        required: true
                    },
                    tanggal_lahir: {
                        required: true
                    },
                    jenis_kelamin: {
                        required: true
                    },
                    no_telfon: {
                        required: true
                    },
                    email: {
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
                    kelas: {
                        required: "Please enter an Kelas"
                    },
                    alamat: {
                        required: "Please enter an Alamat"
                    },
                    tanggal_lahir: {
                        required: "Please enter an Tanggal Lahir"
                    },
                    jenis_kelamin: {
                        required: "Please enter an Jenis Kelamin"
                    },
                    no_telfon: {
                        required: "Please enter an No Telefon"
                    },
                    email: {
                        required: "Please enter an Email"
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
                                .then(() => window.location.href = '<?= base_url('master_data/siswa') ?>');
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