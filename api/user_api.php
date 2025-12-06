<?php
session_start();
require "../config/database.php"; 

// --- PROTEKSI ADMIN ONLY ---
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    if(!isset($_GET['action'])) { include "../views/header.php"; echo "<div class='container'><h2 style='color:red'>Akses Ditolak!</h2></div>"; include "../views/footer.php"; exit; }
    echo json_encode(["status" => "error", "message" => "Akses Ditolak."]); exit;
}

if (isset($_GET['action'])) {
    header("Content-Type: application/json");
    $action = $_GET['action'];
    $admin_id = $_SESSION['user']['id_user']; 

    try {
        switch ($action) {
            case "list":
                $stmt = $pdo->query("SELECT * FROM tbl_user ORDER BY id_user DESC");
                echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
                break;

            case "save":
                $input = json_decode(file_get_contents("php://input"), true);
                $nama = $input['nama_user']; $user = $input['username']; $email = $input['email_user'];
                $role = $input['role']; $pass = $input['password']; $id = $input['id_user'] ?? null;

                if ($id) {
                    if (!empty($pass)) {
                        $sql = "UPDATE tbl_user SET nama_user=?, username=?, email_user=?, role=?, password=? WHERE id_user=?";
                        $pdo->prepare($sql)->execute([$nama, $user, $email, $role, $pass, $id]);
                    } else {
                        $sql = "UPDATE tbl_user SET nama_user=?, username=?, email_user=?, role=? WHERE id_user=?";
                        $pdo->prepare($sql)->execute([$nama, $user, $email, $role, $id]);
                    }
                    catatLog($pdo, $admin_id, "Edit User: $user");
                    echo json_encode(["status" => "success", "message" => "User diupdate"]);
                } else {
                    $sql = "INSERT INTO tbl_user (nama_user, username, email_user, password, role) VALUES (?, ?, ?, ?, ?)";
                    $pdo->prepare($sql)->execute([$nama, $user, $email, $pass, $role]);
                    catatLog($pdo, $admin_id, "Tambah User: $user");
                    echo json_encode(["status" => "success", "message" => "User ditambah"]);
                }
                break;

            case "delete":
                $id = $_GET['id'];
                $cek = $pdo->prepare("SELECT username FROM tbl_user WHERE id_user = ?");
                $cek->execute([$id]);
                $target = $cek->fetch();
                
                if($target) {
                    $uName = $target['username'];
                    // Hapus log user terkait dulu (Foreign Key Fix)
                    $pdo->prepare("DELETE FROM tbl_log_user WHERE id_user = ?")->execute([$id]);
                    // Hapus usernya
                    $pdo->prepare("DELETE FROM tbl_user WHERE id_user = ?")->execute([$id]);
                    
                    catatLog($pdo, $admin_id, "Hapus User: $uName");
                    echo json_encode(["status" => "success", "message" => "User dihapus"]);
                }
                break;

            case "log_list":
                $sql = "SELECT l.*, u.nama_user FROM tbl_log_user l LEFT JOIN tbl_user u ON u.id_user = l.id_user ORDER BY l.timestamp DESC LIMIT 10";
                echo json_encode($pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC));
                break;
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
    exit;
}

function catatLog($pdo, $id, $aksi) {
    $pdo->prepare("INSERT INTO tbl_log_user (id_user, aksi_user) VALUES (?, ?)")->execute([$id, $aksi]);
}

include "../views/header.php"; 
?>
<!-- TAMPILAN USER MANAGEMENT -->
<div class="main-wrapper">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="section-title"><i class="fas fa-users-cog"></i> Manajemen User</h2>
            <button onclick="openModal()" class="btn-action btn-add"><i class="fas fa-plus"></i> Tambah User</button>
        </div>
        <hr>
        <div class="table-responsive">
            <table class="table-data">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="userTableBody"></tbody>
            </table>
        </div>
        <br>
        <h3 class="section-title" style="font-size:18px;"><i class="fas fa-history"></i> Log Aktivitas</h3>
        <div class="table-responsive">
            <table class="table-data">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Pelaku</th>
                        <th>Aktivitas</th>
                    </tr>
                </thead>
                <tbody id="logTableBody"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <span onclick="closeModal()" class="close-btn">&times;</span>
        <h3 id="modalTitle">Tambah User</h3>
        <form id="userForm"><input type="hidden" id="id_user">
            <div class="form-group"><label>Nama</label><input type="text" id="nama_user" required></div>
            <div class="form-group"><label>Username</label><input type="text" id="username" required></div>
            <div class="form-group"><label>Email</label><input type="email" id="email_user" required></div>
            <div class="form-group"><label>Role</label><select id="role">
                    <option>Admin</option>
                    <option>User</option>
                </select></div>
            <div class="form-group"><label>Password</label><input type="password" id="password"
                    placeholder="Isi password baru"></div>
            <button type="submit" class="btn-submit">Simpan</button>
        </form>
    </div>
</div>
<?php include "../views/footer.php"; ?>

<script>
const API_URL = 'user_api.php';
async function loadData() {
    let res = await fetch(API_URL + '?action=list');
    let users = await res.json();
    let rows = users.map(u =>
        `<tr><td>#${u.id_user}</td><td>${u.nama_user}</td><td>${u.username}</td><td>${u.email_user}</td><td><span class="badge ${u.role=='Admin'?'badge-user':'badge-sensor'}">${u.role}</span></td><td><button class="btn-action btn-edit" onclick='edit(${JSON.stringify(u)})'>Edit</button><button class="btn-action btn-delete" onclick="del(${u.id_user})">Hapus</button></td></tr>`
    ).join('');
    document.getElementById('userTableBody').innerHTML = rows || '<tr><td colspan="6">Kosong</td></tr>';

    let resLog = await fetch(API_URL + '?action=log_list');
    let logs = await resLog.json();
    let logRows = logs.map(l =>
        `<tr><td>${l.timestamp}</td><td><span class="badge badge-user">${l.nama_user||'System'}</span></td><td>${l.aksi_user}</td></tr>`
    ).join('');
    document.getElementById('logTableBody').innerHTML = logRows || '<tr><td colspan="3">Kosong</td></tr>';
}
const modal = document.getElementById('userModal');

function openModal() {
    document.getElementById('userForm').reset();
    document.getElementById('id_user').value = '';
    modal.style.display = 'flex';
}

function closeModal() {
    modal.style.display = 'none';
}

function edit(u) {
    document.getElementById('id_user').value = u.id_user;
    document.getElementById('nama_user').value = u.nama_user;
    document.getElementById('username').value = u.username;
    document.getElementById('email_user').value = u.email_user;
    document.getElementById('role').value = u.role;
    modal.style.display = 'flex';
}
document.getElementById('userForm').onsubmit = async (e) => {
    e.preventDefault();
    let data = {
        id_user: document.getElementById('id_user').value,
        nama_user: document.getElementById('nama_user').value,
        username: document.getElementById('username').value,
        email_user: document.getElementById('email_user').value,
        role: document.getElementById('role').value,
        password: document.getElementById('password').value
    };
    await fetch(API_URL + '?action=save', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });
    closeModal();
    loadData();
};
async function del(id) {
    if (confirm('Hapus?')) {
        await fetch(API_URL + `?action=delete&id=${id}`);
        loadData();
    }
}
loadData();
</script>