<?php
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

function fetchData($variant_id, $tanggal_awal, $level, $kode_provinsi = null, $kode_kab_kota = null, $pasar_id = null) {
    $api_url = "https://api-sp2kp.kemendag.go.id/report/api/average-price/export-area-daily-json";
    $found_dates = [];
    $current_date = strtotime($tanggal_awal);

    for ($i = 0; $i < 7; $i++) {
        $formatted_date = date("Y-m-d", $current_date);

        $post_data = [
            "start_date" => $formatted_date,
            "end_date" => $formatted_date,
            "level" => $level,
            "variant_ids" => $variant_id,
            "skip_sat_sun" => true,
            "tipe_komoditas" => 1
        ];

        if ($kode_provinsi !== null) {
            $post_data["kode_provinsi"] = $kode_provinsi;
        }
        if ($kode_kab_kota !== null) {
            $post_data["kode_kab_kota"] = $kode_kab_kota;
        }
        if ($pasar_id !== null) {
            $post_data["pasar_id"] = $pasar_id;
        }

        $form_data = http_build_query($post_data);
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

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response !== false) {
            $data = json_decode($response, true);
            if (!empty($data['data'])) {
                foreach ($data['data'] as $item) {
                    if ($item['variant_id'] == $variant_id) {
                        $found_dates[$formatted_date] = $item['daftarHarga'][0]['harga'] ?? 0;
                    }
                }
            }
        }

        $current_date = strtotime("-1 day", $current_date);
    }

    return $found_dates;
}

$variant_id = $_GET['variant_id'] ?? '51';
$selected_date = $_GET['tanggal'] ?? date("Y-m-d");

// Ambil data dari tiga wilayah
$pamekasan = fetchData($variant_id, $selected_date, 3, 35, 3528, 453);
$jawa_timur = fetchData($variant_id, $selected_date, 1, 35);
$nasional = fetchData($variant_id, $selected_date, 0);

// Pastikan label (tanggal) konsisten
$all_dates = array_keys($pamekasan + $jawa_timur + $nasional);
sort($all_dates);

// Susun data harga sesuai tanggal
$pamekasan_data = array_map(fn($date) => $pamekasan[$date] ?? null, $all_dates);
$jawa_timur_data = array_map(fn($date) => $jawa_timur[$date] ?? null, $all_dates);
$nasional_data = array_map(fn($date) => $nasional[$date] ?? null, $all_dates);

// Tampilkan JSON untuk Chart.js
echo json_encode([
    "date" => $all_dates,
    "pamekasan" => $pamekasan_data,
    "jawa_timur" => $jawa_timur_data,
    "nasional" => $nasional_data
], JSON_PRETTY_PRINT);
?>
