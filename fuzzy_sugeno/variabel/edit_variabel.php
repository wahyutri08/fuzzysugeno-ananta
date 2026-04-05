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

$id_variabel = (int)($_GET['id_variabel'] ?? 0);
if ($id_variabel <= 0) {
    http_response_code(404);
    exit;
}

if (isset($_GET["id_variabel"]) && is_numeric($_GET["id_variabel"])) {
    $id_variabel = $_GET["id_variabel"];
} else {
    http_response_code(404);
    exit;
}

$variabel = query("SELECT * FROM variabel WHERE id_variabel = $id_variabel");

if (empty($variabel)) {
    http_response_code(404);
    exit;
}
$variabel = $variabel[0];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $result = editVariabel($_POST);
    if ($result > 0) {
        echo json_encode(["status" => "success", "message" => "Data Added Successfully"]);
    } elseif ($result == -1) {
        echo json_encode(["status" => "error", "message" => "Variabel Name Already Exists"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Data Failed to Change"]);
    }
    exit;
}

$title = "{$variabel['nama_variabel']}";
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
                                <li class="breadcrumb-item">Fuzzy Sugeno</li>
                                <li class="breadcrumb-item">Variabel</li>
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
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-edit"></i>&nbsp;EDIT - <?= $variabel["nama_variabel"];  ?></h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form method="POST" action="" id="quickForm">
                                    <input type="hidden" id="id_variabel" name="id_variabel" value="<?= htmlspecialchars($variabel["id_variabel"]); ?>">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nama_variabel">Nama Variabel:</label>
                                                    <input type="text" name="nama_variabel" class="form-control" id="nama_variabel" placeholder="Nama Variabel" value="<?= htmlspecialchars($variabel['nama_variabel']); ?>">
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
                    nama_variabel: {
                        required: true
                    }
                },
                messages: {
                    nama_variabel: {
                        required: "Please enter an Nama Variabel"
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
                                .then(() => window.location.href = '<?= base_url('fuzzy_sugeno/variabel') ?>');
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