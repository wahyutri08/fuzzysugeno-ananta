<?php
session_start();
include_once("../auth_check.php");
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: ../login");
    exit;
}

if ($_SESSION['role'] !== 'Admin') {
    header("HTTP/1.1 404 Not Found");
    include("../errors/404.html");
    exit;
}

if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $id = $_GET["id"];
} else {
    header("HTTP/1.1 404 Not Found");
    include("../errors/404.html");
    exit;
}

$id = $_GET["id"];

if (deleteUsers($id) > 0) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error']);
}
exit;
