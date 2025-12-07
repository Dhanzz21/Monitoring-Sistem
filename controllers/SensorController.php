<?php
require_once __DIR__ . '/../config/database.php';

class SensorController {
    
    public static function simulasi($id_sensor, $id_user) {
        global $pdo;

        // 1. AMBIL INFO SENSOR & PARAMETER
        // Gunakan ORDER BY id_log_sensor DESC untuk data terbaru yang absolut
        $stmt = $pdo->prepare("
            SELECT k.keadaan, s.nama_sensor, 
                   p.batas_atas, p.batas_bawah, p.offset, p.skala,
                   (SELECT pengukuran FROM tbl_log_sensor WHERE id_sensor = s.id_sensor ORDER BY id_log_sensor DESC LIMIT 1) as last_val
            FROM tbl_kontrol k 
            JOIN tbl_sensor s ON s.id_sensor = k.id_sensor 
            LEFT JOIN tbl_parameter p ON p.id_sensor = s.id_sensor
            WHERE k.id_sensor = ?
            ORDER BY k.id_kontrol DESC LIMIT 1
        ");
        $stmt->execute([$id_sensor]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data || $data['keadaan'] === 'OFF') {
            return ["status" => "skipped", "message" => "Sensor OFF"];
        }

        $namaSensor = $data['nama_sensor'];
        $isPressure = (stripos($namaSensor, 'BMP') !== false || stripos($namaSensor, 'Tekanan') !== false);
        
        // --- SETUP PARAMETER ---
        $offset = isset($data['offset']) ? floatval($data['offset']) : 0;
        $skala  = (isset($data['skala']) && floatval($data['skala']) != 0) ? floatval($data['skala']) : 1;
        
        // --- TENTUKAN BATAS & NILAI DEFAULT ---
        if ($isPressure) {
            // BMP/Tekanan
            $minSafe = ($data['batas_bawah'] > 0) ? floatval($data['batas_bawah']) : 950;
            $maxSafe = ($data['batas_atas'] > 0) ? floatval($data['batas_atas']) : 1050;
            $defaultVal = 1000; 
            $decimal = 0; $step = 4;
        } else {
            // DHT/Suhu
            $minSafe = ($data['batas_bawah'] > 0) ? floatval($data['batas_bawah']) : 20.0;
            $maxSafe = ($data['batas_atas'] > 0) ? floatval($data['batas_atas']) : 30.0;
            $defaultVal = 24.0;
            $decimal = 2; $step = 0.5;
        }

        // Ambil nilai terakhir. Jika NULL atau 0 (error), pakai Default
        $lastVal = (isset($data['last_val']) && floatval($data['last_val']) > 0) ? floatval($data['last_val']) : $defaultVal;

        // Sanity Check: Jika nilai di DB terlalu ngaco (misal 300 hPa), reset ke default
        if ($isPressure && $lastVal < 800) $lastVal = $defaultVal;
        if (!$isPressure && ($lastVal < -10 || $lastVal > 100)) $lastVal = $defaultVal;

        // --- HITUNG NILAI RAW (REVERSE CALIBRATION) ---
        // Kita butuh nilai mentah untuk simulasi fluktuasi
        $rawLast = ($lastVal / $skala) - $offset;
        $rawMinSafe = ($minSafe / $skala) - $offset;
        $rawMaxSafe = ($maxSafe / $skala) - $offset;
        
        // Sesuaikan step dengan skala
        $rawStep = $step / ($skala != 0 ? $skala : 1);

        // --- LOGIKA SIMULASI (95% Normal, 5% Anomali) ---
        $isAnomalyTurn = (rand(1, 100) <= 5); 

        if ($isAnomalyTurn) {
            // MODE ANOMALI: Lompat keluar
            if (rand(0, 1) == 1) $rawNew = $rawMaxSafe + ($rawStep * rand(5, 10)); 
            else $rawNew = $rawMinSafe - ($rawStep * rand(5, 10));
        } else {
            // MODE NORMAL: Tetap di dalam
            $isOutside = ($lastVal > $maxSafe || $lastVal < $minSafe);
            
            if ($isOutside) {
                // Recovery: Tarik cepat ke tengah
                $center = ($rawMaxSafe + $rawMinSafe) / 2;
                $direction = ($center > $rawLast) ? 1 : -1;
                $rawNew = $rawLast + ($direction * $rawStep * 3); 
            } else {
                // Normal: Fluktuasi
                $change = (mt_rand() / mt_getrandmax() * 2 - 1) * $rawStep; 
                $rawNew = $rawLast + $change;
                
                // Pantulan Batas
                if ($rawNew > $rawMaxSafe) $rawNew = $rawMaxSafe - abs($change);
                if ($rawNew < $rawMinSafe) $rawNew = $rawMinSafe + abs($change);
            }
        }

        // --- KALIBRASI ULANG UNTUK DISIMPAN ---
        $finalValCalc = ($rawNew + $offset) * $skala;
        $finalVal = number_format($finalValCalc, $decimal, '.', '');

        // 4. Simpan
        $currentTime = date('Y-m-d H:i:s');
        $pdo->prepare("INSERT INTO tbl_log_sensor (id_sensor, id_user, pengukuran, timestamp) VALUES (?, ?, ?, ?)")
            ->execute([$id_sensor, $id_user, $finalVal, $currentTime]);

        // 5. Cek & Catat Anomali Log
        $pesanAnomali = null;
        $unit = $isPressure ? "hPa" : "°C";

        if ($finalVal > $maxSafe) {
            $pesanAnomali = $isPressure ? "Tekanan Tinggi ($finalVal $unit)" : "Suhu Tinggi ($finalVal $unit)";
        } elseif ($finalVal < $minSafe) {
            $pesanAnomali = $isPressure ? "Tekanan Rendah ($finalVal $unit)" : "Suhu Rendah ($finalVal $unit)";
        }

        if ($pesanAnomali) {
            // Cek log terakhir agar tidak spam log yang sama
            $stmtLog = $pdo->prepare("SELECT aksi FROM tbl_log_anomali WHERE id_sensor = ? ORDER BY id_anomali DESC LIMIT 1");
            $stmtLog->execute([$id_sensor]);
            $lastLogMsg = $stmtLog->fetchColumn();

            if ($lastLogMsg !== $pesanAnomali) {
                $pdo->prepare("INSERT INTO tbl_log_anomali (id_sensor, aksi, waktu) VALUES (?, ?, ?)")
                    ->execute([$id_sensor, $pesanAnomali, $currentTime]);
            }
        }

        return [
            "status" => "success",
            "data" => [
                "nilai" => $finalVal,
                "timestamp" => date('H:i:s')
            ],
            "anomali" => $pesanAnomali ? "YES" : "NO"
        ];
    }

    public static function getAll() {
        global $pdo;
        // PENTING: Gunakan id_log_sensor DESC agar urutan benar meski jam ngaco
        $sql = "SELECT ls.*, s.nama_sensor, s.satuan_sensor, s.ruangan, u.nama_user 
                FROM tbl_log_sensor ls 
                JOIN tbl_sensor s ON s.id_sensor = ls.id_sensor 
                LEFT JOIN tbl_user u ON u.id_user = ls.id_user 
                ORDER BY ls.id_log_sensor DESC LIMIT 20";
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
