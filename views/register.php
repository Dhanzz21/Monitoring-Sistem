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
    <title>Register - SILO Monitoring</title>

    <!-- Font & Icon -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS Global -->
    <!-- Path CSS: Mundur satu folder (../) lalu masuk public/css -->
    <link rel="stylesheet" href="../public/css/style.css">
</head>

<body class="landing-body">

    <!-- Gunakan padding lebih besar karena formnya panjang -->
    <div class="landing-card" style="max-width: 450px; padding: 40px 30px;">

        <div style="margin-bottom: 25px;">
            <div class="landing-icon-box" style="width: 60px; height: 60px; font-size: 24px; margin-bottom: 15px;">
                <i class="fas fa-user-plus"></i>
            </div>
            <h2 style="color: var(--text-dark); margin-bottom: 5px;">Buat Akun Baru</h2>
            <p style="color: var(--text-grey); font-size: 14px;">Lengkapi data diri Anda untuk mendaftar.</p>
        </div>

        <!-- FORM MENGARAH KE AUTH CONTROLLER -->
        <!-- Path Controller: Mundur satu folder (../) lalu masuk controllers -->
        <form method="POST" action="../controllers/AuthController.php">

            <!-- Nama Lengkap -->
            <div class="input-group">
                <i class="fas fa-id-card"></i>
                <input type="text" name="nama_user" placeholder="Nama Lengkap" required autocomplete="off">
            </div>

            <!-- Username -->
            <div class="input-group">
                <i class="fas fa-user-tag"></i>
                <input type="text" name="username" placeholder="Username" required autocomplete="off">
            </div>

            <!-- Email -->
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email_user" placeholder="Email Aktif" required autocomplete="off">
            </div>

            <!-- Password -->
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <!-- Role Selection -->
            <div class="input-group select-box">
                <i class="fas fa-user-cog"></i>
                <select name="role" required>
                    <option value="" disabled selected>Pilih Role Pengguna</option>
                    <option value="Admin">Admin</option>
                    <option value="User">User</option>
                </select>
            </div>

            <!-- TOMBOL REGISTER -->
            <button type="submit" name="register" class="btn-landing btn-primary-landing" style="margin-top: 20px;">
                DAFTAR SEKARANG
            </button>

        </form>

        <div class="auth-links">
            <!-- Link ke Login (Root): Mundur satu folder (../) -->
            Sudah memiliki akun? <a href="../login.php">Login disini</a>
            <br><br>
            <!-- Link ke Login (Root): Mundur satu folder (../) -->
            <a href="../login.php" style="color: #999; font-weight: normal;">
                <i class="fas fa-arrow-left"></i> Kembali ke Login
            </a>
        </div>

    </div>

</body>

</html>