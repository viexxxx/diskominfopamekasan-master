document.addEventListener("DOMContentLoaded", function () {
    // 🔹 Inisialisasi Flatpickr
    initFlatpickr();

    // 🔹 Inisialisasi Grafik Harga Nasional (Pertama)

    // 🔹 Inisialisasi Grafik Harga Kabupaten (4 Kabupaten)
    initPriceCharts();

    const cityDropdown = document.querySelector("#city");
    const datePicker = document.querySelector("#datePicker");

    if (cityDropdown && datePicker) {
        updateData(cityDropdown.value, datePicker.value);
    }

    if (cityDropdown) {
        cityDropdown.addEventListener("change", function () {
            updateData(this.value, datePicker.value);
        });
    }

    if (datePicker) {
        datePicker.addEventListener("change", function () {
            updateData(cityDropdown.value, this.value);
        });
    }

    // 🔹 Update grafik saat komoditas atau tanggal berubah
    $("#variant, #dateGraph").on("change", updateAllCharts);

    // 🔹 Inisialisasi filter kategori
    handleFilterScroll();
    handleFilterSelection();
});

/**
 * 🔹 Inisialisasi Flatpickr untuk pemilihan tanggal
 */
function initFlatpickr() {
    flatpickr("#datePicker", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "F j, Y",
        minDate: "2024-01-01"
    });
    flatpickr("#dateGraph", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "F j, Y",
        minDate: "2024-01-01"
    });
}

/**
 * 🔹 Inisialisasi Grafik Harga Nasional (Grafik Pertama)
 */

function updateData(city, date) {
    fetch(`views/carousel.php?kode_kab_kota=${city}&tanggal=${date}`)
        .then(response => response.text())
        .then(html => {
            const carouselContainer = document.querySelector(".price-container");

            if (!carouselContainer) {
                console.error("Error: Elemen .price-container tidak ditemukan!");
                return;
            }

            carouselContainer.innerHTML = html;
            initializeCharts(); // Render ulang grafik setelah data dimuat
        })
        .catch(error => console.error("Gagal memuat data:", error));
}

function initializeCharts() {
    document.querySelectorAll(".chart").forEach(chartElem => {
        const ctx = chartElem.getContext("2d");
        const hargaData = JSON.parse(chartElem.getAttribute("data-harga"));
        const tanggalData = JSON.parse(chartElem.getAttribute("data-tanggal"));
        const borderColor = chartElem.getAttribute("data-color");

        new Chart(ctx, {
            type: "line",
            data: {
                labels: tanggalData,
                datasets: [{
                    data: hargaData,
                    borderColor: borderColor,
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: borderColor,
                    fill: false,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { ticks: { font: { size: 10 }, autoSkip: false } },
                    y: {
                        beginAtZero: false,
                        ticks: {
                            font: { size: 10 },
                            callback: function(value) { return "Rp" + value.toLocaleString("id-ID"); }
                        }
                    }
                },
                plugins: { legend: { display: false } }
            }
        });
    });
}

let charts = {}; // Menyimpan instance grafik agar bisa diperbarui

function initPriceCharts() {
    const chartIds = ["chartPamekasan", "chartBangkalan", "chartSumenep", "chartSampang"];
    
    chartIds.forEach(id => {
        if (!document.getElementById(id)) {
            return;
        }
    });

    updateAllCharts();
}

/**
 * 🔹 Fungsi untuk memperbarui semua grafik dengan data terbaru
 */
function updateAllCharts() {
    let selectedVariant = $("#variant").val();
    let selectedDate = $("#dateGraph").val();

    let chartIds = {
        "3528": "chartPamekasan",
        "3526": "chartBangkalan",
        "3529": "chartSumenep",
        "3527": "chartSampang",
        "national": "priceChart"
    };

    Object.entries(chartIds).forEach(([kabCode, chartId]) => {
        fetchChartData(kabCode, chartId, selectedVariant, selectedDate);
    });
}

/**
 * 🔹 Warna untuk grafik setiap kota dan nasional
 */
const chartColors = {
    "3528": "#1E88E5",  // Pamekasan - Biru
    "3526": "#43A047",  // Bangkalan - Hijau
    "3529": "#FB8C00",  // Sumenep - Oranye
    "3527": "#8E24AA",  // Sampang - Ungu
    "national": "#000000" // Nasional - Hitam
};

/**
 * 🔹 Fungsi untuk mengambil data harga dan memperbarui grafik
 */
function fetchChartData(kabCode, chartId, variantId, selectedDate) {
    let ctx = document.getElementById(chartId)?.getContext("2d");

    if (!ctx) {
        console.warn(`❌ Canvas ${chartId} tidak ditemukan`);
        return;
    }

    $.ajax({
        url: `api/fetch_month_prices.php?variant_id=${variantId}&tanggal=${selectedDate}`,
        method: "GET",
        success: function (response) {
            if (!response.date || !response.harga || response.date.length === 0) {
                console.warn(`❌ Tidak ada data untuk ${chartId}`);
                return;
            }

            // ** Jalankan update hanya untuk grafik nasional **
            if (chartId === "priceChart") {
                updatePriceChangeDisplay(response.kenaikan_harga, response.persentase_kenaikan);
            }

            // ** Tentukan warna berdasarkan kota/nasional **
            let borderColor = chartColors[kabCode] || "#000000";
            let backgroundColor = borderColor + "20"; // Transparan 20%

            if (charts[chartId]) {
                charts[chartId].destroy(); // Hapus grafik lama sebelum menggambar ulang
            }

            charts[chartId] = new Chart(ctx, {
                type: "line",
                data: {
                    labels: response.date,
                    datasets: [{
                        label: kabCode === "national" ? "Harga Nasional" : "Harga Kabupaten",
                        data: response.harga,
                        borderColor: borderColor,
                        backgroundColor: backgroundColor,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: "top" },
                        title: { display: true, text: "Perubahan Harga Bulanan" }
                    },
                    scales: {
                        x: { grid: { display: true, color: "#E0E0E0" } },
                        y: {
                            beginAtZero: false,
                            grid: { display: true, color: "#E0E0E0" },
                            ticks: {
                                callback: (value) => `Rp${value.toLocaleString()}`
                            }
                        }
                    }
                }
            });
        },
        error: function (err) {
            console.error(`❌ Gagal mengambil data untuk ${chartId}:`, err);
        }
    });
}

/**
 * 🔹 Fungsi untuk memperbarui tampilan harga dan persentase perubahan
 */
function updatePriceChangeDisplay(priceChange, percentageChange) {
    const displayElement = document.getElementById("priceChangeDisplay");

    if (!displayElement) return;

    // Format angka ke Rupiah
    let formattedPriceChange = new Intl.NumberFormat("id-ID").format(Math.abs(priceChange));
    let formattedPercentage = percentageChange.toFixed(2) + "%";

    // Tentukan warna dan simbol perubahan
    let changeSymbol = priceChange >= 0 ? "▲" : "▼";
    let changeColor = priceChange >= 0 ? "green" : "red";

    // Update tampilan harga dan perubahan
    displayElement.innerHTML = `Rp ${formattedPriceChange} <span class="change ${changeColor}">${changeSymbol} ${formattedPercentage}</span>`;
}

/**
 * 🔹 Fungsi untuk menangani filter kategori scroll
 */
function handleFilterScroll() {
    const filterContainer = document.querySelector(".filter-buttons-container");
    if (!filterContainer) return;

    let isDown = false;
    let startX;
    let scrollLeft;

    filterContainer.addEventListener("mousedown", (e) => {
        isDown = true;
        startX = e.pageX - filterContainer.offsetLeft;
        scrollLeft = filterContainer.scrollLeft;
    });

    filterContainer.addEventListener("mouseleave", () => { isDown = false; });
    filterContainer.addEventListener("mouseup", () => { isDown = false; });

    filterContainer.addEventListener("mousemove", (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - filterContainer.offsetLeft;
        const walk = (x - startX) * 2;
        filterContainer.scrollLeft = scrollLeft - walk;
    });
}

/**
 * 🔹 Fungsi untuk menangani filter kategori pilihan
 */
function handleFilterSelection() {
    const filterButtons = document.querySelectorAll(".filter-button");
    filterButtons.forEach(button => {
        button.addEventListener("click", function () {
            filterButtons.forEach(btn => btn.classList.remove("active"));
            this.classList.add("active");
        });
    });
}
