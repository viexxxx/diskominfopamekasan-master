<!DOCTYPE html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Diskominfo Pamekasan</title>
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.svg" />

    <!-- ========================= CSS here ========================= -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/animate.css" />
    <link rel="stylesheet" href="assets/css/tiny-slider.css" />
    <link rel="stylesheet" href="assets/css/glightbox.min.css" />
    <link rel="stylesheet" href="assets/css/tes.css" />

    <link rel="stylesheet" href="./node_modules/flatpickr/dist/flatpickr.css"/>
    <link rel="stylesheet" href="./node_modules/lineicons/assets/icon-fonts/lineicons.css" />


</head>

<body>
    <!--[if lte IE 9]>
      <p class="browserupgrade">
        You are using an <strong>outdated</strong> browser. Please
        <a href="https://browsehappy.com/">upgrade your browser</a> to improve
        your experience and security.
      </p>
    <![endif]-->

    <!-- Preloader -->
    <div class="preloader">
        <div class="preloader-inner">
            <div class="preloader-icon">
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
    <!-- /End Preloader -->

    <!-- Start Header Area -->
    <header class="header navbar-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <div class="nav-inner">
                        <!-- Start Navbar -->
                        <nav class="navbar navbar-expand-lg">
                            <a class="navbar-brand" href="index.html">
                                <img src="assets/images/logo/diskominfo.svg" alt="Logo">
                            </a>
                            <button class="navbar-toggler mobile-menu-btn" type="button" data-bs-toggle="collapse"
                                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                                aria-expanded="false" aria-label="Toggle navigation">
                                <span class="toggler-icon"></span>
                                <span class="toggler-icon"></span>
                                <span class="toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse sub-menu-bar" id="navbarSupportedContent">
                                <ul id="nav" class="navbar-nav ms-auto">
                                    <li class="nav-item">
                                        <div class="nav-link">
                                          <a href="?page=home" class="active" aria-label="Toggle navigation">
                                            <span class="icon">
                                                <i class="lni lni-home-2"></i>
                                            </span>
                                            Dashboard
                                          </a>
                                        </div>
                                    </li>
                                      
                                    <li class="nav-item">
                                        <div class="nav-link">
                                          <a href="?page=profile" class="active" aria-label="Toggle navigation">
                                            <span class="icon">
                                                <i class="lni lni-double-quotes-end-1"></i>
                                            </span>
                                            Profil
                                          </a>
                                        </div>
                                    </li>

                                    <li class="nav-item">
                                        <div class="nav-link">
                                          <a href="?page=login" class="active" aria-label="Toggle navigation">
                                            <span class="icon">
                                              <i class="lni lni-exit"></i>
                                            </span>
                                            Login
                                          </a>
                                        </div>
                                    </li>
                                </ul>
                            </div> <!-- navbar collapse -->
                            <div class="button home-btn">
                                <a href="#" class="btn">Contact Us</a>
                            </div>
                        </nav>
                        <!-- End Navbar -->
                    </div>
                </div>
            </div> <!-- row -->
        </div> <!-- container -->
    </header>
    <!-- End Header Area -->

    <!-- Start Hero Area -->
    <section class="hero-area">
        <div class="container">
          <div class="hero-content">
            <div class="row align-items-center">
              <div class="col-lg-5 col-md-12 col-12">
                <h1>
                  Dashboard <span class="highlight">Pangan</span><br />
                  Kabupaten Pamekasan
                </h1>
              </div>
            </div>
            <div class="search-container">
              <div class="row align-items-center">
                <div class="col-lg-6 col-md-12 col-12">
                  <label for="datePicker">Pilih Tanggal</label>
                  <div class="dropdown-wrapper">
                      <input type="text" id="datePicker" class="form-control" placeholder="Pilih Tanggal">
                  </div>
                </div>                 
              </div>              
            </div>
            <div class="price-info">
              <div class="row">
                <div class="col-lg-6 col-md-12 col-12">
                    <?php include $_SERVER['DOCUMENT_ROOT'] . '/views/carousel.php'; ?>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="custom-table-wrapper">
                      <?php include $_SERVER['DOCUMENT_ROOT'] . '/views/average_kab.php'; ?> 
                    </div>
                </div>
              </div>
            </div>

            <div class="search-container">
              <div class="row align-items-center">
                <div class="col-lg-6 col-md-12 col-12">
                    <div class="search-box">
                      <div class="row">
                        <div class="col-md-3 col-6">
                          <label for="variant">Pilih Komoditas</label>
                          <div class="dropdown-wrapper">
                            <select id="variant" class="form-select">
                              <option selected value="51">Beras Medium</option>
                              <option value="52">Beras Premium</option>
                              <option value="9">Cabai Merah Besar</option>
                              <option value="10">Cabai Rawit</option>
                              <option value="17">Minyak Goreng Sawit</option>
                              <option value="18">MinyaKita</option>
                              <option value="26">Tepung Terigu</option>
                              <option value="27">Daging Ayam Ras</option>
                              <option value="25">Telur Ayam Ras</option>
                              <option value="19">Daging Sapi</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>                  
              </div>              
            </div>
            <div class="price-section">
                <!-- Grafik Pamekasan -->
                <div class="col-lg-12 col-md-12">
                    <h3 class="table-title">Harga Harian<br><span class="highlight">Nasional, Jawa Timur, Pamekasan</span></h3>
                    <canvas id="chartPamekasan"></canvas>
                </div>

                <!-- Grafik Bangkalan -->
                <div class="col-lg-12 col-md-12">
                    <h3 class="table-title">Harga Harian<br><span class="highlight">Madura</span></h3>
                    <canvas id="chartMadura"></canvas>
                </div>
            </div>            

            <div class="chart-section">
            <h3 class="table-title">Harga Tahunan<br><span class="highlight">Pamekasan</span></h3>
                <div class="chart-container">
                  <canvas id="priceChart"></canvas>
                </div>
            </div>

          </div>
        </div>
    </section>
    <!-- End Hero Area -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3>📌 Link Pengaduan</h3>
                <p>Laporan Pengaduan klik <a href="https://lapor.go.id">lapor.go.id</a></p>
                <p>Laporan tindak pidana korupsi klik <a href="https://docs.google.com/forms/d/e/1FAIpQLSeGq7QXoo8h-02Om8l6bsNF4Y3OSdDxFNpE5AuJkc3zS9WP2A/viewform">WBS Inspektorat</a></p>
                <p>Pelaporan gratifikasi klik <a href="https://gol.kpk.go.id">gol.kpk.go.id</a></p>
            </div>
            <div class="footer-section">
                <h3>📞 Kontak Kami</h3>
                <p>Kantor Dinas Komunikasi dan Informatika Pamekasan</p>
                <p>Jl. Jokotole Gg. IV No. 1, Kel. Barurambat Kota, Kec. Pamekasan, Kabupaten Pamekasan, Jawa Timur 69317</p>
                <p>Email: <a href="mailto:diskominfo@pamekasankab.go.id">diskominfo@pamekasankab.go.id</a></p>
            </div>
            <div class="footer-section map-container">
                <h3>🗺️ Map</h3>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3958.591785116312!2d113.47447707499984!3d-7.173630492857451!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7891c91c9c1df%3A0x87bbf4e3f8e7cf65!2sDinas%20Komunikasi%20dan%20Informatika%20Kabupaten%20Pamekasan!5e0!3m2!1sid!2sid!4v1718111111111"></iframe>
            </div>
        </div>
        <div class="footer-bottom">
            2025 Dinas Komunikasi dan Informatika Kabupaten Pamekasan
        </div>
    </footer>

    <!-- ========================= scroll-top ========================= -->
     
    <a href="#" class="scroll-top">
        <i class="lni lni-chevron-up"></i>
    </a>
    <div class="sticky-lower-third">
        <div class="marquee-text">
            <span class="highlight">Pemantauan Harga Dilakukan Saat Hari Kerja</span> | Dinas Komunikasi dan Informatika Pamekasan
        </div>
    </div>
    <!-- ========================= JS here ========================= -->
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/wow.min.js"></script>
    <script src="assets/js/tiny-slider.js"></script>
    <script src="assets/js/glightbox.min.js"></script>
    <script src="assets/js/count-up.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/changes.js"></script>
    <script src="./node_modules/chart.js/dist/chart.umd.js"></script>
    <script src="./node_modules/jquery/dist/jquery.js"></script>
    <script src="./node_modules/alpinejs/dist/cdn.js"></script>
    <script src="./node_modules/flatpickr/dist/flatpickr.js"></script>

</body>

</html>