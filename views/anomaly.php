<?php include "header.php"; ?>
<div class="main-wrapper">
    <div class="container">
        <h2 class="section-title" style="color:var(--danger)"><i class="fas fa-exclamation-triangle"></i> Log Anomali
        </h2>
        <hr>
        <div class="table-responsive">
            <table class="table-data">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Sensor</th>
                        <th>Lokasi</th>
                        <th>Masalah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="bodyAnomaly"></tbody>
            </table>
        </div>
    </div>
</div>
<?php include "footer.php"; ?>
<script>
async function load() {
    let res = await fetch('../api/anomaly_api.php?action=list');
    let data = await res.json();
    let rows = data.map(r =>
        `<tr><td>${r.waktu}</td><td>${r.nama_sensor}</td><td>${r.ruangan}</td><td><span class="badge badge-danger-soft">${r.aksi}</span></td><td><button onclick="del(${r.id_anomali})" class="btn-action btn-delete"><i class="fas fa-trash"></i></button></td></tr>`
        ).join('');
    document.getElementById('bodyAnomaly').innerHTML = rows || '<tr><td colspan="5">Aman</td></tr>';
}
async function del(id) {
    if (confirm('Hapus?')) {
        let res = await fetch(`../api/anomaly_api.php?action=delete&id=${id}`);
        let j = await res.json();
        if (j.status == 'error') alert(j.message);
        else load();
    }
}
load();
</script>