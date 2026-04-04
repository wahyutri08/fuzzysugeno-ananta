<?php
session_start();
include_once("../../auth_check.php");
header('Content-Type: application/json');

// cek login
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    echo json_encode(["status" => "redirect"]);
    exit;
}

// cek role
if ($_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    // echo json_encode([
    //     "status" => "error",
    //     "message" => "Access denied"
    // ]);
    exit;
}

// hanya POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    // echo json_encode([
    //     "status" => "error",
    //     "message" => "Method not allowed"
    // ]);
    exit;
}

// validasi ID
if (!isset($_POST['id_rule']) || !is_numeric($_POST['id_rule'])) {
    http_response_code(400);
    // echo json_encode([
    //     "status" => "error",
    //     "message" => "Invalid ID"
    // ]);
    exit;
}

$id_rule = (int) $_POST['id_rule'];

// cek data ada
$cek = query("SELECT id_rule FROM rule_fuzzy WHERE id_rule = $id_rule");
if (empty($cek)) {
    http_response_code(404);
    // echo json_encode([
    //     "status" => "error",
    //     "message" => "Data not found"
    // ]);
    exit;
}

// proses delete
if (deleteRuleFuzzy($id_rule) > 0) {
    echo json_encode([
        "status" => "success",
        "message" => "Data Successfully Deleted"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Data Deletion Failed"
    ]);
}
exit;
