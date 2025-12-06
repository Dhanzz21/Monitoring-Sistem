<?php
$host = "localhost";
$db = "monitoring";
$user = "root";
$pass = "";
date_default_timezone_set('Asia/Jakarta');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    die("Connection failed: " . $e->getMessage());
}
?>