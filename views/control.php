<?php 
include "header.php"; 
// Cek Role di PHP untuk keamanan layer pertama
$isAdmin = isset($_SESSION['user']) && $_SESSION['user']['role'] === 'Admin'; 
?>

<div class="main-wrapper">
    <div class="container">

        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="section-title"><i class="fas fa-sliders-h"></i> Kontrol Aktuator</h2>

            <?php if($isAdmin): ?>
            <div>
                <button onclick="resetSystem()" class="btn-action btn-delete"
                    style="padding: 10px 15px; font-size:14px; margin-right:10px;">
                    <i class="fas fa-sync-alt"></i> Reset Sistem
                </button>
                <button onclick="openModal()" class="btn-action btn-add">
                    <i class="fas fa-plus"></i> Tambah Perangkat
                </button>
            </div>
            <?php endif; ?>
        </div>
        <hr>

        <div class="table-responsive">
            <table class="table-data">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Sensor</th>
                        <th>Batas Aman</th>
                        <th>Tipe Aktuator</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th>Kontrol</th>
                        <?php if($isAdmin): ?><th>Aksi</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody id="bodyControl">
                    <tr>
                        <td colspan="8" style="text-align:center;">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL TAMBAH PERANGKAT LENGKAP -->
<!-- ========================================== -->
<div id="deviceModal" class="modal">
    <div class="modal-content" style="max-width: 600px; padding: 40px; border-radius: 15px;">
        <span onclick="closeModal()" class="close-btn" style="top: 15px; right: 20px;">&times;</span>

        <div style="text-align: center; margin-bottom: 20px;">
            <h3 style="color: var(--text-dark); margin: 0; font-weight: 700;">Konfigurasi Perangkat</h3>
            <p style="color: #7f8c8d; font-size: 13px;">Setting sensor, batas anomali, dan kalibrasi.</p>
        </div>

        <form id="deviceForm">
            <!-- 1. IDENTITAS -->
            <h4
                style="font-size:14px; color:var(--primary-color); margin-bottom:15px; border-bottom:1px solid #eee; padding-bottom:5px;">
                1. Info Dasar</h4>
            <div style="display:flex; gap:15px;">
                <div class="input-group select-box" style="flex:1; position: relative;">
                    <label style="display:block; margin-bottom:8px; font-size:12px; font-weight:600; color:#555;">Jenis
                        Perangkat</label>
                    <div style="position: relative;">
                        <i class="fas fa-plug"
                            style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--primary-color);"></i>
                        <select id="jenis_sensor" required onchange="updateUnits()"
                            style="padding-left: 40px; width: 100%;">
                            <option value="" disabled selected>Pilih Jenis...</option>
                            <option value="DHT">Sensor Suhu (DHT)</option>
                            <option value="BMP">Sensor Tekanan (BMP)</option>
                            <option value="Buzzer">Alarm (Buzzer)</option>
                        </select>
                    </div>
                </div>
                <div class="input-group" style="flex:1; position: relative;">
                    <label style="display:block; margin-bottom:8px; font-size:12px; font-weight:600; color:#555;">Lokasi
                        Pemasangan</label>
                    <div style="position: relative;">
                        <i class="fas fa-map-marker-alt"
                            style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--primary-color);"></i>
                        <input type="text" id="ruangan" placeholder="Contoh: Lab A" required
                            style="padding-left: 40px; width: 100%;">
                    </div>
                </div>
            </div>

            <!-- 2. PARAMETER (Hanya muncul jika bukan Buzzer) -->
            <div id="paramSection">
                <h4
                    style="font-size:14px; color:var(--danger); margin-bottom:15px; margin-top: 15px; border-bottom:1px solid #eee; padding-bottom:5px;">
                    2. Batas Anomali (<span id="unitLabel1">-</span>)</h4>
                <div style="display:flex; gap:15px;">
                    <div class="input-group" style="flex:1;">
                        <label
                            style="display:block; margin-bottom:8px; font-size:12px; font-weight:600; color:#555;">Batas
                            Bawah (Min)</label>
                        <input type="number" step="0.1" id="batas_bawah" placeholder="Min Value" required
                            style="width: 100%;">
                    </div>
                    <div class="input-group" style="flex:1;">
                        <label
                            style="display:block; margin-bottom:8px; font-size:12px; font-weight:600; color:#555;">Batas
                            Atas (Max)</label>
                        <input type="number" step="0.1" id="batas_atas" placeholder="Max Value" required
                            style="width: 100%;">
                    </div>
                </div>

                <h4
                    style="font-size:14px; color:var(--warning); margin-bottom:15px; margin-top: 15px; border-bottom:1px solid #eee; padding-bottom:5px;">
                    3. Kalibrasi Data</h4>
                <div style="display:flex; gap:15px;">
                    <div class="input-group" style="flex:1; position: relative;">
                        <label
                            style="display:block; margin-bottom:8px; font-size:12px; font-weight:600; color:#555;">Offset
                            (+/-)</label>
                        <div style="position: relative;">
                            <i class="fas fa-arrows-alt-v"
                                style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--warning); font-size: 12px;"></i>
                            <input type="number" step="0.01" id="offset" placeholder="0" value="0"
                                style="padding-left: 40px; width: 100%;">
                        </div>
                    </div>
                    <div class="input-group" style="flex:1; position: relative;">
                        <label
                            style="display:block; margin-bottom:8px; font-size:12px; font-weight:600; color:#555;">Skala
                            (Multiplier)</label>
                        <div style="position: relative;">
                            <i class="fas fa-times"
                                style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--warning); font-size: 12px;"></i>
                            <input type="number" step="0.01" id="skala" placeholder="1" value="1"
                                style="padding-left: 40px; width: 100%;">
                        </div>
                    </div>
                </div>
                <!-- Menampilkan Satuan Otomatis (Readonly) -->
                <div class="input-group" style="margin-top: 15px; position: relative;">
                    <label style="display:block; margin-bottom:8px; font-size:12px; font-weight:600; color:#555;">Satuan
                        Terdeteksi</label>
                    <div style="position: relative;">
                        <i class="fas fa-ruler"
                            style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #777;"></i>
                        <input type="text" id="satuan_display" placeholder="Satuan Otomatis" readonly
                            style="background-color: #eee; color: #777; padding-left: 40px; width: 100%;">
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div style="display: flex; gap: 10px; margin-top: 25px;">
                <button type="button" onclick="closeModal()"
                    style="flex: 1; padding: 12px; border: 1px solid #ddd; background: white; color: #666; border-radius: 50px; font-weight: 600; cursor: pointer;">Batal</button>
                <button type="submit" class="btn-submit" style="flex: 1; border-radius: 50px; margin: 0;">Simpan
                    Konfigurasi</button>
            </div>
        </form>
    </div>
</div>

<?php include "footer.php"; ?>

<script>
const isAdmin = <?= $isAdmin ? 'true' : 'false'; ?>;

function updateUnits() {
    const jenis = document.getElementById('jenis_sensor').value;
    const unitLabel = document.getElementById('unitLabel1');
    const paramSection = document.getElementById('paramSection');
    const satuanDisplay = document.getElementById('satuan_display');

    if (jenis === 'DHT') {
        unitLabel.innerText = 'Celcius';
        satuanDisplay.value = 'Celcius (°C)';
        paramSection.style.display = 'block';
        document.getElementById('batas_bawah').value = 20;
        document.getElementById('batas_atas').value = 30;
    } else if (jenis === 'BMP') {
        unitLabel.innerText = 'HPa';
        satuanDisplay.value = 'HPa';
        paramSection.style.display = 'block';
        document.getElementById('batas_bawah').value = 950;
        document.getElementById('batas_atas').value = 1050;
    } else {
        // Buzzer tidak butuh parameter
        paramSection.style.display = 'none';
    }
}

async function load() {
    try {
        let res = await fetch('../api/control_api.php?action=list');
        let data = await res.json();

        if (data.length === 0) {
            document.getElementById('bodyControl').innerHTML =
                '<tr><td colspan="8" style="text-align:center;">Tidak ada perangkat.</td></tr>';
            return;
        }

        let rows = data.map(r => {
            let statusBadge = r.keadaan === 'ON' ? '<span class="badge badge-status-on">ON</span>' :
                '<span class="badge badge-status-off">OFF</span>';
            let sensorBadge =
                `<span class="badge badge-sensor" style="border-radius: 50px; font-size: 11px;">${r.nama_sensor}</span>`;

            // Tampilkan Batas & Parameter
            let limitInfo = '-';
            if (!r.nama_sensor.startsWith('Buzzer') && r.batas_atas) {
                // Pakai nilai default jika null
                let b_atas = r.batas_atas || '-';
                let b_bawah = r.batas_bawah || '-';
                let off = r.offset || '0';
                let skl = r.skala || '1';

                limitInfo = `
                        <div style="font-size:11px; line-height:1.4; color:#555;">
                            <div>Range: <b>${b_bawah} - ${b_atas}</b> ${r.satuan_sensor}</div>
                            <div style="color:#888;">(Offset: ${off}, Skala: ${skl})</div>
                        </div>
                    `;
            }

            return `<tr>
                    <td>#${r.id_kontrol}</td>
                    <td>${sensorBadge}</td>
                    <td>${limitInfo}</td>
                    <td>${r.nama_aktuator}</td>
                    <td>${r.ruangan}</td>
                    <td>${statusBadge}</td>
                    <td><label class="switch"><input type="checkbox" ${r.keadaan==='ON'?'checked':''} ${!isAdmin?'disabled':''} onchange="toggle(${r.id_kontrol}, this)"><span class="slider"></span></label></td>
                    ${isAdmin ? `<td><button class="btn-action btn-delete" onclick="del(${r.id_kontrol})"><i class="fas fa-trash"></i></button></td>` : ''}
                </tr>`;
        }).join('');

        document.getElementById('bodyControl').innerHTML = rows;
    } catch (e) {
        console.error(e);
    }
}

async function toggle(id, el) {
    if (!isAdmin) {
        alert("Akses Ditolak!");
        el.checked = !el.checked;
        return;
    }
    let state = el.checked ? 'ON' : 'OFF';
    try {
        await fetch('../api/control_api.php?action=toggle', {
            method: 'POST',
            body: JSON.stringify({
                id: id,
                state: state
            })
        });
        setTimeout(load, 100);
    } catch (e) {
        el.checked = !el.checked;
    }
}

// MODAL LOGIC
const modal = document.getElementById('deviceModal');

function openModal() {
    document.getElementById('deviceForm').reset();
    updateUnits();
    modal.style.display = 'flex';
}

function closeModal() {
    modal.style.display = 'none';
}
window.onclick = function(e) {
    if (e.target == modal) closeModal();
}

document.getElementById('deviceForm').onsubmit = async (e) => {
    e.preventDefault();
    let jenis = document.getElementById('jenis_sensor').value;
    let data = {
        jenis_sensor: jenis,
        ruangan: document.getElementById('ruangan').value,
        // Kirim parameter jika bukan buzzer
        batas_atas: (jenis != 'Buzzer') ? document.getElementById('batas_atas').value : 0,
        batas_bawah: (jenis != 'Buzzer') ? document.getElementById('batas_bawah').value : 0,
        offset: (jenis != 'Buzzer') ? document.getElementById('offset').value : 0,
        skala: (jenis != 'Buzzer') ? document.getElementById('skala').value : 1
    };
    await fetch('../api/control_api.php?action=add_device', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });
    closeModal();
    load();
};

async function del(id) {
    if (confirm("Hapus?")) {
        await fetch(`../api/control_api.php?action=delete_device&id=${id}`);
        load();
    }
}
async function resetSystem() {
    if (confirm("RESET TOTAL?")) {
        await fetch('../api/control_api.php?action=reset_system');
        load();
    }
}

load();
setInterval(load, 5000);
</script>