<?php
session_start();
header("Content-Type: application/json");
require_once "../controllers/SensorController.php"; 

$action = $_GET['action'] ?? '';

try {
    if ($action == "simulasi") {
        // Ambil ID Sensor dari JS
        $input = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($input['id_sensor'])) {
            throw new Exception("ID Sensor tidak ditemukan dalam request.");
        }

        $id_sensor = $input['id_sensor'];
        
        // ID User (System/Admin) - Default 1 jika sesi habis/cron job
        $id_user = $_SESSION['user']['id_user'] ?? 1;

        // Panggil Controller (Server yang menghitung nilai)
        $result = SensorController::simulasi($id_sensor, $id_user);
        
        echo json_encode($result);
    } 
    elseif ($action == "list") {
        $data = SensorController::getAll();
        echo json_encode(["status" => "success", "data" => $data]);
    }
} catch (Exception $e) { 
    http_response_code(500); // Kirim status error 500
    echo json_encode(["status" => "error", "message" => $e->getMessage()]); 
}
?>