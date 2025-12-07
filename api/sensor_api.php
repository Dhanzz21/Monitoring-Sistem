<?php
session_start();
header("Content-Type: application/json");
require_once "../controllers/SensorController.php"; 

$action = $_GET['action'] ?? '';

try {
    if ($action == "simulasi") {
        $input = json_decode(file_get_contents("php://input"), true);
        
        // Validasi ID
        if (!isset($input['id_sensor'])) {
            throw new Exception("ID Sensor Missing");
        }

        $id_sensor = $input['id_sensor'];
        $id_user = $_SESSION['user']['id_user'] ?? 1;

        // Panggil Controller (Tanpa input nilai, server yang hitung)
        $result = SensorController::simulasi($id_sensor, $id_user);
        
        echo json_encode($result);
    } 
    elseif ($action == "list") {
        echo json_encode(["status" => "success", "data" => SensorController::getAll()]);
    }
} catch (Exception $e) { 
    http_response_code(500); // Pastikan kirim 500 jika error DB
    echo json_encode(["status" => "error", "message" => $e->getMessage()]); 
}
?>
