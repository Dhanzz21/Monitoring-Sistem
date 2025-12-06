<?php 
require "config/database.php"; 
include "views/header.php"; 

$totalSensor = $pdo->query("SELECT COUNT(*) FROM tbl_sensor")->fetchColumn();
$totalUser = $pdo->query("SELECT COUNT(*) FROM tbl_user")->fetchColumn();
?>

<div class="main-wrapper">
    <div class="container">

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 class="section-title" style="margin: 0;">Dashboard Real-time</h2>
            <div style="text-align: right;">
                <span class="status-indicator status-online"></span> <small>Live Connection</small>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="stat-card success">
                <div class="stat-info">
                    <h3 id="total-sensor-count"><?= $totalSensor ?></h3>
                    <p>Sensor Aktif</p>
                </div>
                <div class="stat-icon"><i class="fas fa-satellite-dish"></i></div>
            </div>
            <div class="stat-card warning">
                <div class="stat-info">
                    <h3><?= $totalUser ?></h3>
                    <p>User Terdaftar</p>
                </div>
                <div class="stat-icon"><i class="fas fa-users"></i></div>
            </div>
        </div>

        <hr>

        <h3 style="margin-bottom: 15px; color: #555;">Status Sensor Terkini</h3>
        <div id="dynamic-sensor-cards" class="dashboard-grid"
            style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
            <p style="color:#999;">Memuat data sensor...</p>
        </div>

        <div class="chart-box">
            <h3>Live Monitoring (Multi-Sensor)</h3>
            <div style="height: 350px; position: relative;">
                <canvas id="chartRealtime"></canvas>
            </div>
        </div>

    </div>
</div>

<?php include "views/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Konfigurasi Warna Konsisten: Biru (1), Hijau (2), Kuning (3), Hitam (4), dst
const colors = ['#1a73e8', '#2ecc71', '#f1c40f', '#333333', '#9b59b6', '#e67e22'];

const ctx = document.getElementById('chartRealtime').getContext('2d');
const myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [],
        datasets: []
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: false, // Matikan animasi agar grafik geser mulus real-time
        interaction: {
            mode: 'index',
            intersect: false
        },
        scales: {
            y: {
                beginAtZero: false
            }
        },
        elements: {
            point: {
                radius: 0
            }
        }
    }
});

let activeSensors = [];

// --- MAIN LOOP ---
async function runSystemLoop() {
    try {
        // 1. Ambil List Sensor dari API
        let res = await fetch('api/control_api.php?action=list');
        let data = await res.json();

        // Filter: Hanya yang ON dan BUKAN Buzzer yang masuk grafik
        let sensorsOnGraph = data.filter(s => s.keadaan === 'ON' && !s.nama_sensor.startsWith('Buzzer'));
        let allSensorsNoBuzzer = data.filter(s => !s.nama_sensor.startsWith('Buzzer'));

        // Update Total Angka
        const countEl = document.getElementById('total-sensor-count');
        if (countEl && countEl.innerText != allSensorsNoBuzzer.length) {
            countEl.innerText = allSensorsNoBuzzer.length;
        }

        // Update UI
        updateSensorCards(allSensorsNoBuzzer);
        updateChartDatasets(sensorsOnGraph);

        // 2. Minta Server Generate Data Baru (Simulasi)
        await requestNewData(sensorsOnGraph);

    } catch (error) {
        console.error("Error Loop:", error);
        await new Promise(r => setTimeout(r, 3000));
        runSystemLoop();
    }
}

// --- UPDATE KARTU ---
function updateSensorCards(sensors) {
    const container = document.getElementById('dynamic-sensor-cards');
    const currentCards = container.querySelectorAll('.stat-card');

    // Logika Re-render: Jika jumlah beda ATAU ID beda
    const currentIds = Array.from(currentCards).map(c => c.id);
    const newIds = sensors.map(s => `card-${s.id_sensor}`);

    let needRerender = false;
    if (currentCards.length !== sensors.length) needRerender = true;
    else {
        for (let id of newIds) {
            if (!currentIds.includes(id)) {
                needRerender = true;
                break;
            }
        }
    }

    if (needRerender) {
        if (sensors.length === 0) {
            container.innerHTML = '<p class="text-muted">Tidak ada sensor terpasang.</p>';
            return;
        }

        let html = '';
        sensors.forEach((s) => {
            // Warna Konsisten berdasarkan ID (Index array warna)
            // ID 1 -> Index 0 (Biru), ID 2 -> Index 1 (Hijau), dst
            let colorIndex = (s.id_sensor - 1) % colors.length;
            if (colorIndex < 0) colorIndex = 0;
            let color = colors[colorIndex];

            let isOff = s.keadaan === 'OFF';
            let valColor = isOff ? '#ccc' : color;
            let valText = (!isOff && s.last_reading) ? s.last_reading : (isOff ? 'Mati' : '-');
            let unit = s.satuan_sensor || '';

            html += `
                    <div class="stat-card" id="card-${s.id_sensor}" style="border-left: 4px solid ${isOff ? '#ccc' : color}; padding: 15px; opacity: ${isOff ? '0.7' : '1'};">
                        <div class="stat-info">
                            <h3 id="val-display-${s.id_sensor}" style="color:${valColor}" data-unit="${unit}">${valText} ${unit}</h3>
                            <p style="font-weight:600; font-size:12px;">${s.nama_sensor} <span id="status-${s.id_sensor}">${isOff ? '(OFF)' : ''}</span></p>
                            <small style="color:#888;">${s.ruangan}</small>
                        </div>
                        <div class="stat-icon" style="color: ${isOff ? '#ccc' : color}; opacity: 0.2;">
                            <i class="fas fa-microchip"></i>
                        </div>
                    </div>
                `;
        });
        container.innerHTML = html;
    } else {
        // Update Status Saja (Tanpa Reset HTML agar tidak kedip)
        sensors.forEach((s) => {
            let card = document.getElementById(`card-${s.id_sensor}`);
            let valEl = document.getElementById(`val-display-${s.id_sensor}`);
            let statusEl = document.getElementById(`status-${s.id_sensor}`);

            if (card && valEl) {
                let colorIndex = (s.id_sensor - 1) % colors.length;
                if (colorIndex < 0) colorIndex = 0;
                let color = colors[colorIndex];
                let isOff = s.keadaan === 'OFF';

                card.style.borderLeftColor = isOff ? '#ccc' : color;
                card.style.opacity = isOff ? 0.7 : 1;
                valEl.style.color = isOff ? '#ccc' : color;

                if (statusEl) statusEl.innerText = isOff ? '(OFF)' : '';

                // Jika OFF, tulis Mati. Jika ON, jangan sentuh (biar diupdate requestNewData)
                if (isOff) valEl.innerText = 'Mati';
            }
        });
    }
}

// --- UPDATE GRAFIK ---
function updateChartDatasets(sensorsOn) {
    // Hapus dataset usang
    for (let i = myChart.data.datasets.length - 1; i >= 0; i--) {
        let ds = myChart.data.datasets[i];
        let stillExists = sensorsOn.find(s => s.id_sensor == ds.sensorId);
        if (!stillExists) {
            myChart.data.datasets.splice(i, 1);
        }
    }

    // Tambah dataset baru
    sensorsOn.forEach((s) => {
        let existing = myChart.data.datasets.find(ds => ds.sensorId == s.id_sensor);
        if (!existing) {
            // Warna Konsisten
            let colorIndex = (s.id_sensor - 1) % colors.length;
            if (colorIndex < 0) colorIndex = 0;
            let color = colors[colorIndex];

            // Ambil data awal dari last_reading agar grafik nyambung
            let startData = [];
            if (s.last_reading) startData.push(s.last_reading);

            myChart.data.datasets.push({
                sensorId: s.id_sensor,
                label: s.nama_sensor,
                borderColor: color,
                backgroundColor: 'transparent',
                borderWidth: 2,
                tension: 0.4,
                data: startData
            });
        }
    });

    myChart.update();
}

// --- REQUEST DATA KE SERVER ---
async function requestNewData(sensors) {
    // Geser sumbu X jika penuh
    if (myChart.data.labels.length > 20) {
        myChart.data.labels.shift();
        myChart.data.datasets.forEach(ds => ds.data.shift());
    }

    let serverTime = null;
    let updatePromises = [];

    // Loop request paralel
    for (let s of sensors) {
        // HANYA KIRIM ID, SERVER YANG HITUNG NILAI
        updatePromises.push(
            fetch('api/sensor_api.php?action=simulasi', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id_sensor: s.id_sensor
                })
            }).then(res => res.json()).then(json => {
                if (json.status === 'success') {
                    // DATA BALIKAN SERVER (PASTI SAMA DENGAN DATABASE)
                    let newVal = json.data.nilai;

                    // Update Chart
                    let dataset = myChart.data.datasets.find(ds => ds.sensorId == s.id_sensor);
                    if (dataset) dataset.data.push(newVal);

                    // Update Kartu
                    let cardVal = document.getElementById(`val-display-${s.id_sensor}`);
                    if (cardVal) {
                        let unit = cardVal.getAttribute('data-unit') || '';
                        cardVal.innerText = newVal + " " + unit;
                    }
                    serverTime = json.data.timestamp;
                }
            })
        );
    }

    await Promise.all(updatePromises);

    if (serverTime) myChart.data.labels.push(serverTime);
    myChart.update();

    // Delay 3 detik
    await new Promise(r => setTimeout(r, 3000));

    runSystemLoop();
}

runSystemLoop();
</script>