<?php
$context = stream_context_create([
    'http' => ['timeout' => 5]
]);

$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date("Y-m-d");

// 🔹 Ambil data dari API
$data_json = file_get_contents("http://diskominfopamekasan.test/api/get_average_data_kab.php?tanggal=" . $tanggal);
$data = json_decode($data_json, true);

$unit_mapping = [
    52 => "kg",
    51 => "kg",
    9 => "kg",
    10 => "kg",
    17 => "liter",
    18 => "liter",
    26 => "kg",
    27 => "kg",
    25 => "kg",
    19 => "kg"
];

// 🔹 Ambil tanggal awal & akhir dari data pertama yang memiliki harga
$tanggal_awal = "-";
$tanggal_akhir = "-";

foreach ($data as $info) {
    if (!empty($info["harga"])) {
        $tanggal_awal = date("d M", strtotime($info["date"][0])); // Tanggal pertama yang valid
        $tanggal_akhir = date("d M", strtotime(end($info["date"]))); // Tanggal terakhir yang valid
        break; // Hanya ambil tanggal dari data pertama yang valid
    }
}
?>

<!-- 🔹 Struktur tabel hanya ada satu kali -->
<table class="custom-table">
    <thead>
        <tr>
            <th>Komoditas</th>
            <th>Unit</th>
            <th id="tanggal_awal"><?= $tanggal_awal ?></th>
            <th id="tanggal_akhir"><?= $tanggal_akhir ?></th>
            <th>Perubahan</th>
        </tr>
    </thead>
    <tbody id="average-table">
        <?php foreach ($data as $info): ?>
            <tr>
                <td><?= htmlspecialchars($info['variant']) ?></td> <!-- Komoditas -->
                <td><?= isset($unit_mapping[$info['variant_id']]) ? $unit_mapping[$info['variant_id']] : '-' ?></td> <!-- Unit -->
                <td><?= isset($info['harga'][0]) ? number_format($info['harga'][0], 0, ',', '.') : '-' ?></td> <!-- Harga Tanggal Awal -->
                <td><?= isset($info['harga'][1]) ? number_format($info['harga'][1], 0, ',', '.') : '-' ?></td> <!-- Harga Tanggal Akhir -->
                <td><?= htmlspecialchars($info['persen']) ?></td> <!-- Perubahan Persentase -->
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
