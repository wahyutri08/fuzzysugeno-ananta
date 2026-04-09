<?php
session_start();
include_once("../../auth_check.php");
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: ../../login");
    exit;
}

$query = mysqli_query($db, "SELECT MAX(id_siswa) AS max_id FROM siswa");
$data = mysqli_fetch_assoc($query);
$next_id = $data['max_id'] + 1;
if (!$next_id) {
    $next_id = 1; // jika belum ada data sama sekali
}


$id = $_SESSION["id"];
$role = $_SESSION['role'];
$user = query("SELECT * FROM users WHERE id = $id")[0];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $result = addSiswa($_POST);
    if ($result > 0) {
        echo json_encode(["status" => "success", "message" => "Data Added Successfully"]);
    } elseif ($result == -1) {
        echo json_encode(["status" => "error", "message" => "NIS Sudah Ada Sebelumnya"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Data Failed to Change"]);
    }
    exit;
}

$title = "Tambah Siswa";
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
                                <li class="breadcrumb-item"><?= $title;  ?></li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <!-- left column -->
                        <div class="col-md-12">
                            <!-- jquery validation -->
                            <div class="card card-danger">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-edit"></i>&nbsp; <?= $title;  ?></h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form method="POST" action="" id="quickForm">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="id_siswa">ID Siswa:</label>
                                                    <input type="number" name="id_siswa" class="form-control" id="id_siswa" placeholder="ID Siswa" value="<?= $next_id ?>" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="nis">NIS: <span class="text-danger">*</span></label>
                                                    <input type="number" name="nis" class="form-control" id="nis" placeholder="NIS">
                                                </div>
                                                <div class="form-group">
                                                    <label for="nama_siswa">Nama Siswa: <span class="text-danger">*</span></label>
                                                    <input type="text" name="nama_siswa" class="form-control" id="nama_siswa" placeholder="Nama Siswa">
                                                </div>
                                                <div class="form-group">
                                                    <label for="kelas">Kelas: <span class="text-danger">*</span></label>
                                                    <input type="text" name="kelas" class="form-control" id="kelas" placeholder="Kelas">
                                                </div>
                                                <div class="form-group">
                                                    <label for="alamat">Alamat: <span class="text-danger">*</span></label>
                                                    <textarea class="form-control" id="alamat" name="alamat" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <div class=" col-md-6">
                                                <div class="form-group">
                                                    <label for="tanggal_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" name="tanggal_lahir" id="tanggal_lahir" placeholder="Tanggal Lahir" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Jenis Kelamin <span class="text-danger">*</span></label>
                                                    <br>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="jenis_kelamin" id="Laki-Laki" value="Laki-Laki">
                                                        <label class="form-check-label" for="Laki-Laki">Laki-laki</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="jenis_kelamin" id="Perempuan" value="Perempuan">
                                                        <label class="form-check-label" for="Perempuan">Perempuan</label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="no_telfon">No Telepon <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="no_telfon" id="no_telfon" placeholder="No Telepon" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="email">Email <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control" name="email" id="email" placeholder="Email" required>
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
            </section>
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
            bsCustomFileInput.init();
        });
    </script>
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

                if (!$(this).valid()) return;

                // 🔥 MUNCULKAN OVERLAY LANGSUNG
                $('#pageLoader').show();
                $('button[type="submit"]').prop('disabled', true);

                $.ajax({
                    url: '',
                    type: 'POST',
                    data: new FormData(this),
                    processData: false,
                    contentType: false,

                    success: function(response) {

                        $('#pageLoader').hide();
                        $('button[type="submit"]').prop('disabled', false);

                        let res;
                        try {
                            res = JSON.parse(response);
                        } catch (e) {
                            Swal.fire('Error', 'Invalid Server Response', 'error');
                            return;
                        }

                        if (res.status === 'success') {
                            Swal.fire({
                                title: "Success",
                                text: res.message,
                                icon: "success"
                            }).then(() => {
                                window.location.href = '<?= base_url('master_data/siswa') ?>';
                            });
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    },

                    error: function() {
                        $('#pageLoader').hide();
                        $('button[type="submit"]').prop('disabled', false);

                        Swal.fire('Error', 'An Error Occurred on the Server', 'error');
                    }
                });
            });
        });
    </script>
</body>

</html>