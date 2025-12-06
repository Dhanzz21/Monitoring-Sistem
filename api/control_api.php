<?php
session_start();
header("Content-Type: application/json");
require_once "../controllers/ControlController.php";

$action = $_GET['action'] ?? '';
$isAdmin = isset($_SESSION['user']) && $_SESSION['user']['role'] === 'Admin';
$admin_id = $_SESSION['user']['id_user'] ?? 1;

try {
    if ($action == 'list') {
        echo json_encode(ControlController::getAll());
    } 
    // ... (check_alarm dan toggle tetap sama) ...
    elseif ($action == 'check_alarm') {
        require_once "../config/database.php"; 
        $sql = "SELECT COUNT(*) FROM tbl_kontrol k JOIN tbl_sensor s ON s.id_sensor = k.id_sensor WHERE s.nama_sensor LIKE 'Buzzer%' AND k.keadaan = 'ON'";
        $count = $pdo->query($sql)->fetchColumn();
        echo json_encode(["status" => "success", "alarm_on" => ($count > 0)]);
    }
    elseif ($action == 'toggle') {
        if (!$isAdmin) die(json_encode(["status"=>"error", "message"=>"Hanya Admin"]));
        $input = json_decode(file_get_contents("php://input"), true);
        ControlController::toggle($input['id'], $input['state'], $admin_id);
        echo json_encode(["status" => "success"]);
    } 
    
    // --- REVISI ADD DEVICE (TERIMA PARAMETER) ---
    elseif ($action == 'add_device') {
        if (!$isAdmin) die(json_encode(["status"=>"error", "message"=>"Akses Ditolak"]));
        $input = json_decode(file_get_contents("php://input"), true);
        
        // Validasi input parameter (default 0 atau 1 jika kosong)
        $input['batas_atas'] = $input['batas_atas'] ?? 0;
        $input['batas_bawah'] = $input['batas_bawah'] ?? 0;
        $input['offset'] = $input['offset'] ?? 0;
        $input['skala'] = $input['skala'] ?? 1;

        $name = ControlController::addDevice($input, $admin_id);
        echo json_encode(["status" => "success", "message" => "Ditambahkan: $name"]);
    } 
    
    // ... (delete & reset tetap sama) ...
    elseif ($action == 'delete_device') {
        if (!$isAdmin) die(json_encode(["status"=>"error", "message"=>"Akses Ditolak"]));
        ControlController::deleteDevice($_GET['id'], $admin_id);
        echo json_encode(["status" => "success", "message" => "Dihapus"]);
    } elseif ($action == 'reset_system') {
        if (!$isAdmin) die(json_encode(["status"=>"error", "message"=>"Akses Ditolak"]));
        ControlController::resetSystem($admin_id);
        echo json_encode(["status" => "success", "message" => "System Reset!"]);
    }
} catch (Exception $e) { echo json_encode(["status" => "error", "message" => $e->getMessage()]); }
?>