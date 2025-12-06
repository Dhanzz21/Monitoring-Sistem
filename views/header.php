<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user'])) {
    header("Location: /proyekBD/views/login.php"); 
    exit;
}

$user_nama = $_SESSION['user']['nama_user'];
$user_role = $_SESSION['user']['role'];

$path = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$base = "/" . $path[0]; 
$current_page = strtolower(basename($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Monitoring IoT</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= $base ?>/public/css/style.css">
    <style>
    /* Animasi Lonceng Bergetar */
    @keyframes bell-shake {
        0% {
            transform: rotate(0);
        }

        15% {
            transform: rotate(15deg);
        }

        30% {
            transform: rotate(-15deg);
        }

        45% {
            transform: rotate(10deg);
        }

        60% {
            transform: rotate(-10deg);
        }

        75% {
            transform: rotate(5deg);
        }

        85% {
            transform: rotate(-5deg);
        }

        100% {
            transform: rotate(0);
        }
    }

    .alarm-active {
        /* REVISI: Mengubah warna alarm jadi MERAH (#e74c3c) */
        color: #e74c3c !important;
        animation: bell-shake 1.5s infinite;
        display: inline-block !important;
        /* Paksa muncul */
    }

    #alarm-icon {
        display: none;
        /* Default sembunyi */
        margin-right: 15px;
        font-size: 18px;
        cursor: help;
    }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <i class="fas fa-network-wired"></i> SISTEM MONITORING SENSOR
        </div>
        <ul class="nav-links">
            <!-- ALARM ICON (BARU) -->
            <li>
                <div id="alarm-icon" title="PERINGATAN: BUZZER MENYALA!">
                    <i class="fas fa-bell"></i>
                </div>
            </li>

            <li><a href="<?= $base ?>/Dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>"><i
                        class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="<?= $base ?>/views/Sensors.php"
                    class="<?= $current_page == 'sensors.php' ? 'active' : '' ?>"><i class="fas fa-microchip"></i>
                    Sensor Log</a></li>

            <?php if ($user_role === 'Admin'): ?>
            <li><a href="<?= $base ?>/api/user_api.php"
                    class="<?= $current_page == 'user_api.php' ? 'active' : '' ?>"><i class="fas fa-users-cog"></i>
                    User</a></li>
            <li><a href="<?= $base ?>/views/anomaly.php"
                    class="<?= $current_page == 'anomaly.php' ? 'active' : '' ?>"><i
                        class="fas fa-exclamation-triangle"></i> Anomali</a></li>
            <li><a href="<?= $base ?>/views/control.php"
                    class="<?= $current_page == 'control.php' ? 'active' : '' ?>"><i class="fas fa-toggle-on"></i>
                    Kontrol</a></li>
            <?php endif; ?>

            <li><a href="<?= $base ?>/views/about.php" class="<?= $current_page == 'about.php' ? 'active' : '' ?>"><i
                        class="fas fa-info-circle"></i> About</a></li>

            <li class="user-profile">
                <span class="user-name"><?= htmlspecialchars($user_nama); ?></span>
                <span class="user-role"><?= htmlspecialchars($user_role); ?></span>
            </li>
            <li><a href="<?= $base ?>/controllers/AuthController.php?logout=1" class="btn-logout-nav"><i
                        class="fas fa-sign-out-alt"></i></a></li>
        </ul>
    </nav>

    <!-- Script Global Cek Alarm -->
    <script>
    // Cek status alarm setiap 3 detik di semua halaman
    async function checkAlarm() {
        try {
            // Sesuaikan path jika header dipanggil dari folder views atau root
            // Kita gunakan absolute path relatif dari root server agar aman
            const basePath = "<?= $base ?>";
            const res = await fetch(basePath + '/api/control_api.php?action=check_alarm');
            const data = await res.json();

            const alarmIcon = document.getElementById('alarm-icon');
            if (data.status === 'success' && data.alarm_on) {
                alarmIcon.classList.add('alarm-active');
                alarmIcon.style.display = 'block';
            } else {
                alarmIcon.classList.remove('alarm-active');
                alarmIcon.style.display = 'none';
            }
        } catch (error) {
            console.error("Gagal cek alarm:", error);
        }
    }

    // Jalankan saat load & interval
    checkAlarm();
    setInterval(checkAlarm, 3000);
    </script>
</body>

</html>