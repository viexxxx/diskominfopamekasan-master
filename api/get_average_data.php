<?php
header('Content-Type: application/json');

// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Daftar variant_id yang ingin diambil (berdasarkan urutan tertentu)
$selected_variant_ids = [52, 51, 9, 10, 17, 18, 26, 27, 25, 19];

function fetchData($tanggal) {
    $api_url = "https://api-sp2kp.kemendag.go.id/report/api/average-price/generate-perbandingan-harga";
    
    $post_data = [
        "tanggal" => $tanggal
    ];

    // Konversi ke format `x-www-form-urlencoded`
    $form_data = http_build_query($post_data);

    // Inisialisasi cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/x-www-form-urlencoded",
        "Accept: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $form_data);

    // Eksekusi request
    $response = curl_exec($ch);
    curl_close($ch);

    // Jika terjadi error, return null
    if ($response === false) {
        return null;
    }

    return json_decode($response, true);
}

// Tentukan tanggal saat ini
$tanggal = date("Y-m-d");

// Ambil data dari API
$data = fetchData($tanggal);

// Jika tidak ada data, tetap kembalikan JSON kosong
if (!$data || empty($data['data'])) {
    echo json_encode(["error" => "Tidak ada data dalam rentang waktu yang diambil"]);
    exit;
}

// 🔹 Cleaning Data: Ambil hanya variant yang diinginkan
$cleaned_data = [];
foreach ($data['data'] as $item) {
    if (in_array($item['variant_id'], $selected_variant_ids)) {
        $cleaned_data[] = [
            "variant_id" => $item['variant_id'],
            "variant" => $item['variant_nama'],
            "satuan" => $item['satuan_display'],
            "tanggal" => $item['tanggal'],
            "harga" => $item['harga']
        ];
    }
}

// 🔹 Urutkan hasil sesuai urutan dalam $selected_variant_ids
usort($cleaned_data, function ($a, $b) use ($selected_variant_ids) {
    return array_search($a['variant_id'], $selected_variant_ids) - array_search($b['variant_id'], $selected_variant_ids);
});

// Output hasil dalam format JSON
echo json_encode($cleaned_data, JSON_PRETTY_PRINT);
?>
