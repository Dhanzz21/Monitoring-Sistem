document.addEventListener("DOMContentLoaded", () => {

    // load tabel pengukuran
    fetch("/api/sensor_api.php")
        .then(res => res.json())
        .then(data => {
            let html = "";
            data.forEach(d => {
                html += `
                    <tr>
                        <td>${d.nama_sensor}</td>
                        <td>${d.pengukuran}</td>
                        <td>${d.nama_user}</td>
                        <td>${d.timestamp}</td>
                    </tr>
                `;
            });

            document.getElementById("tabelPengukuran").innerHTML = html;
        });

});

// Toggle navbar for mobile
document.addEventListener("DOMContentLoaded", () => {
    const navbar = document.querySelector(".navbar");
    const toggleBtn = document.createElement("div");

    toggleBtn.classList.add("menu-toggle");
    toggleBtn.innerHTML = "☰";
    toggleBtn.style.fontSize = "22px";
    toggleBtn.style.color = "white";
    toggleBtn.style.cursor = "pointer";
    toggleBtn.style.marginLeft = "15px";
    toggleBtn.style.display = "none";

    navbar.appendChild(toggleBtn);

    // Show only on mobile
    if (window.innerWidth < 768) {
        toggleBtn.style.display = "block";
    }

    toggleBtn.addEventListener("click", () => {
        navbar.classList.toggle("active");
    });
});

