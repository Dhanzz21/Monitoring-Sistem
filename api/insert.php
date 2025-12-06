<?php
require "../config/database.php";
date_default_timezone_set('Asia/Jakarta');

// Tangkap input
$id_sensor = $_POST['id_sensor'] ?? null;
$id_user   = $_POST['id_user'] ?? null;
$pengukuran = $_POST['pengukuran'] ?? null;

// Validasi simple
if (!$id_sensor || !$id_user || !$pengukuran) {
    echo json_encode(["status" => "error", "msg" => "Input tidak lengkap"]);
    exit;
}

$timestamp = date('Y-m-d H:i:s');  // timestamp akurat WIB

// Insert
$stmt = $pdo->prepare("
    INSERT INTO tbl_log_sensor (id_sensor, id_user, pengukuran, created_at)
    VALUES (:id_sensor, :id_user, :pengukuran, :created_at)
");

$stmt->execute([
    ":id_sensor"   => $id_sensor,
    ":id_user"     => $id_user,
    ":pengukuran"  => $pengukuran,
    ":created_at"  => $timestamp
]);

echo json_encode([
    "status" => "success",
    "saved_at" => $timestamp
]);