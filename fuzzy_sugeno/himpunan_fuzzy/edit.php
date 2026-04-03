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

$id_set = (int)($_GET['id_set'] ?? 0);
if ($id_set <= 0) {
    http_response_code(404);
    exit;
}

if (isset($_GET["id_set"]) && is_numeric($_GET["id_set"])) {
    $id_set = $_GET["id_set"];
} else {
    http_response_code(404);
    exit;
}

$fuzzy_set = query("SELECT * FROM fuzzy_set WHERE id_set = $id_set");

if (empty($fuzzy_set)) {
    http_response_code(404);
    exit;
}
$fuzzy_set = $fuzzy_set[0];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $result = updateFuzzy($_POST);

    if ($result > 0) {
        echo json_encode(["status" => "success", "message" => "Fuzzy Data Successfully Updated"]);
    } elseif ($result == -1) {
        echo json_encode(["status" => "error", "message" => "Category Already Exists In This Variable"]);
    } elseif ($result == -3) {
        echo json_encode(["status" => "error", "message" => "Data Cannot Be Empty"]);
    } elseif ($result == -4) {
        echo json_encode(["status" => "error", "message" => "Wrong Format! a ≤ b ≤ c"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Data Failed To Be Added"]);
    }
    exit;
}

$title = "{$fuzzy_set['nama_set']}";
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
                                    <h3 class="card-title"><i class="fas fa-edit"></i>&nbsp;EDIT - <?= $fuzzy_set["nama_set"];  ?></h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form method="POST" action="" id="quickForm">
                                    <input type="hidden" name="id_set" value="<?= htmlspecialchars($fuzzy_set["id_set"]); ?>">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">

                                                <div class="form-group">
                                                    <label for="id_variabel">Variabel:</label>
                                                    <select name="id_variabel" id="id_variabel" class="form-control" required>
                                                        <option value="" selected disabled>-- Selected One --</option>
                                                        <?php $variabel = query("SELECT * FROM variabel"); ?>
                                                        <?php foreach ($variabel as $v): ?>
                                                            <option value="<?= $v['id_variabel']; ?>"
                                                                <?= $v['id_variabel'] == $fuzzy_set['id_variabel'] ? 'selected' : ''; ?>>
                                                                <?= $v['nama_variabel']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="nama_set">Kategori:</label>
                                                    <select name="nama_set" id="nama_set" class="form-control">
                                                        <option value="" selected disabled>-- Selected One --</option>
                                                        <option <?= $fuzzy_set['nama_set'] == 'Rendah' ? 'selected' : ''; ?>>Rendah</option>
                                                        <option <?= $fuzzy_set['nama_set'] == 'Sedang' ? 'selected' : ''; ?>>Sedang</option>
                                                        <option <?= $fuzzy_set['nama_set'] == 'Tinggi' ? 'selected' : ''; ?>>Tinggi</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="a">a:</label>
                                                    <input type="number" name="a" id="a" class="form-control" value="<?= $fuzzy_set['a']; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="b">b:</label>
                                                    <input type="number" name="b" id="b" class="form-control" value="<?= $fuzzy_set['b']; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="c">c:</label>
                                                    <input type="number" name="c" id="c" class="form-control" value="<?= $fuzzy_set['c']; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary">
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
                    a: {
                        required: true,
                        number: true
                    },
                    b: {
                        required: true,
                        number: true
                    },
                    c: {
                        required: true,
                        number: true
                    }
                },
                messages: {
                    a: "Masukkan nilai a",
                    b: "Masukkan nilai b",
                    c: "Masukkan nilai c"
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
                                .then(() => window.location.href = '<?= base_url('fuzzy_sugeno/himpunan_fuzzy') ?>');
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