<?php include "header.php"; ?>
<div class="main-wrapper">
    <div class="container">
        <div style="display:flex; justify-content:space-between;">
            <h2 class="section-title">Log Sensor</h2><button onclick="load()"
                class="btn-action btn-edit">Refresh</button>
        </div>
        <hr>
        <div class="table-responsive">
            <table class="table-data">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Sensor</th>
                        <th>Nilai</th>
                        <th>User</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody id="bodySensor"></tbody>
            </table>
        </div>
    </div>
</div>
<?php include "footer.php"; ?>
<script>
async function load() {
    let res = await fetch('../api/sensor_api.php?action=list');
    let json = await res.json();
    let rows = json.data.map(r =>
        `<tr><td>#${r.id_log_sensor}</td><td><span class="badge badge-sensor">${r.nama_sensor}</span></td><td><b>${r.pengukuran}</b> ${r.satuan_sensor||''}</td><td><span class="badge badge-user">${r.nama_user||'System'}</span></td><td>${r.timestamp}</td></tr>`
    ).join('');
    document.getElementById('bodySensor').innerHTML = rows || '<tr><td colspan="5">Kosong</td></tr>';
}
load();
setInterval(load, 5000);
</script>