<?php include "header.php"; require "../config/database.php";
$data = $pdo->query("SELECT * FROM tbl_panduan")->fetchAll(PDO::FETCH_ASSOC); ?>
<div class="main-wrapper">
    <div class="container">
        <div style="text-align:center; margin-bottom:30px;">
            <div class="landing-icon-box"><i class="fas fa-info"></i></div>
            <h2>Tentang Aplikasi</h2>
        </div>
        <div class="dashboard-grid">
            <?php foreach($data as $r): ?>
            <div class="stat-card" style="display:block; height:100%;">
                <h3><?= $r['judul'] ?></h3>
                <hr>
                <p><?= nl2br($r['isi']) ?></p>
                <?php if($r['versi_sistem']!='-'): ?><br><span
                    class="badge badge-sensor">v<?= $r['versi_sistem'] ?></span><?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php include "footer.php"; ?>