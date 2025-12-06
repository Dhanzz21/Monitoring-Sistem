<?php
require_once __DIR__ . '/../config/database.php';

class SensorController {
    
    public static function simulasi($id_sensor, $id_user) {
        global $pdo;

        // 1. Ambil Data Terakhir & Status Alat
        $stmt = $pdo->prepare("
            SELECT k.keadaan, s.nama_sensor, 
                   (SELECT pengukuran FROM tbl_log_sensor WHERE id_sensor = s.id_sensor ORDER BY timestamp DESC LIMIT 1) as last_val
            FROM tbl_kontrol k 
            JOIN tbl_sensor s ON s.id_sensor = k.id_sensor 
            WHERE k.id_sensor = ?
            ORDER BY k.id_kontrol DESC LIMIT 1
        ");
        $stmt->execute([$id_sensor]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        // Jika Sensor Mati atau Tidak Ditemukan
        if (!$data || $data['keadaan'] === 'OFF') {
            return ["status" => "skipped", "message" => "Sensor OFF or Not Found"];
        }

        $namaSensor = $data['nama_sensor'];
        
        // --- DETEKSI TIPE SENSOR ---
        $isPressure = (stripos($namaSensor, 'BMP') !== false || stripos($namaSensor, 'Tekanan') !== false);
        $isTemp = (stripos($namaSensor, 'DHT') !== false || stripos($namaSensor, 'Suhu') !== false);
        
        // Fallback jika nama tidak mengandung BMP/DHT/Tekanan/Suhu
        if (!$isPressure && !$isTemp) {
             // Default ke Suhu agar aman
             $isTemp = true;
        }
        
        // 2. Tentukan Batas Normal & Anomali
        if ($isPressure) {
            // Tekanan (hPa)
            $minSafe = 950; $maxSafe = 1100;
            $minExtreme = 850; $maxExtreme = 1200;
            $startValue = 1013; // Tekanan standar
            $decimalPoints = 0; 
            $maxStep = 3; 
        } else {
            // Suhu (Celcius)
            $minSafe = 20.0; $maxSafe = 26.0; 
            $minExtreme = 10; $maxExtreme = 40;
            $startValue = 24.0;
            $decimalPoints = 2; 
            $maxStep = 0.4;
        }

        // AMBIL NILAI TERAKHIR (PENTING: Handle NULL dengan benar)
        if ($data['last_val'] !== null) {
            $lastVal = floatval($data['last_val']);
        } else {
            $lastVal = $startValue;
        }

        // Sanity Check: Jika nilai di DB aneh (misal 0), reset
        if ($isPressure && $lastVal < 500) $lastVal = $startValue;
        if (!$isPressure && ($lastVal < -50 || $lastVal > 100)) $lastVal = $startValue;

        // 3. LOGIKA RANDOM WALK
        $isAnomaly = (rand(1, 100) <= 5); // 5% Chance

        if ($isAnomaly) {
            // ANOMALI
            if (rand(0, 1) == 1) {
                $newVal = rand($maxSafe + 2, $maxExtreme); // Loncat Tinggi
            } else {
                $newVal = rand($minExtreme, $minSafe - 2); // Loncat Rendah
            }
            // Tambah desimal acak untuk variasi suhu
            if (!$isPressure) $newVal += (rand(0, 99) / 100);

        } else {
            // NORMAL: Fluktuasi Halus
            // Random factor float antara -1.0 sampai 1.0
            $randomFactor = (mt_rand() / mt_getrandmax()) * 2 - 1; 
            $change = $randomFactor * $maxStep;
            $newVal = $lastVal + $change;

            // Bouncing: Jaga agar tetap di zona aman
            if ($newVal > $maxSafe) $newVal = $maxSafe - abs($change);
            if ($newVal < $minSafe) $newVal = $minSafe + abs($change);
        }

        $finalVal = number_format($newVal, $decimalPoints, '.', '');

        // 4. Simpan ke Database
        // Gunakan Waktu Server PHP agar konsisten
        $currentTime = date('Y-m-d H:i:s');
        
        $pdo->prepare("INSERT INTO tbl_log_sensor (id_sensor, id_user, pengukuran, timestamp) VALUES (?, ?, ?, ?)")
            ->execute([$id_sensor, $id_user, $finalVal, $currentTime]);

        // 5. Cek & Catat Anomali Log
        $pesanAnomali = null;
        if ($isPressure) {
            if ($finalVal > $maxSafe) $pesanAnomali = "Tekanan Tinggi ($finalVal hPa)";
            elseif ($finalVal < $minSafe) $pesanAnomali = "Tekanan Rendah ($finalVal hPa)";
        } else {
            if ($finalVal > $maxSafe) $pesanAnomali = "Suhu Tinggi ($finalVal °C)";
            elseif ($finalVal < $minSafe) $pesanAnomali = "Suhu Rendah ($finalVal °C)";
        }

        if ($pesanAnomali) {
            // Cek log terakhir agar tidak spam
            $lastLog = $pdo->prepare("SELECT aksi FROM tbl_log_anomali WHERE id_sensor = ? ORDER BY waktu DESC LIMIT 1");
            $lastLog->execute([$id_sensor]);
            $existingLog = $lastLog->fetchColumn();

            // Hanya catat jika pesan berbeda dari log terakhir
            if ($existingLog !== $pesanAnomali) {
                $pdo->prepare("INSERT INTO tbl_log_anomali (id_sensor, aksi, waktu) VALUES (?, ?, ?)")
                    ->execute([$id_sensor, $pesanAnomali, $currentTime]);
            }
        }

        // 6. Return Data Matang
        return [
            "status" => "success",
            "data" => [
                "nilai" => $finalVal,
                "timestamp" => date('H:i:s') // Jam saja untuk grafik
            ],
            "anomali" => $pesanAnomali ? "YES" : "NO"
        ];
    }

    public static function getAll() {
        global $pdo;
        // Gunakan ORDER BY id_log_sensor DESC agar data terbaru (ID terbesar) yang diambil
        // Ini menghindari masalah jam server yang mungkin tidak sinkron
        $sql = "SELECT ls.*, s.nama_sensor, s.satuan_sensor, u.nama_user 
                FROM tbl_log_sensor ls 
                JOIN tbl_sensor s ON s.id_sensor = ls.id_sensor 
                LEFT JOIN tbl_user u ON u.id_user = ls.id_user 
                ORDER BY ls.id_log_sensor DESC LIMIT 20";
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>