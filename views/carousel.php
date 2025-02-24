<?php
try {
    // Ambil kota yang dipilih atau default ke '3528'

    // Cek apakah tanggal dipilih, jika tidak gunakan tanggal kemarin
    $selected_date = isset($_GET['tanggal']) ? $_GET['tanggal'] : date("Y-m-d", strtotime("-1 day"));

    // Ambil data dari API berdasarkan kota dan tanggal
    $data_json = file_get_contents("http://diskominfopamekasan.test/api/get_data.php?tanggal=" . $selected_date);
    $data = json_decode($data_json, true);

    if (!$data_json) {
        throw new Exception("Gagal mengambil data dari API");
    }

    if (!$data || isset($data['error'])) {
        echo "<p class='error-message'>Data tidak tersedia untuk tanggal $selected_date</p>";
        exit;
    }
} catch (Exception $e) {
    echo "<p class='error-message'>Data tidak tersedia</p>";
    exit;
}
?>

<div class="price-container">
    <div class="row gx-3 gy-3">
        <?php foreach ($data as $info): ?>
            <?php 
                $chart_id = "chart-" . md5($info["variant"]);
                $color = ($info["status"] === "naik") ? "red" : "green"; 
                $hasData = !empty($info["harga"]) && count(array_filter($info["harga"])) > 0; // Cek apakah ada data harga
            ?>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="price-card">
                    <div class="card-left">
                        <img src="./assets/images/card/<?= $info["variant_id"] ?>.png" alt="<?= $info["variant"] ?>">
                    </div>
                    <div class="card-right">
                        <h4><?= $info["variant"] ?></h4>
                        <p>Rp<?= number_format(end($info["harga"]), 0, ',', '.') ?> / kg</p>

                        <div class="graph">
                            <?php if ($hasData): ?>
                                <canvas id="<?= $chart_id ?>" class="chart" 
                                    data-harga='<?= json_encode($info["harga"]) ?>' 
                                    data-tanggal='<?= json_encode($info["date"]) ?>' 
                                    data-color="<?= $color ?>">
                                </canvas>
                            <?php else: ?>
                                <h4 class="no-data">Data Tidak Tersedia</h4>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
