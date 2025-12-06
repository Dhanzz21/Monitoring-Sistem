<?php
session_start();
require "../config/database.php"; 

// --- LOGIN ---
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM tbl_user WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $password === $user['password']) { // Gunakan password_verify jika hash
        $_SESSION['user'] = [
            'id_user'   => $user['id_user'],
            'nama_user' => $user['nama_user'],
            'role'      => $user['role']
        ];

        // Log Login
        try {
            $log = $pdo->prepare("INSERT INTO tbl_log_user (id_user, aksi_user) VALUES (?, 'Login ke Sistem')");
            $log->execute([$user['id_user']]);
        } catch (Exception $e) {}

        header("Location: ../Dashboard.php");
        exit;
    } else {
        header("Location: ../login.php?error=1");
        exit;
    }
}

// --- REGISTER ---
if (isset($_POST['register'])) {
    $nama = $_POST['nama_user'];
    $user = $_POST['username'];
    $email = $_POST['email_user'];
    $pass = $_POST['password'];
    $role = $_POST['role'];

    $cek = $pdo->prepare("SELECT id_user FROM tbl_user WHERE username = ?");
    $cek->execute([$user]);
    if($cek->rowCount() > 0) {
        echo "<script>alert('Username sudah terdaftar!'); window.location='../views/register.php';</script>";
        exit;
    }

    $sql = "INSERT INTO tbl_user (nama_user, username, email_user, password, role) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$nama, $user, $email, $pass, $role])) {
        header("Location: ../login.php"); 
    } else {
        echo "Gagal Mendaftar.";
    }
}

// --- LOGOUT ---
if (isset($_GET['logout'])) {
    if(isset($_SESSION['user'])){
        $log = $pdo->prepare("INSERT INTO tbl_log_user (id_user, aksi_user) VALUES (?, 'Logout')");
        $log->execute([$_SESSION['user']['id_user']]);
    }
    session_destroy();
    header("Location: ../index.php");
    exit;
}
?>