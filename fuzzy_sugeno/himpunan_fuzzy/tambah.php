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

$variabel = query("SELECT * FROM variabel");

$id = $_SESSION["id"];
$role = $_SESSION['role'];

// header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $result = tambahFuzzy($_POST);

    if ($result > 0) {
        echo json_encode([
            "status" => "success",
            "message" => "Fuzzy Data Successfully Added"
        ]);
    } elseif ($result == -1) {
        echo json_encode([
            "status" => "error",
            "message" => "Category Already Exists In This Variable"
        ]);
    } elseif ($result == -3) {
        echo json_encode([
            "status" => "error",
            "message" => "Data Cannot Be Empty"
        ]);
    } elseif ($result == -4) {
        echo json_encode([
            "status" => "error",
            "message" => "Wrong Format! a ≤ b ≤ c"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Data Failed To Be Added"
        ]);
    }

    exit;
}

$title = "Tambah Fuzzy";
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
                                <li class="breadcrumb-item">Himpunan Fuzzy</li>
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
                                                    <label for="id_variabel">Variabel:</label>
                                                    <select name="id_variabel" id="id_variabel" class="form-control" required>
                                                        <option value="" selected disabled>-- Selected One --</option>
                                                        <?php foreach ($variabel as $v) : ?>
                                                            <option value="<?= $v['id_variabel']; ?>">
                                                                <?= $v['nama_variabel']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="nama_set">Kategori:</label>
                                                    <select name="nama_set" id="nama_set" class="form-control">
                                                        <option value="" selected disabled>-- Selected One --</option>
                                                        <option value="Rendah">Rendah</option>
                                                        <option value="Sedang">Sedang</option>
                                                        <option value="Tinggi">Tinggi</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="a">a:</label>
                                                    <input type="text" name="a" class="form-control" id="a" placeholder="a">
                                                </div>
                                                <div class="form-group">
                                                    <label for="b">b:</label>
                                                    <input type="text" name="b" class="form-control" id="b" placeholder="c">
                                                </div>
                                                <div class="form-group">
                                                    <label for="c">c:</label>
                                                    <input type="text" name="c" class="form-control" id="c" placeholder="c">
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
                    id_variabel: {
                        required: true
                    },
                    nama_variabel: {
                        required: true
                    }
                },
                messages: {
                    id_variabel: {
                        required: "Please enter an ID Variabel"
                    },
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
                                window.location.href = '<?= base_url('fuzzy_sugeno/himpunan_fuzzy') ?>';
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