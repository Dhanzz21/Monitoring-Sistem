<?php
require_once __DIR__ . '/../config/database.php';

class UserController {
    public static function getAll() {
        global $pdo;
        return $pdo->query("SELECT * FROM tbl_user ORDER BY id_user DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($data, $admin_id) {
        global $pdo;
        $sql = "INSERT INTO tbl_user (nama_user, username, email_user, password, role) VALUES (?, ?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$data['nama_user'], $data['username'], $data['email_user'], $data['password'], $data['role']]);
        self::log($admin_id, "Menambah User: " . $data['username']);
        return true;
    }

    public static function update($data, $admin_id) {
        global $pdo;
        if (!empty($data['password'])) {
            $sql = "UPDATE tbl_user SET nama_user=?, username=?, email_user=?, role=?, password=? WHERE id_user=?";
            $pdo->prepare($sql)->execute([$data['nama_user'], $data['username'], $data['email_user'], $data['role'], $data['password'], $data['id_user']]);
        } else {
            $sql = "UPDATE tbl_user SET nama_user=?, username=?, email_user=?, role=? WHERE id_user=?";
            $pdo->prepare($sql)->execute([$data['nama_user'], $data['username'], $data['email_user'], $data['role'], $data['id_user']]);
        }
        self::log($admin_id, "Edit User: " . $data['username']);
        return true;
    }

    public static function delete($id, $admin_id) {
        global $pdo;
        // Ambil nama dulu
        $cek = $pdo->prepare("SELECT username FROM tbl_user WHERE id_user = ?");
        $cek->execute([$id]);
        $uName = $cek->fetchColumn();

        // Hapus Log Terkait (Foreign Key Fix)
        $pdo->prepare("DELETE FROM tbl_log_user WHERE id_user = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM tbl_user WHERE id_user = ?")->execute([$id]);
        
        self::log($admin_id, "Hapus User: " . $uName);
        return true;
    }

    public static function getLogs() {
        global $pdo;
        return $pdo->query("SELECT l.*, u.nama_user FROM tbl_log_user l LEFT JOIN tbl_user u ON u.id_user = l.id_user ORDER BY l.timestamp DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    }

    private static function log($id, $aksi) {
        global $pdo;
        try { $pdo->prepare("INSERT INTO tbl_log_user (id_user, aksi_user) VALUES (?, ?)")->execute([$id, $aksi]); } catch(Exception $e){}
    }
}
?>