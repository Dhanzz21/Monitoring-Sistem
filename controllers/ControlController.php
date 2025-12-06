<?php
require_once __DIR__ . '/../config/database.php';

class ControlController {
    public static function getAll() {
        global $pdo;
        
        $sql = "SELECT k.*, a.nama_aktuator, s.nama_sensor, s.satuan_sensor, s.ruangan,
                       p.batas_atas, p.batas_bawah, p.offset, p.skala,
               (SELECT pengukuran FROM tbl_log_sensor WHERE id_sensor = k.id_sensor ORDER BY id_log_sensor DESC LIMIT 1) as last_reading
                FROM tbl_kontrol k 
                JOIN tbl_aktuator a ON a.id_aktuator = k.id_aktuator 
                JOIN tbl_sensor s ON s.id_sensor = k.id_sensor 
                LEFT JOIN tbl_parameter p ON p.id_sensor = s.id_sensor
                ORDER BY k.id_kontrol ASC";
                
        try {
            return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public static function toggle($id, $state, $admin_id) {
        global $pdo;
        
        // 1. Update Status Kontrol
        $pdo->prepare("UPDATE tbl_kontrol SET keadaan = ?, timestamp = NOW() WHERE id_kontrol = ?")->execute([$state, $id]);
        
        // 2. FITUR BARU: Jika ON, Suntikkan Data Awal (Supaya Grafik Langsung Muncul)
        if ($state === 'ON') {
            // Ambil ID Sensor dan Jenisnya
            $stmt = $pdo->prepare("SELECT k.id_sensor, s.nama_sensor FROM tbl_kontrol k JOIN tbl_sensor s ON s.id_sensor = k.id_sensor WHERE k.id_kontrol = ?");
            $stmt->execute([$id]);
            $info = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($info && !stripos($info['nama_sensor'], 'Buzzer')) { // Jangan suntik data buat Buzzer
                $id_sensor = $info['id_sensor'];
                $isPressure = stripos($info['nama_sensor'], 'BMP') !== false || stripos($info['nama_sensor'], 'Tekanan') !== false;
                
                // Nilai Awal Aman (Agar grafik start di tengah)
                $initVal = $isPressure ? 1000 : 25;
                
                // Insert ke Log Sensor
                $pdo->prepare("INSERT INTO tbl_log_sensor (id_sensor, id_user, pengukuran) VALUES (?, ?, ?)")
                    ->execute([$id_sensor, $admin_id, $initVal]);
            }
        }

        self::log($admin_id, "Mengubah status ID $id ke $state");
        return true;
    }

    public static function addDevice($data, $admin_id) {
        global $pdo;
        $jenis = $data['jenis_sensor'];
        
        $stmt = $pdo->prepare("SELECT nama_sensor FROM tbl_sensor WHERE nama_sensor LIKE ?");
        $stmt->execute(["$jenis%"]);
        $existing = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $maxNum = 0;
        foreach ($existing as $name) {
            if (preg_match('/-(\d+)$/', $name, $matches)) {
                $num = (int)$matches[1];
                if ($num > $maxNum) $maxNum = $num;
            }
        }
        $finalName = "$jenis-" . ($maxNum + 1);
        
        $satuan = ($jenis == 'BMP') ? 'HPa' : (($jenis == 'DHT') ? 'Celcius' : '-');
        $namaAktuator = ($jenis == 'BMP') ? 'Kompresor/Valve' : (($jenis == 'DHT') ? 'Pendingin/Pemanas' : 'Alarm Sirine');

        $pdo->prepare("INSERT INTO tbl_sensor (nama_sensor, satuan_sensor, ruangan) VALUES (?, ?, ?)")->execute([$finalName, $satuan, $data['ruangan']]);
        $id_sensor = $pdo->lastInsertId();
        
        $pdo->prepare("INSERT INTO tbl_aktuator (id_sensor, nama_aktuator) VALUES (?, ?)")->execute([$id_sensor, $namaAktuator]);
        $id_aktuator = $pdo->lastInsertId();
        
        // Default OFF saat ditambah
        $pdo->prepare("INSERT INTO tbl_kontrol (id_aktuator, id_sensor, keadaan) VALUES (?, ?, 'OFF')")->execute([$id_aktuator, $id_sensor]);

        if ($jenis != 'Buzzer') {
            $sqlParam = "INSERT INTO tbl_parameter (id_user, id_sensor, batas_atas, batas_bawah, offset, skala, satuan) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $pdo->prepare($sqlParam)->execute([
                $admin_id,
                $id_sensor,
                $data['batas_atas'],
                $data['batas_bawah'],
                $data['offset'],
                $data['skala'],
                $satuan
            ]);

            // Init Data Awal (Sama seperti toggle ON)
            $initVal = ($jenis == 'BMP') ? 1000 : 25;
            $pdo->prepare("INSERT INTO tbl_log_sensor (id_sensor, id_user, pengukuran) VALUES (?, ?, ?)")->execute([$id_sensor, $admin_id, $initVal]);
        }

        self::log($admin_id, "Tambah Alat: $finalName");
        return $finalName;
    }

    public static function deleteDevice($id_kontrol, $admin_id) {
        global $pdo;
        $cek = $pdo->prepare("SELECT k.id_sensor, k.id_aktuator, s.nama_sensor FROM tbl_kontrol k JOIN tbl_sensor s ON s.id_sensor = k.id_sensor WHERE k.id_kontrol = ?");
        $cek->execute([$id_kontrol]);
        $d = $cek->fetch();

        if ($d) {
            $pdo->prepare("DELETE FROM tbl_log_sensor WHERE id_sensor = ?")->execute([$d['id_sensor']]);
            $pdo->prepare("DELETE FROM tbl_log_anomali WHERE id_sensor = ?")->execute([$d['id_sensor']]);
            $pdo->prepare("DELETE FROM tbl_parameter WHERE id_sensor = ?")->execute([$d['id_sensor']]); 
            $pdo->prepare("DELETE FROM tbl_kontrol WHERE id_kontrol = ?")->execute([$id_kontrol]);
            $pdo->prepare("DELETE FROM tbl_aktuator WHERE id_aktuator = ?")->execute([$d['id_aktuator']]);
            $pdo->prepare("DELETE FROM tbl_sensor WHERE id_sensor = ?")->execute([$d['id_sensor']]);
            
            self::log($admin_id, "Hapus Alat: " . $d['nama_sensor']);
            return true;
        }
        return false;
    }

    public static function resetSystem($admin_id) {
        global $pdo;
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        $pdo->exec("TRUNCATE TABLE tbl_log_sensor");
        $pdo->exec("TRUNCATE TABLE tbl_log_anomali");
        $pdo->exec("TRUNCATE TABLE tbl_parameter");
        $pdo->exec("TRUNCATE TABLE tbl_kontrol");
        $pdo->exec("TRUNCATE TABLE tbl_aktuator");
        $pdo->exec("TRUNCATE TABLE tbl_sensor");
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        self::log($admin_id, "RESET SYSTEM TOTAL");
        return true;
    }

    private static function log($id, $aksi) {
        global $pdo;
        try { $pdo->prepare("INSERT INTO tbl_log_user (id_user, aksi_user) VALUES (?, ?)")->execute([$id, $aksi]); } catch(Exception $e){}
    }
}
?>