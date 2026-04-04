<?php
session_start();
require_once("../../auth_check.php");
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: ../../login");
    exit;
}

if ($_SESSION['role'] !== 'Admin') {
    http_response_code(404);
    exit;
}

$user_id = $_SESSION['id'];
$role    = $_SESSION['role'];

$id_rule = (int)($_GET['id_rule'] ?? 0);
if ($id_rule <= 0) {
    http_response_code(404);
    exit;
}

if (isset($_GET["id_rule"]) && is_numeric($_GET["id_rule"])) {
    $id_rule = $_GET["id_rule"];
} else {
    http_response_code(404);
    exit;
}

$rule_fuzzy = query("SELECT * FROM rule_fuzzy WHERE id_rule = $id_rule");

if (empty($rule_fuzzy)) {
    http_response_code(404);
    exit;
}
$rule_fuzzy = $rule_fuzzy[0];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $result = editRule($_POST);
    if ($result > 0) {
        echo json_encode(["status" => "success", "message" => "Data Successfully Changed"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Rules Already Existed"]);
    }
    exit;
}

$title = "Edit Rule Fuzzy";
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
                                <li class="breadcrumb-item"><a href="<?= base_url('dashboard'); ?>">Home</a></li>
                                <li class="breadcrumb-item">Menu</li>
                                <li class="breadcrumb-item">Master Data</li>
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
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <!-- left column -->
                        <div class="col-md-12">
                            <!-- jquery validation -->
                            <div class="card card-warning">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-edit"></i>&nbsp;EDIT - Rule Fuzzy</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form method="POST" action="" id="quickForm">
                                    <input type="hidden" name="id_rule" value="<?= htmlspecialchars($rule_fuzzy["id_rule"]); ?>">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="uts">Ujian Tengeh Semester: <span class="text-danger">*</span></label>
                                                    <select class="form-control" name="uts" id="uts" required>
                                                        <option value="" disabled selected>-Choose One-</option>
                                                        <option value="Rendah" <?= ($rule_fuzzy["uts"] == "Rendah") ? "selected" : "" ?>>Rendah</option>
                                                        <option value="Sedang" <?= ($rule_fuzzy["uts"] == "Sedang") ? "selected" : "" ?>>Sedang</option>
                                                        <option value="Tinggi" <?= ($rule_fuzzy["uts"] == "Tinggi") ? "selected" : "" ?>>Tinggi</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="uas">Ujian Akhir Semester: <span class="text-danger">*</span></label>
                                                    <select class="form-control" name="uas" id="uas" required>
                                                        <option value="" disabled selected>-Choose One-</option>
                                                        <option value="Rendah" <?= ($rule_fuzzy["uas"] == "Rendah") ? "selected" : "" ?>>Rendah</option>
                                                        <option value="Sedang" <?= ($rule_fuzzy["uas"] == "Sedang") ? "selected" : "" ?>>Sedang</option>
                                                        <option value="Tinggi" <?= ($rule_fuzzy["uas"] == "Tinggi") ? "selected" : "" ?>>Tinggi</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="keaktifan">Keaktifan: <span class="text-danger">*</span></label>
                                                    <select class="form-control" name="keaktifan" id="keaktifan" required>
                                                        <option value="" disabled selected>-Choose One-</option>
                                                        <option value="Rendah" <?= ($rule_fuzzy["keaktifan"] == "Rendah") ? "selected" : "" ?>>Rendah</option>
                                                        <option value="Sedang" <?= ($rule_fuzzy["keaktifan"] == "Sedang") ? "selected" : "" ?>>Sedang</option>
                                                        <option value="Tinggi" <?= ($rule_fuzzy["keaktifan"] == "Tinggi") ? "selected" : "" ?>>Tinggi</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="output">Output: <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="output" id="output" value="<?= $rule_fuzzy["output"]; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="keterangan">keterangan: <span class="text-danger">*</span></label>
                                                    <select class="form-control" name="keterangan" id="keterangan" required>
                                                        <option value="" disabled selected>-Choose One-</option>
                                                        <option value="Layak" <?= ($rule_fuzzy["keterangan"] == "Layak") ? "selected" : "" ?>>Layak</option>
                                                        <option value="Dipertimbangkan" <?= ($rule_fuzzy["keterangan"] == "Dipertimbangkan") ? "selected" : "" ?>>Dipertimbangkan</option>
                                                        <option value="Tidak Layak" <?= ($rule_fuzzy["keterangan"] == "Tidak Layak") ? "selected" : "" ?>>Tidak Layak</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-check"></i> Update
                                        </button>
                                        <button type="reset" class="btn btn-dark">Reset</button>
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
                                .then(() => window.location.href = '<?= base_url('fuzzy_sugeno/rule_fuzzy') ?>');
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