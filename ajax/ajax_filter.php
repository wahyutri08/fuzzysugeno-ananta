<?php
// Hanya boleh via AJAX
if (
    !isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'
) {
    http_response_code(403);
    exit;
}

// Hanya boleh method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

session_start();
require_once("../auth_check.php");

if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: ../login");
    exit;
}

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* =============================
   AMBIL DATA FILTER
============================= */

$where = [];

$keyword     = mysqli_real_escape_string($db, $_POST['search'] ?? '');
$tanggal = $_POST['tanggal'] ?? '';
$id_siswa   = $_POST['id_siswa'] ?? 'all';
$user_id     = $_POST['user_id'] ?? 'all';
$keterangan   = $_POST['keterangan'] ?? 'all';


/* =============================
   FILTER
============================= */

if ($tanggal != '') {
    $start = $tanggal . " 00:00:00";
    $end   = $tanggal . " 23:59:59";
    $where[] = "hasil.tanggal BETWEEN '$start' AND '$end'";
}

if ($id_siswa != 'all')
    $where[] = "hasil.id_siswa = '$id_siswa'";

if ($user_id != 'all')
    $where[] = "hasil.user_id = '$user_id'";

if ($keterangan != 'all')
    $where[] = "hasil.keterangan = '$keterangan'";


/* =============================
   KEYWORD SEARCH
============================= */

if ($keyword != '') {
    $where[] = "(
        siswa.nama_siswa LIKE '%$keyword%' OR
        siswa.nis LIKE '%$keyword%' OR
        hasil.keterangan LIKE '%$keyword%' OR
        hasil.tanggal LIKE '%$keyword%' 
    )";
}

$whereSQL = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

/* =============================
   QUERY
============================= */

$query = "SELECT
          hasil.*,
          users.name,
          users.role,
          siswa.nama_siswa,
          siswa.nis
          FROM hasil
          LEFT JOIN users 
          ON hasil.user_id = users.id
          LEFT JOIN siswa 
          ON hasil.id_siswa = siswa.id_siswa
          $whereSQL
          ORDER BY hasil.id_hasil DESC";

/* =============================
   EKSEKUSI QUERY
============================= */

$result = mysqli_query($db, $query);

if (!$result) {
    echo json_encode([
        'status' => 'error',
        'message' => mysqli_error($db)
    ]);
    exit;
}

$data = mysqli_fetch_all($result, MYSQLI_ASSOC);

if (!$data) {
    echo json_encode(['status' => 'empty']);
    exit;
}


/* =============================
   BUILD HTML OUTPUT
============================= */

ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Result Search</h3>
                </div>
                <div class="card-body table-responsive">
                    <table id="example1" class="table table-bordered table-hover">
                        <thead>
                            <tr class="text-center">
                                <th><input type="checkbox" id="checkAll"></th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Nilai</th>
                                <th>Keterangan</th>
                                <th>Tanggal Proses</th>
                                <th>Nama Admin / Staff</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $row): ?>
                                <tr class="text-center">
                                    <td>
                                        <input type="checkbox" class="check-item" value="<?= $row['id_hasil']; ?>">
                                    </td>
                                    <td><?= htmlspecialchars((string)($row["nis"] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($row["nama_siswa"] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($row["nilai_fuzzy"] ?? '')) ?></td>
                                    <td>
                                        <?php if (($row["keterangan"] ?? '') === 'Layak'): ?>
                                            <span class="badge bg-success">
                                                <?= htmlspecialchars((string)$row["keterangan"]) ?>
                                            </span>
                                        <?php elseif (($row["keterangan"] ?? '') === 'Dipertimbangkan'): ?>
                                            <span class="badge bg-info">
                                                <?= htmlspecialchars((string)$row["keterangan"]) ?>
                                            </span>
                                        <?php elseif (($row["keterangan"] ?? '') === 'Tidak Layak'): ?>
                                            <span class="badge bg-danger">
                                                <?= htmlspecialchars((string)$row["keterangan"]) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars((string)($row["tanggal"] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($row["name"] ?? '')) ?> (<?= htmlspecialchars((string)($row["role"] ?? '')) ?>)</td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn dropdown-toggle" type="button" data-toggle="dropdown">
                                                Action
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="<?= base_url('laporan_hasil/hasil_analisa/hasil_detail/' . $row['id_hasil']) ?>">
                                                        <i class="fas fa-eye"></i> Hasil Detail
                                                    </a>
                                                </li>
                                                <?php if ($_SESSION['role'] === 'Admin'): ?>
                                                    <li>
                                                        <button class="dropdown-item tombol-hapus" data-id="<?= $row['id_hasil']; ?>">
                                                            <i class="far fa-trash-alt"></i> Delete
                                                        </button>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$html = ob_get_clean();

echo json_encode([
    'status' => 'success',
    'html'   => $html
]);
