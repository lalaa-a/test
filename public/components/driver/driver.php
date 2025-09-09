<?php
// DEV only: show PHP errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define APP_ROOT constant
define('APP_ROOT', dirname(__DIR__, 3) . '/app');

// Public base URL for assets/images (adjust if needed)
$BASE_URL = '/test';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Plan Your Way Through Sri-Lanka's Wonders</title>
  <link rel="stylesheet" href="driver.css">
  <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@400;600;700&family=Roboto:wght@400;600&family=Poppins:wght@400&family=Inter:wght@700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  
  <?php
    include APP_ROOT.'/libraries/Functions.php';
    addAssets('inc','navigation');
    addAssets('inc','footer');
    printAssets();
  ?>
</head>
<body>
   <!--navigation bar-->
   <?php renderComponent('inc','navigation',[]); ?>

  <!-- Main Content -->
  <main class="main-content">

    <!-- Hero Section -->
    <section class="hero-section" style="margin-bottom:40px; margin-top:0;">
      <div class="hero-destinations-grid">
        <article class="destination-card" tabindex="0" aria-label="Ella, lush green hills and scenic train rides">
          <div class="destination-bg" style="background-image:url('../driver/images/img01.jpg');"></div>
          <div class="destination-overlay"></div>
          <div class="destination-text">
            <h2 class="destination-title">Ella</h2>
            <p class="destination-subtitle">Green hills & scenic train rides</p>
          </div>
        </article>
        <article class="destination-card" tabindex="0" aria-label="Galle, historic fort and coastal charm">
          <div class="destination-bg" style="background-image:url('../driver/images/img02.jpg');"></div>
          <div class="destination-overlay"></div>
          <div class="destination-text">
            <h2 class="destination-title">Galle</h2>
            <p class="destination-subtitle">Historic fort and coastal charm</p>
          </div>
        </article>
        <article class="destination-card" tabindex="0" aria-label="Yala, wildlife safaris and national park">
          <div class="destination-bg" style="background-image:url('../driver/images/img03.jpg');"></div>
          <div class="destination-overlay"></div>
          <div class="destination-text">
            <h2 class="destination-title">Yala</h2>
            <p class="destination-subtitle">Wildlife safaris & national parks</p>
          </div>
        </article>
        <article class="destination-card" tabindex="0" aria-label="Sigiriya, ancient rock fortress and panoramic views">
          <div class="destination-bg" style="background-image:url('../driver/images/img04.jpg');"></div>
          <div class="destination-overlay"></div>
          <div class="destination-text">
            <h2 class="destination-title">Sigiriya</h2>
            <p class="destination-subtitle">Rock fort & views</p>
          </div>
        </article>
      </div>
    </section>

    <!-- Trending Drivers Section -->
        <section class="drivers-section">
            <h2 class="section-title">Trending Drivers</h2>
            <div class="drivers-row">
                    <div class="drivers-container">
                    <div class="driver-card">
                        <div class="driver-badge top-rated">Top Rated</div>
                        <div class="driver-avatar">
                            <img src="http://localhost:3845/assets/85d14703779dbe008f621b5c9aa61934f86a364c.png" alt="John Doe">
                        </div>
                        <div class="driver-info">
                            <h3 class="driver-name">John Doe</h3>
                            <div class="driver-rating">
                                <span class="star">★</span>
                                <span class="rating">4.9</span>
                                <span class="reviews">(124 reviews)</span>
                            </div>
                            <p class="driver-description">
                                Experienced driver with a comfortable sedan. Knows all the best routes and hidden gems. Fluent in English.
                            </p>
                            <button class="select-driver-btn">Select Driver</button>
                        </div>
                    </div>
                    <div class="driver-card">
                        <div class="driver-badge top-rated">Top Rated</div>
                        <div class="driver-avatar">
                            <img src="http://localhost:3845/assets/d84cc254159082188feff2d34dfc6e9238320fe8.png" alt="Jane Smith">
                        </div>
                        <div class="driver-info">
                            <h3 class="driver-name">Jane Smith</h3>
                            <div class="driver-rating">
                                <span class="star">★</span>
                                <span class="rating">4.8</span>
                                <span class="reviews">(86 reviews)</span>
                            </div>
                            <p class="driver-description">
                                Friendly and reliable driver with a spacious van, perfect for families or large groups. Safety is my priority.
                            </p>
                            <button class="select-driver-btn">Select Driver</button>
                        </div>
                    </div>
                    <div class="driver-card">
                        <div class="driver-badge most-booked">Most Booked</div>
                        <div class="driver-avatar">
                            <img src="http://localhost:3845/assets/dc44eecc642e9be7da61c724fd4b46f41efac2cd.png" alt="Kumar Fernando">
                        </div>
                        <div class="driver-info">
                            <h3 class="driver-name">Kumar Fernando</h3>
                            <div class="driver-rating">
                                <span class="star">★</span>
                                <span class="rating">5.0</span>
                                <span class="reviews">(210 reviews)</span>
                            </div>
                            <p class="driver-description">
                                Your local guide on wheels! I'll not only drive you but also share stories about our beautiful country.
                            </p>
                            <button class="select-driver-btn">Select Driver</button>
                        </div>
                    </div>
                    <div class="driver-card">
                        <div class="driver-avatar">
                            <img src="http://localhost:3845/assets/05290308ee16b71bd5e5c1bb8463f0e3cb324862.png" alt="Saman Perera">
                        </div>
                        <div class="driver-info">
                            <h3 class="driver-name">Saman Perera</h3>
                            <div class="driver-rating">
                                <span class="star">★</span>
                                <span class="rating">4.7</span>
                                <span class="reviews">(95 reviews)</span>
                            </div>
                            <p class="driver-description">
                                Punctual and professional driver with a modern, air-conditioned car for your ultimate comfort.
                            </p>
                            <button class="select-driver-btn">Select Driver</button>
                        </div>
                    </div>
                    <div class="driver-card">
                        <div class="driver-avatar">
                            <img src="http://localhost:3845/assets/fcc7a2a5ab3d9e00eb348299ffb0b305a88f7e4b.png" alt="Nimal Silva">
                        </div>
                        <div class="driver-info">
                            <h3 class="driver-name">Nimal Silva</h3>
                            <div class="driver-rating">
                                <span class="star">★</span>
                                <span class="rating">4.9</span>
                                <span class="reviews">(158 reviews)</span>
                            </div>
                            <p class="driver-description">
                                Passionate about travel and culture. I offer customized tours to make your journey unforgettable.
                            </p>
                            <button class="select-driver-btn">Select Driver</button>
                        </div>
                    </div>
                    <div class="driver-card">
                        <div class="driver-avatar">
                            <img src="http://localhost:3845/assets/5505e7f18c3390dbecb39dafd7e71849786c4ad6.png" alt="Rohan Jayasuriya">
                        </div>
                        <div class="driver-info">
                            <h3 class="driver-name">Rohan Jayasuriya</h3>
                            <div class="driver-rating">
                                <span class="star">★</span>
                                <span class="rating">4.8</span>
                                <span class="reviews">(112 reviews)</span>
                            </div>
                            <p class="driver-description">
                                Wildlife enthusiast and experienced safari driver. Let's explore the national parks together!
                            </p>
                            <button class="select-driver-btn">Select Driver</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <!-- Example: One drivers section (repeat structure for Trending / Licensed / Reviewed / Tourist) -->
    <section class="drivers-section">
            <h2 class="section-title">All Drivers</h2>
            <h3 class="subsection-title">Licensed Drivers</h3>
            <div class="drivers-container-grid">
                <div class="driver-card">
                    <div class="driver-avatar">
                        <img src="http://localhost:3845/assets/85d14703779dbe008f621b5c9aa61934f86a364c.png" alt="John Doe">
                    </div>
                    <div class="driver-info">
                        <h3 class="driver-name">John Doe</h3>
                        <div class="driver-rating">
                            <span class="star">★</span>
                            <span class="rating">4.9</span>
                            <span class="reviews">(124 reviews)</span>
                        </div>
                        <p class="driver-description">
                            Experienced driver with a comfortable sedan. Knows all the best routes and hidden gems. Fluent in English.
                        </p>
                        <button class="select-driver-btn">Select Driver</button>
                    </div>
                </div>
                <div class="driver-card">
                    <div class="driver-avatar">
                        <img src="http://localhost:3845/assets/d84cc254159082188feff2d34dfc6e9238320fe8.png" alt="Jane Smith">
                    </div>
                    <div class="driver-info">
                        <h3 class="driver-name">Jane Smith</h3>
                        <div class="driver-rating">
                            <span class="star">★</span>
                            <span class="rating">4.8</span>
                            <span class="reviews">(86 reviews)</span>
                        </div>
                        <p class="driver-description">
                            Friendly and reliable driver with a spacious van, perfect for families or large groups. Safety is my priority.
                        </p>
                        <button class="select-driver-btn">Select Driver</button>
                    </div>
                </div>
                <div class="driver-card">
                    <div class="driver-avatar">
                        <img src="http://localhost:3845/assets/dc44eecc642e9be7da61c724fd4b46f41efac2cd.png" alt="Kumar Fernando">
                    </div>
                    <div class="driver-info">
                        <h3 class="driver-name">Kumar Fernando</h3>
                        <div class="driver-rating">
                            <span class="star">★</span>
                            <span class="rating">5.0</span>
                            <span class="reviews">(210 reviews)</span>
                        </div>
                        <p class="driver-description">
                            Your local guide on wheels! I'll not only drive you but also share stories about our beautiful country.
                        </p>
                        <button class="select-driver-btn">Select Driver</button>
                    </div>
                </div>
                <div class="driver-card">
                    <div class="driver-avatar">
                        <img src="images/driver-jane-smith-198.png" alt="Maria Rodriguez">
                    </div>
                    <div class="driver-info">
                        <h3 class="driver-name">Maria Rodriguez</h3>
                        <div class="driver-rating">
                            <span class="star">★</span>
                            <span class="rating">4.7</span>
                            <span class="reviews">(156 reviews)</span>
                        </div>
                        <p class="driver-description">
                            Multilingual guide specializing in cultural tours. Drives a luxury SUV and knows the best local restaurants and shopping spots.
                        </p>
                        <button class="select-driver-btn">Select Driver</button>
                    </div>
                </div>
            </div>
            <button class="see-more-btn" onclick="window.location.href='LicensedDriver.html'">
                See More
                <img src="http://localhost:3845/assets/34fa6f5128ca8c6bd1292e82ea9ddd3a9d87a8e4.svg" alt="Arrow" class="arrow-icon">
            </button>
        </section>

        <!-- Reviewed Drivers Section -->
        <section class="drivers-section">
            <h2 class="section-title">Reviewed Drivers</h2>
                <div class="drivers-row">
                <div class="drivers-container-grid">
                    <div class="driver-card">
                        <div class="driver-avatar">
                            <img src="http://localhost:3845/assets/85d14703779dbe008f621b5c9aa61934f86a364c.png" alt="John Doe">
                        </div>
                        <div class="driver-info">
                            <h3 class="driver-name">John Doe</h3>
                            <div class="driver-rating">
                                <span class="star">★</span>
                                <span class="rating">4.9</span>
                                <span class="reviews">(124 reviews)</span>
                            </div>
                            <p class="driver-description">
                                Experienced driver with a comfortable sedan. Knows all the best routes and hidden gems. Fluent in English.
                            </p>
                            <button class="select-driver-btn">Select Driver</button>
                        </div>
                    </div>
                    <div class="driver-card">
                        <div class="driver-avatar">
                            <img src="http://localhost:3845/assets/d84cc254159082188feff2d34dfc6e9238320fe8.png" alt="Jane Smith">
                        </div>
                        <div class="driver-info">
                            <h3 class="driver-name">Jane Smith</h3>
                            <div class="driver-rating">
                                <span class="star">★</span>
                                <span class="rating">4.8</span>
                                <span class="reviews">(86 reviews)</span>
                            </div>
                            <p class="driver-description">
                                Friendly and reliable driver with a spacious van, perfect for families or large groups. Safety is my priority.
                            </p>
                            <button class="select-driver-btn">Select Driver</button>
                        </div>
                    </div>
                    <div class="driver-card">
                        <div class="driver-avatar">
                            <img src="http://localhost:3845/assets/dc44eecc642e9be7da61c724fd4b46f41efac2cd.png" alt="Kumar Fernando">
                        </div>
                        <div class="driver-info">
                            <h3 class="driver-name">Kumar Fernando</h3>
                            <div class="driver-rating">
                                <span class="star">★</span>
                                <span class="rating">5.0</span>
                                <span class="reviews">(210 reviews)</span>
                            </div>
                            <p class="driver-description">
                                Your local guide on wheels! I'll not only drive you but also share stories about our beautiful country.
                            </p>
                            <button class="select-driver-btn">Select Driver</button>
                        </div>
                    </div>
                    <div class="driver-card">
                        <div class="driver-avatar">
                            <img src="images/driver-jane-smith-198.png" alt="Maria Rodriguez">
                        </div>
                        <div class="driver-info">
                            <h3 class="driver-name">Maria Rodriguez</h3>
                            <div class="driver-rating">
                                <span class="star">★</span>
                                <span class="rating">4.7</span>
                                <span class="reviews">(156 reviews)</span>
                            </div>
                            <p class="driver-description">
                                Multilingual guide specializing in cultural tours. Drives a luxury SUV and knows the best local restaurants and shopping spots.
                            </p>
                            <button class="select-driver-btn">Select Driver</button>
                        </div>
                    </div>
                </div>
                <button class="see-more-btn" onclick="window.location.href='ReviewedDriver.html'">
                    See More
                    <img src="http://localhost:3845/assets/34fa6f5128ca8c6bd1292e82ea9ddd3a9d87a8e4.svg" alt="Arrow" class="arrow-icon">
                </button>
            </div>
        </section>

        <!-- Tourist Drivers Section -->
        <section class="drivers-section">
            <h2 class="section-title">Tourist Drivers</h2>
            <div class="drivers-row">
                <div class="drivers-container-grid">
                    <div class="driver-card">
                        <div class="driver-avatar">
                            <img src="http://localhost:3845/assets/85d14703779dbe008f621b5c9aa61934f86a364c.png" alt="John Doe">
                        </div>
                        <div class="driver-info">
                            <h3 class="driver-name">John Doe</h3>
                            <div class="driver-rating">
                                <span class="star">★</span>
                                <span class="rating">4.9</span>
                                <span class="reviews">(124 reviews)</span>
                            </div>
                            <p class="driver-description">
                                Experienced driver with a comfortable sedan. Knows all the best routes and hidden gems. Fluent in English.
                            </p>
                            <button class="select-driver-btn">Select Driver</button>
                        </div>
                    </div>
                    <div class="driver-card">
                        <div class="driver-avatar">
                            <img src="http://localhost:3845/assets/d84cc254159082188feff2d34dfc6e9238320fe8.png" alt="Jane Smith">
                        </div>
                        <div class="driver-info">
                            <h3 class="driver-name">Jane Smith</h3>
                            <div class="driver-rating">
                                <span class="star">★</span>
                                <span class="rating">4.8</span>
                                <span class="reviews">(86 reviews)</span>
                            </div>
                            <p class="driver-description">
                                Friendly and reliable driver with a spacious van, perfect for families or large groups. Safety is my priority.
                            </p>
                            <button class="select-driver-btn">Select Driver</button>
                        </div>
                    </div>
                    <div class="driver-card">
                        <div class="driver-avatar">
                            <img src="http://localhost:3845/assets/dc44eecc642e9be7da61c724fd4b46f41efac2cd.png" alt="Kumar Fernando">
                        </div>
                        <div class="driver-info">
                            <h3 class="driver-name">Kumar Fernando</h3>
                            <div class="driver-rating">
                                <span class="star">★</span>
                                <span class="rating">5.0</span>
                                <span class="reviews">(210 reviews)</span>
                            </div>
                            <p class="driver-description">
                                Your local guide on wheels! I'll not only drive you but also share stories about our beautiful country.
                            </p>
                            <button class="select-driver-btn">Select Driver</button>
                        </div>
                    </div>
                    <div class="driver-card">
                        <div class="driver-avatar">
                            <img src="images/driver-jane-smith-198.png" alt="Maria Rodriguez">
                        </div>
                        <div class="driver-info">
                            <h3 class="driver-name">Maria Rodriguez</h3>
                            <div class="driver-rating">
                                <span class="star">★</span>
                                <span class="rating">4.7</span>
                                <span class="reviews">(156 reviews)</span>
                            </div>
                            <p class="driver-description">
                                Multilingual guide specializing in cultural tours. Drives a luxury SUV and knows the best local restaurants and shopping spots.
                            </p>
                            <button class="select-driver-btn">Select Driver</button>
                        </div>
                    </div>
                </div>
                <button class="see-more-btn" onclick="window.location.href='ReviewedDriver.html'">
                    See More
                    <img src="http://localhost:3845/assets/34fa6f5128ca8c6bd1292e82ea9ddd3a9d87a8e4.svg" alt="Arrow" class="arrow-icon">
                </button>
            </div>
        </section>
  </main>

  <?php renderComponent('inc','footer',[]); ?>

    <script src="driver.js"></script>
</body>
</html>
