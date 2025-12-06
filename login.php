<?php 
// Auto Detect Base URL
$path = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$base = "/" . $path[0];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SILO Monitoring</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- PERBAIKAN CSS: Hapus '../' karena file ini ada di root -->
    <link rel="stylesheet" href="public/css/style.css">
</head>

<body class="landing-body">

    <div class="landing-card" style="max-width: 400px; padding: 40px;">

        <div style="margin-bottom: 30px;">
            <div class="landing-icon-box" style="width: 70px; height: 70px; font-size: 30px; margin-bottom: 15px;">
                <i class="fas fa-user-shield"></i>
            </div>
            <h2 style="color: var(--text-dark); margin-bottom: 5px;">Selamat Datang</h2>
            <p style="color: var(--text-grey); font-size: 14px;">Silakan login untuk mengakses dashboard.</p>
        </div>

        <!-- NOTIFIKASI ERROR -->
        <?php if(isset($_GET['error'])): ?>
        <div class="alert"
            style="background: #ffebee; color: #c62828; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 13px; text-align: left; border-left: 4px solid #c62828; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-exclamation-circle"></i>
            <div><b>Login Gagal!</b><br>Username atau Password salah.</div>
        </div>
        <?php endif; ?>

        <!-- FORM LOGIN -->
        <!-- Action ke controllers/AuthController.php (tanpa ../ karena folder controllers ada di sebelah root) -->
        <form method="POST" action="controllers/AuthController.php">

            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" required autocomplete="off">
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" name="login" class="btn-landing btn-primary-landing">
                MASUK SEKARANG
            </button>

        </form>

        <div class="auth-links">
            <!-- Link ke Register di folder views -->
            Belum punya akun? <a href="views/register.php">Daftar disini</a>
            <br><br>
            <a href="index.php" style="color: #999; font-weight: normal;">
                <i class="fas fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>

    </div>

</body>

</html>