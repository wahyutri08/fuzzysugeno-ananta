<?php
session_start();
include_once("../../auth_check.php");
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: ../../login");
    exit;
}

if ($_SESSION['role'] !== 'Admin') {
    http_response_code(404);
    exit;
}

$id = $_SESSION["id"];
$role = $_SESSION['role'];

// header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $result = addRules($_POST);
    if ($result > 0) {
        echo json_encode(["status" => "success", "message" => "Data Added Successfully"]);
    } elseif ($result == 0) {
        echo json_encode(["status" => "error", "message" => "Rules Already Existed"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Data Failed to Add"]);
    }

    exit;
}

$title = "Tambah Rule Fuzzy";
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
                                <li class="breadcrumb-item">Fuzzy Sugeno</li>
                                <li class="breadcrumb-item">Rule Fuzzy</li>
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
                            <div class="card card-warning">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-edit"></i>&nbsp; <?= $title;  ?></h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form method="POST" action="" enctype="multipart/form-data" id="quickForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="uts">Nilai Ujian Tengeh Semester: <span class="text-danger">*</span></label>
                                                    <select class="form-control" name="uts" id="uts" required>
                                                        <option value="" disabled selected>-Choose One-</option>
                                                        <option value="Rendah">Rendah</option>
                                                        <option value="Sedang">Sedang</option>
                                                        <option value="Tinggi">Tinggi</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="uas">Nilai Ujian Akhir Semester: <span class="text-danger">*</span></label>
                                                    <select class="form-control" name="uas" id="uas" required>
                                                        <option value="" disabled selected>-Choose One-</option>
                                                        <option value="Rendah">Rendah</option>
                                                        <option value="Sedang">Sedang</option>
                                                        <option value="Tinggi">Tinggi</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="keaktifan">Nilai Keaktifan: <span class="text-danger">*</span></label>
                                                    <select class="form-control" name="keaktifan" id="keaktifan" required>
                                                        <option value="" disabled selected>-Choose One-</option>
                                                        <option value="Rendah">Rendah</option>
                                                        <option value="Sedang">Sedang</option>
                                                        <option value="Tinggi">Tinggi</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="output">Output <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="output" id="output" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="keterangan">Keterangan: <span class="text-danger">*</span></label>
                                                    <select class="form-control" name="keterangan" id="keterangan" required>
                                                        <option value="" disabled selected>-Choose One-</option>
                                                        <option value="Layak">Layak</option>
                                                        <option value="Dipertimbangkan">Dipertimbangkan</option>
                                                        <option value="Tidak Layak">Tidak Layak</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-warning"><i class="fas fa-solid fa-check"></i> Submit</button>
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
                    uts: {
                        required: true
                    },
                    uas: {
                        required: true
                    },
                    keaktifan: {
                        required: true
                    },
                    output: {
                        required: true
                    },
                    keterangan: {
                        required: true
                    }
                },
                messages: {
                    uts: {
                        required: "Please enter an UTS"
                    },
                    uas: {
                        required: "Please enter an UAS"
                    },
                    keaktifan: {
                        required: "Please enter an Keaktifan"
                    },
                    output: {
                        required: "Please enter an Output"
                    },
                    keterangan: {
                        required: "Please enter an Keterangan"
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
                                window.location.href = '<?= base_url('fuzzy_sugeno/rule_fuzzy') ?>';
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