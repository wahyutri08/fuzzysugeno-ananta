<?php
session_start();
include_once("../../auth_check.php");

if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    echo json_encode(["status" => "redirect"]);
    exit;
}

header('Content-Type: application/json');

$user_id = $_SESSION['id'];

$ids = $_POST['ids'] ?? [];

if (empty($ids)) {
    echo json_encode([
        "status" => "error",
        "message" => "Tidak ada data dipilih"
    ]);
    exit;
}

$total = 0;

foreach ($ids as $id_siswa) {

    $id_siswa = (int)$id_siswa;

    $data = query("
        SELECT * FROM penilaian 
        WHERE id_siswa = $id_siswa
    ");

    if (empty($data)) continue;

    $data = $data[0];

    hitungFuzzySugeno(
        $db,
        $data['nilai_uts'],
        $data['nilai_uas'],
        $data['keaktifan'],
        $id_siswa,
        $user_id
    );

    $total++;
}

echo json_encode([
    "status" => "success",
    "message" => "$total data berhasil diproses"
]);
