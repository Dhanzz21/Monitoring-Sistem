<?php
// File: api/get_dashboard_data.php
header('Content-Type: application/json');
require "../config/database.php";

try {
    // 1. Ambil 10 Data Sensor Terakhir (Untuk Grafik & Tabel)
    // Kita join dengan tbl_sensor agar mendapat nama sensornya
    $stmt = $pdo->query("
        SELECT ls.id_log_sensor, ls.pengukuran, ls.timestamp, s.nama_sensor 
        FROM tbl_log_sensor ls
        JOIN tbl_sensor s ON s.id_sensor = ls.id_sensor
        ORDER BY ls.id_log_sensor DESC 
        LIMIT 10
    ");
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Ambil Status Anomali Hari Ini (Realtime Counter)
    $stmtAnomali = $pdo->query("SELECT COUNT(*) FROM tbl_log_anomali WHERE DATE(waktu) = CURDATE()");
    $totalAnomali = $stmtAnomali->fetchColumn();

    // 3. Ambil Status Aktuator Terbaru
    $stmtKontrol = $pdo->query("
        SELECT k.id_kontrol, k.keadaan, a.nama_aktuator 
        FROM tbl_kontrol k
        JOIN tbl_aktuator a ON a.id_aktuator = k.id_aktuator
    ");
    $kontrols = $stmtKontrol->fetchAll(PDO::FETCH_ASSOC);

    // Kirim response JSON
    echo json_encode([
        "status" => "success",
        "logs" => $logs,                // Data mentah (descending)
        "logs_grafik" => array_reverse($logs), // Data dibalik (ascending) agar grafik urut dari kiri ke kanan
        "anomali_count" => $totalAnomali,
        "kontrol" => $kontrols
    ]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>