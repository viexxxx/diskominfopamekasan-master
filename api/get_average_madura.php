<?php
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fungsi untuk mengambil data dari API
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

// Kode Kabupaten dan Pasar untuk 4 Kabupaten
$kabupaten_list = [
    "pamekasan" => ["kode_kab_kota" => 3528, "pasar_id" => 453],
    "sampang" => ["kode_kab_kota" => 3527, "pasar_id" => 452],
    "bangkalan"   => ["kode_kab_kota" => 3526, "pasar_id" => 451],
    "sumenep"   => ["kode_kab_kota" => 3529, "pasar_id" => 42]
];

$variant_id = $_GET['variant_id'] ?? '51';
$selected_date = $_GET['tanggal'] ?? date("Y-m-d");

$data_kabupaten = [];
$all_dates = [];

// Looping untuk mengambil data dari setiap kabupaten
foreach ($kabupaten_list as $nama_kab => $info_kab) {
    $data_kabupaten[$nama_kab] = fetchData($variant_id, $selected_date, 3, 35, $info_kab["kode_kab_kota"], $info_kab["pasar_id"]);
    $all_dates = array_merge($all_dates, array_keys($data_kabupaten[$nama_kab]));
}

// Pastikan label (tanggal) konsisten
$all_dates = array_unique($all_dates);
sort($all_dates);

// Susun data harga sesuai tanggal
foreach ($data_kabupaten as $nama_kab => &$harga_kab) {
    $harga_kab = array_map(fn($date) => $harga_kab[$date] ?? null, $all_dates);
}

// Tampilkan JSON untuk Chart.js
echo json_encode(array_merge(["date" => $all_dates], $data_kabupaten), JSON_PRETTY_PRINT);
?>
