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
}

function updateData(date) {
    Promise.all([
        fetch(`views/carousel.php?tanggal=${date}`).then(response => response.text()),
        fetch(`views/average_kab.php?tanggal=${date}`).then(response => response.text())
    ])
    .then(([carouselHtml, averageKabHtml]) => {
        // Update elemen container untuk carousel
        const carouselContainer = document.querySelector(".price-container");
        if (carouselContainer) {
            carouselContainer.innerHTML = carouselHtml;
        } else {
            console.error("Error: Elemen .price-container tidak ditemukan!");
        }

        // Update elemen container untuk average kabupaten
        const averageKabContainer = document.querySelector(".custom-table");
        if (averageKabContainer) {
            averageKabContainer.innerHTML = averageKabHtml;
        } else {
            console.error("Error: Elemen .custom-table tidak ditemukan!");
        }

        // Render ulang grafik setelah data dimuat
        initializeCharts();
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
    let selectedDate = $("#datePicker").val();

    // 4 grafik kota → API `fetch_prices.php` (Data 1 minggu)
    fetchChartDataRegion("chartPamekasan", selectedVariant, selectedDate, "fetch_prices.php");
    fetchChartDataMadura("chartMadura", selectedVariant, selectedDate, "get_average_madura.php");

    // Grafik nasional → API `fetch_month_prices.php` (Data 1 tahun)
    fetchChartData("priceChart", selectedVariant, selectedDate, "fetch_month_prices.php");
}

function fetchChartDataRegion(chartId, variantId, selectedDate, apiEndpoint) {
    let canvas = document.getElementById(chartId);
    if (!canvas) {
        console.warn(`Canvas ${chartId} tidak ditemukan di halaman.`);
        return;
    }

    let ctx = canvas.getContext("2d");

    $.ajax({
        url: `api/${apiEndpoint}?variant_id=${variantId}&tanggal=${selectedDate}`,
        method: "GET",
        success: function (response) {

            if (!response.date || !response.pamekasan || !response.jawa_timur || !response.nasional) {
                console.warn(`Data tidak tersedia untuk ${chartId}`);
                return;
            }

            let allDates = response.date.map(date => new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }));

            if (charts[chartId]) {
                charts[chartId].destroy();
            }

            charts[chartId] = new Chart(ctx, {
                type: "line",
                data: {
                    labels: allDates, 
                    datasets: [
                        {
                            label: "Pamekasan",
                            data: response.pamekasan,
                            borderColor: "#ff0000",
                            backgroundColor: "#ff000020",
                            fill: false,
                            tension: 0.3
                        },
                        {
                            label: "Jawa Timur",
                            data: response.jawa_timur,
                            borderColor: "#0000ff",
                            backgroundColor: "#0000ff20",
                            fill: false,
                            tension: 0.3
                        },
                        {
                            label: "Nasional",
                            data: response.nasional,
                            borderColor: "#008000",
                            backgroundColor: "#00800020",
                            fill: false,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: "top" },
                        title: { display: true, text: "Harga Harian" }
                    },
                    scales: {
                        x: { grid: { display: true, color: "#E0E0E0" } },
                        y: {
                            beginAtZero: false,
                            grid: { display: true, color: "#E0E0E0" },
                            ticks: { callback: (value) => `Rp${value.toLocaleString()}` }
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

function fetchChartDataMadura(chartId, variantId, selectedDate, apiEndpoint) {
    let canvas = document.getElementById(chartId);
    if (!canvas) {
        console.warn(`Canvas ${chartId} tidak ditemukan di halaman.`);
        return;
    }

    let ctx = canvas.getContext("2d");

    $.ajax({
        url: `api/${apiEndpoint}?variant_id=${variantId}&tanggal=${selectedDate}`,
        method: "GET",
        success: function (response) {

            if (!response.date) {
                console.warn(`Data tidak tersedia untuk ${chartId}`);
                return;
            }

            let allDates = response.date.map(date => new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }));

            if (charts[chartId]) {
                charts[chartId].destroy();
            }

            charts[chartId] = new Chart(ctx, {
                type: "line",
                data: {
                    labels: allDates, 
                    datasets: [
                        {
                            label: "Pamekasan",
                            data: response.pamekasan,
                            borderColor: "#ff0000",
                            backgroundColor: "#ff000020",
                            fill: false,
                            tension: 0.3
                        },
                        {
                            label: "Bangkalan",
                            data: response.bangkalan,
                            borderColor: "#0000ff",
                            backgroundColor: "#0000ff20",
                            fill: false,
                            tension: 0.3
                        },
                        {
                            label: "Sampang",
                            data: response.sampang,
                            borderColor: "#008000",
                            backgroundColor: "#00800020",
                            fill: false,
                            tension: 0.3
                        },
                        {
                            label: "Sumenep",
                            data: response.sumenep,
                            borderColor: "#ffa500",
                            backgroundColor: "#ffa50020",
                            fill: false,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: "top" }, title: { display: true, text: "Harga Harian" } },
                    scales: { x: { grid: { display: true } }, y: { beginAtZero: false } }
                }
            });
        }
    });
}


/**
 * 🔹 Fungsi untuk mengambil data harga dan memperbarui grafik
 */
function fetchChartData(chartId, variantId, selectedDate, apiEndpoint) {
    let canvas = document.getElementById(chartId);

    if (!canvas) {
        console.warn(`Canvas ${chartId} tidak ditemukan di halaman.`);
        return;
    }

    let ctx = canvas.getContext("2d");

    $.ajax({
        url: `api/${apiEndpoint}?variant_id=${variantId}&tanggal=${selectedDate}`,
        method: "GET",
        success: function (response) {

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
