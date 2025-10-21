<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($data['name']) ? $data['name'] : 'Destination'; ?> | Sri Lanka Travel</title>
    <link rel="stylesheet" href="public/css/destination-details.css">
    <link rel="stylesheet" href="public/css/popup.css">
    <link rel="stylesheet" href="public/css/tripingoo-footer.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include dirname(__DIR__, 2) . '/public/components/int/navigation/navigation.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <div class="rating">
                        <i class="fa-solid fa-location-dot" style="color:#000"></i>
                        <h1><?php echo isset($data['name']) ? $data['name'] : 'Destination'; ?></h1>
                    </div>
                    <div class="rating">
                        <span class="rating-score"><?php echo isset($data['rating']) ? $data['rating'] : '4.8'; ?></span>
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <span class="review-count">(<?php echo isset($data['review_count']) ? $data['review_count'] : '450'; ?> reviews)</span>
                        <img src="assets/recommend.jpg" alt="Recommended" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;">
                        <span class="recommended-text">recommended by 95% travellers</span>
                        <button class="save-to-trip-btn" id="saveToTripBtn">
                            <i class="fas fa-heart"></i>
                            Save to Trip
                        </button>
                    </div>
                </div>
                <div class="hero-images">
                    <div class="images-grid">
                        <div class="main-image">
                            <img src="<?php echo isset($data['image']) ? $data['image'] : 'assets/sigiriya.jpg'; ?>" alt="<?php echo isset($data['name']) ? $data['name'] : 'Destination'; ?>">
                        </div>
                        <div class="side-images column">
                            <img src="assets/Sigiriya Frescoes.jpg" alt="Sigiriya Frescoes" class="side-image">
                            <img src="assets/Travellers at Sigiriya.jpg" alt="Travellers at Sigiriya" class="side-image">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Navigation Tabs -->
    <section class="nav-tabs">
        <div class="container">
            <div class="tabs">
                <a href="#overview" class="tab active">Overview</a>
                <a href="#details" class="tab">Details</a>
                <a href="#itinerary" class="tab">Itinerary</a>
                <a href="#guides" class="tab">Guides</a>
                <a href="#reviews" class="tab">Reviews</a>
            </div>
        </div>
    </section>

    <!-- Overview Section -->
    <section id="overview" class="section">
        <div class="container">
            <h2>Overview</h2>
            <div class="overview-content">
               <div class="itinerary-text">
                    <p>Sigiriya, also known as the Lion Rock Fortress, is one of Sri Lanka’s most iconic landmarks and a UNESCO World Heritage Site. Rising nearly 200 meters (660 feet) above the surrounding plains, this ancient rock fortress in Matale District was built by King Kashyapa (477–495 AD). The site combines natural beauty, engineering brilliance, and artistic excellence..</p>
                    <p>Visitors can explore the remains of the royal palace at the summit, beautifully landscaped water gardens, and stunning frescoes that have survived for over 1,500 years. The Mirror Wall, once polished so finely that the king could see his reflection, still bears ancient inscriptions left by visitors centuries ago.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Details Section -->
    <section id="details" class="section">
        <div class="container">
            <h2>Details</h2>
            <div class="details-content">
                <div class="details-list">
                    <div class="detail-item">
                        <span class="detail-label">Location:</span>
                        <span class="detail-value"><?php echo isset($data['location']) ? $data['location'] : 'Sri Lanka'; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Built By:</span>
                        <span class="detail-value">King Kashyapa (477–495 AD)</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Height:</span>
                        <span class="detail-value">200 meters (660 ft)</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Highlights:</span>
                        <span class="detail-value">Water Gardens, Boulder Gardens, and Terraced Gardens</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"></span>
                        <span class="detail-value">Frescoes & Mirror Wall</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"></span>
                        <span class="detail-value">Lion's Paw Entrance</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"></span>
                        <span class="detail-value">Summit Palace Ruins & breathtaking views</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Best Time to Visit:</span>
                        <span class="detail-value">Early morning (7–9 AM) or late afternoon (after 3 PM) to avoid midday heat</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Duration of Visit:</span>
                        <span class="detail-value">2–3 hours total (climb ~45–60 mins)</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Nearby Attractions:</span>
                        <span class="detail-value">Pidurangala Rock, Dambulla Cave Temple, Minneriya National Park</span>
                    </div>
                </div>
                <div class="map-container">
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Itinerary Section -->
    <section id="itinerary" class="section">
        <div class="container">
            <h2>Itinerary</h2>
            <div class="itinerary-content">
                <div class="itinerary-text">
                    <p>Start your journey through the beautiful landscaped gardens at the base, where you'll walk past the water gardens, boulder gardens, and terraced gardens. As you move higher, you'll encounter the world-famous Sigiriya frescoes and the mysterious Mirror Wall, which still carries ancient inscriptions.</p>
                    <p>Climbing further brings you to the impressive Lion's Paw Terrace, the gateway to the summit. From here, the final ascent takes you to the royal palace ruins at the top, where breathtaking 360° views of the jungle and villages stretch as far as the eye can see.</p>
                </div>
                <div class="itinerary-image">
                    <img src="assets/map.png" alt="Sigiriya Itinerary Map">
                </div>
            </div>
        </div>
    </section>

    <!-- Guides Section -->
    <section id="guides" class="section">
        <div class="container">
            <h2>Guides</h2>
            <div class="guides-content">
                <div class="guides-cards">
                    <div class="guide-card">
                        <div class="guide-avatar">
                            <img src="assets/john.jpg" alt="John Doe">
                        </div>
                        <h3>John Doe</h3>
                        <div class="guide-rating">
                            <i class="fas fa-star"></i>
                            <span>4.9 (124 reviews)</span>
                        </div>
                        <p>Experienced driver with a comfortable car, ensuring a smooth and safe journey. Fluent in English.</p>
                        <button class="btn-primary">Select Driver</button>
                    </div>
                    <div class="guide-card">
                        <div class="guide-avatar">
                            <img src="assets/jane.jpg" alt="Jane Smith">
                        </div>
                        <h3>Jane Smith</h3>
                        <div class="guide-rating">
                            <i class="fas fa-star"></i>
                            <span>4.9 (98 reviews)</span>
                        </div>
                        <p>Friendly and reliable driver with a spacious vehicle. I am committed to making your safety is my priority.</p>
                        <button class="btn-primary">Select Driver</button>
                    </div>
                    <div class="guide-card">
                        <div class="guide-avatar">
                            <img src="assets/mike.jpg" alt="Mike Johnson">
                        </div>
                        <h3>Mike Johnson</h3>
                        <div class="guide-rating">
                            <i class="fas fa-star"></i>
                            <span>4.8 (156 reviews)</span>
                        </div>
                        <p>Professional guide with 10+ years of experience. Expert knowledge of Sigiriya's history and culture.</p>
                        <button class="btn-primary">Select Guide</button>
                    </div>
                    <div class="guide-card">
                        <div class="guide-avatar">
                            <img src="assets/sarah.jpg" alt="Sarah Lee">
                        </div>
                        <h3>Sarah Lee</h3>
                        <div class="guide-rating">
                            <i class="fas fa-star"></i>
                            <span>4.9 (89 reviews)</span>
                        </div>
                        <p>Certified archaeologist specializing in ancient Sri Lankan history. Provides in-depth cultural insights.</p>
                        <button class="btn-primary">Select Guide</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Reviews Section -->
    <section id="reviews" class="section">
        <div class="container">
            <h2>Reviews</h2>
            <div class="reviews-content">
                <div class="overall-rating">
                    <div class="rating-summary">
                        <span class="rating-number">4.6</span>
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <span class="total-reviews">[1485]</span>
                    </div>
                </div>
                <div class="rating-breakdown">
                    <div class="rating-bar">
                        <span class="rating-label">Excellent</span>
                        <div class="bar-container">
                            <div class="bar-fill" style="width: 70%"></div>
                        </div>
                        <span class="rating-count">724</span>
                    </div>
                    <div class="rating-bar">
                        <span class="rating-label">Very good</span>
                        <div class="bar-container">
                            <div class="bar-fill" style="width: 29%"></div>
                        </div>
                        <span class="rating-count">450</span>
                    </div>
                    <div class="rating-bar">
                        <span class="rating-label">Average</span>
                        <div class="bar-container">
                            <div class="bar-fill" style="width: 8.5%"></div>
                        </div>
                        <span class="rating-count">250</span>
                    </div>
                    <div class="rating-bar">
                        <span class="rating-label">Poor</span>
                        <div class="bar-container">
                            <div class="bar-fill" style="width: 9%"></div>
                        </div>
                        <span class="rating-count">61</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include dirname(__DIR__, 2) . '/public/components/int/footer/footer.php'; ?>

    <!-- Save to Trip Popup -->
    <div class="overlay" id="popupOverlay">
      <!-- Main popup container -->
      <div class="popup-container">
        <!-- Header -->
        <div class="popup-header">
          <div class="header-left">
            <i class="fas fa-briefcase"></i>
            <span>My trips</span>
          </div>
          <h2>Save to a trip</h2>
          <button class="close-btn" id="closeBtn">&times;</button>
        </div>

        <!-- Content -->
        <div class="popup-content">
          <!-- Trip Cards -->
          <div class="trip-cards-container">
            <div class="trip-card" data-trip-id="1" data-trip-name="summer in srilanka">
              <div class="trip-image">
                <img src="https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=80&h=80&fit=crop&crop=center" alt="Summer in Sri Lanka">
              </div>
              <div class="trip-details">
                <h4 class="trip-title">summer in srilanka</h4>
                <div class="trip-dates">
                  <i class="fas fa-calendar"></i>
                  <span>Dec 1, 2025 → Dec 23, 2025</span>
                </div>
                <div class="trip-location">
                  <i class="fas fa-map-marker-alt"></i>
                  <span>Sri Lanka, Sigiriya</span>
                </div>
              </div>
            </div>

            <div class="trip-card" data-trip-id="2" data-trip-name="ransara">
              <div class="trip-image">
                <img src="https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=80&h=80&fit=crop&crop=center" alt="Ransara">
              </div>
              <div class="trip-details">
                <h4 class="trip-title">ransara</h4>
                <div class="trip-dates">
                  <i class="fas fa-calendar"></i>
                  <span>Aug 1, 2025 → Aug 4, 2025</span>
                </div>
                <div class="trip-location">
                  <i class="fas fa-map-marker-alt"></i>
                  <span>Sri Lanka, United States, Italy</span>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Trip Details Popup -->
    <div class="overlay" id="tripDetailsOverlay">
      <div class="trip-details-container">
        <!-- Dark Header -->
        <div class="trip-details-header">
          <button class="back-btn" id="backBtn">&times;</button>
          <h2 id="tripDetailsTitle">summer in srilanka</h2>
        </div>

        <!-- Content -->
        <div class="trip-details-content">
          <div class="saved-items-count">
            <span id="savedItemsCount">1 item saved</span>
          </div>

          <!-- Saved Items -->
          <div class="saved-items-container" id="savedItemsContainer">
            <!-- Auto-saved item will be added here -->
          </div>
        </div>
      </div>
    </div>

    <!-- Google Maps Script -->
    <script src="public/js/destination-details.js"></script>
    <script src="public/js/popup.js"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap"></script>
</body>
</html>