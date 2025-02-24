<?php
try {
    $context = stream_context_create([
        'http' => ['timeout' => 5]
    ]);
    $selected_city = isset($_GET['kode_kab_kota']) ? $_GET['kode_kab_kota'] : '3528';

    // Ambil data terbaru dari `get_data.php`
    $data_json = file_get_contents("http://diskominfopamekasan.test/api/get_data.php?kode_kab_kota=" . $selected_city);
    $data = json_decode($data_json, true);

    if (!$data_json) {
        throw new Exception("Gagal mengambil data dari API");
    }

    if (!$data || isset($data['error'])) {
        throw new Exception("Data tidak tersedia");
    }
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
    exit;
}

// **Atur pagination (9 card per slide)**
$cards_per_slide = 9;
$slides = array_chunk($data, $cards_per_slide);
?>

<!-- **Integrasi Alpine.js** -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<!-- **Filter Kategori & Carousel** -->
<div class="price-carousel" x-data="{ selectedCategory: 'all' }">
    <!-- **Kategori Filter** -->
    <div class="filter-buttons">
        <button @click="selectedCategory = 'all'" :class="{ 'active' : selectedCategory === 'all' }">Semua</button>
        <?php foreach ($data as $info): ?>
            <button @click="selectedCategory = '<?= $info['variant'] ?>'" :class="{ 'active' : selectedCategory === '<?= $info['variant'] ?>' }">
                <?= $info['variant'] ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- **Carousel** -->
    <div id="priceCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php $slide_index = 0; ?>
            <?php foreach ($slides as $slide): ?>
                <div class="carousel-item <?= ($slide_index === 0) ? "active" : "" ?>">
                    <div class="row gx-3 gy-3">
                        <?php foreach ($slide as $info): ?>
                            <?php 
                                $chart_id = "chart-" . md5($info["variant"]);
                                $color = ($info["status"] === "naik") ? "red" : "green"; 
                            ?>
                            <div class="col-lg-4 col-md-6 col-sm-12" x-show="selectedCategory === 'all' || selectedCategory === '<?= $info['variant'] ?>'">
                                <div class="price-card" data-category="<?=$info["variant"]?>">
                                    <div class="card-left">
                                        <img src="./assets/images/card/ayam.png" alt="<?= $info["variant"] ?>">
                                        <span class="price-change <?= ($info["status"] === "naik") ? 'red' : 'green' ?>">
                                            <i class="lni <?= ($info["status"] === "naik") ? 'lni lni-arrow-angular-top-left' : 'lni lni-arrow-angular-top-right rotated' ?>"></i>
                                            <?= $info["status"] === "naik" ? "+" : "-" ?><?= $info["persen"] ?> | Rp<?= number_format($info["jumlah"], 0, ',', '.') ?>
                                        </span>
                                    </div>
                                    <div class="card-right">
                                        <h4><?= $info["variant"] ?></h4>
                                        <p>Rp<?= number_format(end($info["harga"]), 0, ',', '.') ?> / kg</p>
                                        <div class="graph">
                                            <canvas id="<?= $chart_id ?>" class="chart" 
                                                data-harga='<?= json_encode($info["harga"]) ?>' 
                                                data-tanggal='<?= json_encode($info["date"]) ?>' 
                                                data-color="<?= $color ?>">
                                            </canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php $slide_index++; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="carousel-pagination">
        <button class="btn-carousel-prev" type="button" data-bs-target="#priceCarousel" data-bs-slide="prev">
            <i class="lni lni-chevron-left"></i>
        </button>
        <span id="page-indicator" class="page-indicator">Page 1 / <?= count($slides) ?></span>
        <button class="btn-carousel-next" type="button" data-bs-target="#priceCarousel" data-bs-slide="next">
            <i class="lni lni-chevron-left rotated"></i>
        </button>
    </div> 
</div>

<!-- **Update Pagination saat Slide Berubah** -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var carousel = document.getElementById("priceCarousel");
        var pageIndicator = document.getElementById("page-indicator");
        var totalSlides = <?= count($slides) ?>;

        carousel.addEventListener("slid.bs.carousel", function (event) {
            var currentIndex = event.to + 1;
            pageIndicator.textContent = "Page " + currentIndex + " / " + totalSlides;
        });
    });
</script>
