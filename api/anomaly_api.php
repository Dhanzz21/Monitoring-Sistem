<?php
session_start();
header("Content-Type: application/json");
require_once "../controllers/AnomalyController.php";

$action = $_GET['action'] ?? '';

try {
    if ($action == 'list') {
        echo json_encode(AnomalyController::getAll());
    } elseif ($action == 'delete') {
        if ($_SESSION['user']['role'] !== 'Admin') die(json_encode(["status"=>"error", "message"=>"Akses Ditolak"]));
        AnomalyController::delete($_GET['id']);
        echo json_encode(["status" => "success"]);
    }
} catch (Exception $e) { echo json_encode(["status" => "error", "message" => $e->getMessage()]); }
?>