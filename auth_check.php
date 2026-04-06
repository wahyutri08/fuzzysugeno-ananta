<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// blok akses langsung (versi aman)
if (!debug_backtrace()) {
    header('HTTP/1.1 403 Forbidden');
    include("errors/404.html");
    exit();
}

require_once 'functions.php';

// cek user aktif
if (!isset($_SESSION['id']) || !is_user_active($_SESSION['id'])) {
    logout();
}
