<?php
// Deteksi Base URL manual karena tidak include header.php
$path = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$base = "/" . $path[0];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - Monitoring Sensor</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= $base ?>/public/css/style.css">
</head>

<body class="landing-body">

    <div class="landing-card">
        <div class="landing-icon-box">
            <i class="fas fa-network-wired"></i>
        </div>

        <h1 class="landing-title">MONITORING SENSOR</h1>
        <p class="landing-subtitle">
            Sistem pemantauan sensor IoT cerdas, real-time, dan terintegrasi untuk efisiensi kontrol perangkat Anda.
        </p>

        <a href="<?= $base ?>/login.php" class="btn-landing btn-primary-landing">
            <i class="fas fa-sign-in-alt"></i> Login Masuk
        </a>

        <a href="<?= $base ?>/views/register.php" class="btn-landing btn-outline-landing">
            <i class="fas fa-user-plus"></i> Buat Akun Baru
        </a>

        <div class="landing-footer">
            &copy; <?= date('Y'); ?> Sistem Basis Data IoT - AWI MASFUFAH
        </div>
    </div>

</body>

</html>