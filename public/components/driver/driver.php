<?php
// DEV only: show PHP errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define APP_ROOT constant
define('APP_ROOT', dirname(__DIR__, 3) . '/app');

// Include database connection
require_once 'database.php';

// Get data from database
$trendingDrivers = getTrendingDrivers(); // Get ALL trending drivers automatically
$licensedDrivers = getAllDrivers(6);
$reviewedDrivers = getReviewedDrivers(6);
$touristDrivers = getTouristDrivers(6);

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
                    <?php foreach($trendingDrivers as $driver): ?>
                    <div class="driver-card">
                        <?php if($driver['badge_type'] !== 'none'): ?>
                            <div class="driver-badge <?php echo $driver['badge_type']; ?>">
                                <?php echo ($driver['badge_type'] === 'top-rated') ? 'Top Rated' : 'Most Booked'; ?>
                            </div>
                        <?php endif; ?>
                        <div class="driver-avatar">
                            <img src="<?php echo htmlspecialchars($driver['image_url']); ?>" alt="<?php echo htmlspecialchars($driver['name']); ?>">
                        </div>
                        <div class="driver-info">
                            <h3 class="driver-name"><?php echo htmlspecialchars($driver['name']); ?></h3>
                            <div class="driver-rating">
                                <span class="star">★</span>
                                <span class="rating"><?php echo $driver['rating']; ?></span>
                                <span class="reviews">(<?php echo $driver['total_reviews']; ?> reviews)</span>
                            </div>
                            <p class="driver-description">
                                <?php echo htmlspecialchars($driver['description']); ?>
                            </p>
                            <button class="select-driver-btn">Select Driver</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

    <!-- All Drivers Section -->
    <section class="drivers-section">
        <h2 class="section-title">All Drivers</h2>
        
        <!-- Licensed Drivers Subsection -->
        <h3 class="subsection-title">Licensed Drivers</h3>
        <div class="drivers-container-grid">
            <?php foreach($licensedDrivers as $driver): ?>
            <div class="driver-card">
                <?php if($driver['badge_type'] !== 'none'): ?>
                    <div class="driver-badge <?php echo $driver['badge_type']; ?>">
                        <?php echo ($driver['badge_type'] === 'top-rated') ? 'Top Rated' : 'Most Booked'; ?>
                    </div>
                <?php endif; ?>
                <div class="driver-avatar">
                    <img src="<?php echo htmlspecialchars($driver['image_url']); ?>" alt="<?php echo htmlspecialchars($driver['name']); ?>">
                </div>
                <div class="driver-info">
                    <h3 class="driver-name"><?php echo htmlspecialchars($driver['name']); ?></h3>
                    <div class="driver-rating">
                        <span class="star">★</span>
                        <span class="rating"><?php echo $driver['rating']; ?></span>
                        <span class="reviews">(<?php echo $driver['total_reviews']; ?> reviews)</span>
                    </div>
                    <p class="driver-description">
                        <?php echo htmlspecialchars($driver['description']); ?>
                    </p>
                    <button class="select-driver-btn">Select Driver</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
                <button class="see-more-btn" onclick="window.location.href='licensedDrivers/licensedDriver.php'">
            See More
            <img src="../driver/images/arrow-2-225.svg" alt="Arrow" class="arrow-icon">
        </button>

        <!-- Reviewed Drivers Subsection -->
        <h3 class="subsection-title">Reviewed Drivers</h3>
        <div class="drivers-container-grid">
            <?php foreach($reviewedDrivers as $driver): ?>
            <div class="driver-card">
                <?php if($driver['badge_type'] !== 'none'): ?>
                    <div class="driver-badge <?php echo $driver['badge_type']; ?>">
                        <?php echo ($driver['badge_type'] === 'top-rated') ? 'Top Rated' : 'Most Booked'; ?>
                    </div>
                <?php endif; ?>
                <div class="driver-avatar">
                    <img src="<?php echo htmlspecialchars($driver['image_url']); ?>" alt="<?php echo htmlspecialchars($driver['name']); ?>">
                </div>
                <div class="driver-info">
                    <h3 class="driver-name"><?php echo htmlspecialchars($driver['name']); ?></h3>
                    <div class="driver-rating">
                        <span class="star">★</span>
                        <span class="rating"><?php echo $driver['rating']; ?></span>
                        <span class="reviews">(<?php echo $driver['total_reviews']; ?> reviews)</span>
                    </div>
                    <p class="driver-description">
                        <?php echo htmlspecialchars($driver['description']); ?>
                    </p>
                    <button class="select-driver-btn">Select Driver</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <button class="see-more-btn" onclick="window.location.href='reviewedDrivers/reviewedDriver.php'">
            See More
            <img src="../driver/images/arrow-2-225.svg" alt="Arrow" class="arrow-icon">
        </button>

        <!-- Tourist Drivers Subsection -->
        <h3 class="subsection-title">Tourist Drivers</h3>
        <div class="drivers-container-grid">
            <?php foreach($touristDrivers as $driver): ?>
            <div class="driver-card">
                <?php if($driver['badge_type'] !== 'none'): ?>
                    <div class="driver-badge <?php echo $driver['badge_type']; ?>">
                        <?php echo ($driver['badge_type'] === 'top-rated') ? 'Top Rated' : 'Most Booked'; ?>
                    </div>
                <?php endif; ?>
                <div class="driver-avatar">
                    <img src="<?php echo htmlspecialchars($driver['image_url']); ?>" alt="<?php echo htmlspecialchars($driver['name']); ?>">
                </div>
                <div class="driver-info">
                    <h3 class="driver-name"><?php echo htmlspecialchars($driver['name']); ?></h3>
                    <div class="driver-rating">
                        <span class="star">★</span>
                        <span class="rating"><?php echo $driver['rating']; ?></span>
                        <span class="reviews">(<?php echo $driver['total_reviews']; ?> reviews)</span>
                    </div>
                    <p class="driver-description">
                        <?php echo htmlspecialchars($driver['description']); ?>
                    </p>
                    <button class="select-driver-btn">Select Driver</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <button class="see-more-btn" onclick="window.location.href='TouristDriver.html'">
            See More
            <img src="../driver/images/arrow-2-225.svg" alt="Arrow" class="arrow-icon">
        </button>
    </section>
  </main>

  <?php renderComponent('inc','footer',[]); ?>

    <script src="driver.js"></script>
</body>
</html>
