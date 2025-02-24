<?php
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

function fetchData($variant_id, $start_date, $end_date) {
    $api_url = "https://api-sp2kp.kemendag.go.id/report/api/average-price/export-area-monthly-json";

    $post_data = [
        "start_date" => $start_date,
        "end_date" => $end_date,
        "level" => 0,
        "variant_ids" => $variant_id,
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

    file_put_contents("debug_log.txt", "Response:\n" . print_r($response, true) . "\n\n", FILE_APPEND);

    $found_dates = [];

    if ($response !== false) {
        $data = json_decode($response, true);
        if (!empty($data['data'][0]['daftarHarga'])) {
            foreach ($data['data'][0]['daftarHarga'] as $item) {
                if (isset($item['date']) && isset($item['harga'])) {
                    $found_dates[] = [
                        "date" => $item['date'],
                        "harga" => $item['harga']
                    ];
                }
            }
        }
    }

    return $found_dates;
}

$variant_id = $_GET['variant_id'] ?? '51';
$selected_date = $_GET['tanggal'] ?? date("Y-m-d");

if (!$selected_date) {
    echo json_encode(["error" => "Tanggal tidak valid"]);
    exit;
}

// Dapatkan tahun saat ini dan tahun sebelumnya
$current_year = date("Y", strtotime($selected_date));
$previous_year = $current_year - 1;

// Rentang waktu satu tahun ke belakang
$start_date = date("Y-01-01", strtotime($previous_year . "-01-01"));
$end_date = date("Y-m-d", strtotime($selected_date));

$data_by_dates = fetchData($variant_id, $start_date, $end_date);

// Pisahkan data menjadi dua kategori
$data_last_year = [];
$data_this_year = [];

foreach ($data_by_dates as $item) {
    $item_year = date("Y", strtotime($item['date']));
    $formatted_date = date("M Y", strtotime($item['date']));

    if ($item_year == $previous_year) {
        $data_last_year[] = [
            "date" => $formatted_date,
            "harga" => $item['harga']
        ];
    } else {
        $data_this_year[] = [
            "date" => $formatted_date,
            "harga" => $item['harga']
        ];
    }
}

// Format hasil untuk JSON
$response = [
    "status" => "success",
    "last_year" => [
        "date" => array_column($data_last_year, "date"),
        "harga" => array_column($data_last_year, "harga")
    ],
    "this_year" => [
        "date" => array_column($data_this_year, "date"),
        "harga" => array_column($data_this_year, "harga")
    ]
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>
