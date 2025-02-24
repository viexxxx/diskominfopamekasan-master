document.addEventListener("DOMContentLoaded", function () {
    // 🔹 Inisialisasi Flatpickr
    initFlatpickr();

    // 🔹 Inisialisasi Grafik Harga Kabupaten (4 Kabupaten)
    initPriceCharts();

    initializeCharts();
    
    const datePicker = document.querySelector("#datePicker");

    if (datePicker) {
        datePicker.addEventListener("change", function () {
            updateData(this.value);
        });
    }

    // 🔹 Update grafik saat komoditas atau tanggal berubah
    $("#variant, #dateGraph").on("change", updateAllCharts);
});

/**
 * 🔹 Inisialisasi Flatpickr untuk pemilihan tanggal
 */
function initFlatpickr() {
    flatpickr("#datePicker", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "F j, Y",
        minDate: "2024-01-01",
        defaultDate: new Date(new Date().setDate(new Date().getDate() - 1))
    });
    flatpickr("#dateGraph", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "F j, Y",
        minDate: "2024-01-01",
        defaultDate: new Date(new Date().setDate(new Date().getDate() - 1))
    });
}

function updateData(date) {
    fetch(`views/carousel.php?tanggal=${date}`)
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

/**
 * 🔹 Inisialisasi Grafik Harga Kabupaten
 */
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

    // 4 grafik kota → API `fetch_prices.php` (Data 1 minggu)
    // fetchChartData("3528", "chartPamekasan", selectedVariant, selectedDate, "fetch_prices.php");
    // fetchChartData("3526", "chartBangkalan", selectedVariant, selectedDate, "fetch_prices.php");
    // fetchChartData("3529", "chartSumenep", selectedVariant, selectedDate, "fetch_prices.php");
    // fetchChartData("3527", "chartSampang", selectedVariant, selectedDate, "fetch_prices.php");

    // Grafik nasional → API `fetch_month_prices.php` (Data 1 tahun)
    fetchChartData("national", "priceChart", selectedVariant, selectedDate, "fetch_month_prices.php");
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
function fetchChartData(kabCode, chartId, variantId, selectedDate, apiEndpoint) {
    let canvas = document.getElementById(chartId);

    if (!canvas) {
        console.warn(`Canvas ${chartId} tidak ditemukan di halaman.`);
        return;
    }

    let ctx = canvas.getContext("2d");

    $.ajax({
        url: `api/${apiEndpoint}?variant_id=${variantId}&kode_kab_kota=${kabCode}&tanggal=${selectedDate}`,
        method: "GET",
        success: function (response) {
            console.log("Response API:", response); // Debugging

            if (!response.last_year || !response.this_year) {
                console.warn(`Data tidak tersedia untuk ${chartId}`);
                return;
            }

            const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

            function mapDataToMonths(dateArray, hargaArray) {
                let mappedData = new Array(12).fill(null);
                dateArray.forEach((date, index) => {
                    let monthIndex = months.indexOf(date.split(" ")[0]); 
                    if (monthIndex !== -1) {
                        mappedData[monthIndex] = hargaArray[index] || null;
                    }
                });
                return mappedData;
            }

            let lastYearMapped = mapDataToMonths(response.last_year.date, response.last_year.harga);
            let thisYearMapped = mapDataToMonths(response.this_year.date, response.this_year.harga);

            console.log("Mapped Data 2024:", lastYearMapped);
            console.log("Mapped Data 2025:", thisYearMapped);

            let allData = lastYearMapped.concat(thisYearMapped).filter((v) => v !== null);

            let yMin = Math.min(...allData) * 0.95; // Sedikit lebih rendah dari harga terendah
            let yMax = Math.max(...allData) * 1.05; // Tambahkan margin di atas harga tertinggi

            if (charts[chartId]) {
                charts[chartId].destroy();
            }

            charts[chartId] = new Chart(ctx, {
                type: "line",
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: "Harga 2024",
                            data: lastYearMapped,
                            borderColor: "#ff0000",
                            backgroundColor: "#ff000020",
                            fill: false,
                            tension: 0.3
                        },
                        {
                            label: "Harga 2025",
                            data: thisYearMapped,
                            borderColor: "#0000ff",
                            backgroundColor: "#0000ff20",
                            fill: false,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: "top" },
                        title: { display: true, text: "Harga Tahunan Pamekasan" }
                    },
                    scales: {
                        x: { 
                            grid: { display: true, color: "#E0E0E0" },
                            ticks: { autoSkip: false }
                        },
                        y: {
                            min: yMin,
                            max: yMax,
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
            console.error(`Gagal mengambil data untuk ${chartId}:`, err);
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
