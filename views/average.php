<?php
try {
    $context = stream_context_create([
        'http' => ['timeout' => 5]
    ]);


    // Ambil data terbaru dari `get_data.php`
    $data_json = file_get_contents("http://diskominfopamekasan.test/api/get_average_data.php");
    $data = json_decode($data_json, true);

    if (!$data_json) {
        throw new Exception("Gagal mengambil data dari API");
    }

    if (!$data || isset($data['error'])) {
        throw new Exception("Data tidak tersedia");
    }
} catch (Exception $e) {
    exit;
}
?>
<?php foreach($data as $info): ?>
    <tr>
        <td><?= $info['variant']?></td>
        <td><?= number_format($info["harga"], 0, ',', '.') . '/' . $info['satuan'] ?></td>
    </tr>
<?php endforeach; ?>    





