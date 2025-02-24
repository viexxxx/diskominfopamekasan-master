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
    <link rel="stylesheet" href="assets/css/main.css" />

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

    <section class="auth-container">
        <div class="auth-box" id="authBox">
          <div class="auth-left" id="authLeft">
            <h2>Welcome Back!</h2>
            <p>To keep connected with us please login with your personal info</p>
            <button class="btn switch-btn" id="switchToSignup">LOG IN</button>
          </div>
          <div class="auth-right" id="authRight">
            <h2>Create Account</h2>
            <div class="social-login">
              <button class="btn google-social-btn"></button>
              <button class="btn linkedin-social-btn"></button>
              <button class="btn gmail-social-btn"></button>
            </div>
            <p>Or use your email for registration:</p>
            <form id="signupForm">
                <div class="input-group">
                    <span class="input-icon">
                        <i class="lni lni-user-4"></i>
                    </span>
                    <input type="text" placeholder="Name" />
                </div>
                <div class="input-group">
                    <span class="input-icon">
                        <i class="lni lni-envelope-1"></i>
                    </span>
                    <input type="email" placeholder="Email" />
                </div>
                <div class="input-group">
                    <span class="input-icon">
                        <i class="lni lni-locked-1"></i>
                    </span>
                    <input type="password" placeholder="Password" />
                </div>
              <button type="submit" class="btn submit-btn" id="submitButton">LOG IN</button>
            </form>
          </div>
        </div>
      </section>
      
      

    <!-- ========================= scroll-top ========================= -->
    <a href="#" class="scroll-top">
        <i class="lni lni-chevron-up"></i>
    </a>

    <!-- ========================= JS here ========================= -->
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/wow.min.js"></script>
    <script src="assets/js/tiny-slider.js"></script>
    <script src="assets/js/glightbox.min.js"></script>
    <script src="assets/js/count-up.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="./node_modules/chart.js/dist/chart.umd.js"></script>
    <script>
        const authBox = document.querySelector(".auth-box");
        const switchButton = document.getElementById('switchToSignup');
        const submitButton = document.getElementById('submitButton');
        switchButton.addEventListener("click", () => {
        authBox.classList.toggle("switched"); // Toggle kelas 'switched'
        if (switchButton.textContent === 'LOG IN') {
            switchButton.textContent = 'SIGN UP';
            submitButton.textContent = 'SIGN UP';
        } else {
            switchButton.textContent = 'LOG IN';
            submitButton.textContent = 'LOG IN';
        }
        });

    </script>
</body>

</html>