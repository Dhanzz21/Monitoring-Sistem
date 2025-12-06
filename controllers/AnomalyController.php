<?php
require_once __DIR__ . '/../config/database.php';

class AnomalyController {
    public static function getAll() {
        global $pdo;
        $sql = "SELECT a.*, s.nama_sensor, s.ruangan FROM tbl_log_anomali a JOIN tbl_sensor s ON a.id_sensor = s.id_sensor ORDER BY a.waktu DESC";
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function delete($id) {
        global $pdo;
        $pdo->prepare("DELETE FROM tbl_log_anomali WHERE id_anomali = ?")->execute([$id]);
        return true;
    }
}
?>