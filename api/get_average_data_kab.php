<?php
header('Content-Type: application/json');

// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fungsi untuk mengambil data berdasarkan rentang tanggal yang tersedia
function fetchData($tanggal_awal) {
    $api_url = "https://api-sp2kp.kemendag.go.id/report/api/average-price/export-area-daily-json";

    // Mencari 3 tanggal terakhir yang tersedia
    $found_dates = [];
    $max_check_days = 30; // Cek hingga 30 hari ke belakang jika perlu
    $current_date = strtotime($tanggal_awal);

    for ($i = 0; $i < $max_check_days; $i++) {
        $formatted_date = date("Y-m-d", $current_date);
        $post_data = [
            "start_date" => $formatted_date,
            "end_date" => $formatted_date,
            "level" => 3,
            "variant_ids" => "52,51,9,10,17,18,26,27,25,19",
            "kode_provinsi" => 35,
            "kode_kab_kota" => 3528,
            "pasar_id" => 453,
            "skip_sat_sun" => true,
            "tipe_komoditas" => 1
        ];

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
                // Pastikan setidaknya ada 1 harga yang bukan 0
                $valid_data = array_filter($data['data'], function($item) {
                    return !empty($item['daftarHarga']) && max(array_column($item['daftarHarga'], 'harga')) > 0;
                });

                if (!empty($valid_data)) {
                    $found_dates[$formatted_date] = $valid_data;
                }
            }
        }

        // Jika sudah mendapatkan 3 tanggal, hentikan loop
        if (count($found_dates) >= 2) {
            break;
        }

        // Kurangi 1 hari dari tanggal saat ini
        $current_date = strtotime("-1 day", $current_date);
    }

    return $found_dates;
}

// Ambil parameter tanggal & kota dari GET request
$selected_date = isset($_GET['tanggal']) ? $_GET['tanggal'] : date("Y-m-d");

// Panggil fungsi untuk mendapatkan data
$data_by_dates = fetchData($selected_date);

// Jika tidak ada data yang ditemukan, kembalikan JSON kosong
if (empty($data_by_dates)) {
    echo json_encode(["error" => "Tidak ada data dalam rentang waktu yang tersedia"]);
    exit;
}

// 🔹 Format data untuk frontend
$cleaned_data = [];

foreach ($data_by_dates as $date => $data_list) {
    foreach ($data_list as $item) {
        $variant_id = $item['variant_id'];
        $variant_name = $item['variant'];
        $prices = [];
        $dates = [];

        foreach ($item['daftarHarga'] as $harga) {
            if ($harga['harga'] > 0) { // Pastikan harga tidak 0
                $dates[] = $date;
                $prices[] = $harga['harga'];
            }
        }

        if (!empty($prices)) {
            $status = "turun";
            $jumlah = 0;
            $persen = 0;

            if (count($prices) > 1) {
                $last_price = end($prices);
                $prev_price = prev($prices);

                if ($prev_price > 0) {
                    $jumlah = abs($last_price - $prev_price);
                    $persen = round(($jumlah / $prev_price) * 100, 2);
                }

                if ($last_price > $prev_price) {
                    $status = "naik";
                }
            }

            // Gabungkan data berdasarkan variant_id untuk menghindari duplikasi
            if (!isset($cleaned_data[$variant_id])) {
                $cleaned_data[$variant_id] = [
                    "variant_id" => $variant_id,
                    "variant" => $variant_name,
                    "date" => [],
                    "harga" => [],
                    "status" => $status,
                    "jumlah" => $jumlah,
                    "persen" => $persen . "%"
                ];
            }

            // Tambahkan data harga & tanggal
            $cleaned_data[$variant_id]["date"] = array_merge($cleaned_data[$variant_id]["date"], $dates);
            $cleaned_data[$variant_id]["harga"] = array_merge($cleaned_data[$variant_id]["harga"], $prices);
        }
    }
}

foreach ($cleaned_data as &$item) {
    $item['date'] = array_reverse($item['date']);
    $item['harga'] = array_reverse($item['harga']);
}



// Ubah ke array numerik untuk JSON output
echo json_encode(array_values($cleaned_data), JSON_PRETTY_PRINT);
?>
