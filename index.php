<?php
// Routing sederhana menggunakan parameter "page"
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Tentukan path file berdasarkan halaman yang diakses
$pageFile = "views/" . $page . ".php";

if (file_exists($pageFile)) {
    include $pageFile;
} else {
    // Jika halaman tidak ditemukan, tampilkan halaman 404
    echo "<h2 style='text-align:center; margin-top:50px;'>404 - Halaman Tidak Ditemukan</h2>";
}
?>
