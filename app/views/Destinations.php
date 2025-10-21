<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tripgooo - Destinations</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/popup.css">
  <link rel="stylesheet" href="css/tripingoo-footer.css">
</head>
<body>
  <?php include dirname(__DIR__, 2) . '/public/components/int/navigation/navigation.php'; ?>

  <section class="hero">
    <img class="hero__bg" alt="Sri Lanka beach" src="assets/beach.jpg">
    <div class="hero__search" role="search">
      <img class="search__icon" alt="search" src="assets/search.png">
      <input id="searchInput" type="text" placeholder="Search by destination" aria-label="Search by destination">
    </div>
  </section>

  <main class="content">
    <h2 class="section__title">Must-Dos in Sri Lanka</h2>
    <div class="scroll-container">
      <button class="scroll-arrow scroll-left" aria-label="Scroll left">
        <i class="fas fa-chevron-left"></i>
      </button>
      <div class="card-row" data-section="must-dos">
        <article class="card" data-destination-id="1" data-destination-name="sigiriya-rock-fortress">
          <a href="index.php?url=DestinationDetails/index/1" class="card-link">
            <img class="card__image" alt="Sigiriya" src="assets/sigiriya.jpg">
            <div class="card__badge">Matale</div>
            <h3 class="card__title">Sigiriya Rock Fortress</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
        <article class="card" data-destination-id="2" data-destination-name="galle-dutch-fort">
          <a href="index.php?url=DestinationDetails/index/2" class="card-link">
            <img class="card__image" alt="Galle Fort" src="assets/galle.jpg">
            <div class="card__badge">Galle</div>
            <h3 class="card__title">Galle Dutch Fort</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
        <article class="card" data-destination-id="3" data-destination-name="secret-beach-mirissa">
          <a href="index.php?url=DestinationDetails/index/3" class="card-link">
            <img class="card__image" alt="Secret Beach" src="assets/mirissa.jpg">
            <div class="card__badge">Matara</div>
            <h3 class="card__title">SECRET BEACH MIRISSA</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
        <article class="card" data-destination-id="1" data-destination-name="sigiriya-rock-fortress">
          <a href="index.php?url=DestinationDetails/index/1" class="card-link">
            <img class="card__image" alt="Sigiriya" src="assets/sigiriya.jpg">
            <div class="card__badge">Matale</div>
            <h3 class="card__title">Sigiriya Rock Fortress</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
        <article class="card" data-destination-id="2" data-destination-name="galle-dutch-fort">
          <a href="index.php?url=DestinationDetails/index/2" class="card-link">
            <img class="card__image" alt="Galle Fort" src="assets/galle.jpg">
            <div class="card__badge">Galle</div>
            <h3 class="card__title">Galle Dutch Fort</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
        <article class="card" data-destination-id="3" data-destination-name="secret-beach-mirissa">
          <a href="index.php?url=DestinationDetails/index/3" class="card-link">
            <img class="card__image" alt="Secret Beach" src="assets/mirissa.jpg">
            <div class="card__badge">Matara</div>
            <h3 class="card__title">SECRET BEACH MIRISSA</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
      </div>
      <button class="scroll-arrow scroll-right" aria-label="Scroll right">
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>

    <h2 class="section__title">Nature & Adventure</h2>
    <div class="scroll-container">
      <button class="scroll-arrow scroll-left" aria-label="Scroll left">
        <i class="fas fa-chevron-left"></i>
      </button>
      <div class="card-row" data-section="nature">
        <article class="card" data-destination-id="1" data-destination-name="sigiriya-rock-fortress">
          <a href="index.php?url=DestinationDetails/index/1" class="card-link">
            <img class="card__image" alt="Sigiriya" src="assets/sigiriya.jpg">
            <div class="card__badge">Matale</div>
            <h3 class="card__title">Sigiriya Rock Fortress</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
        <article class="card" data-destination-id="2" data-destination-name="galle-dutch-fort">
          <a href="index.php?url=DestinationDetails/index/2" class="card-link">
            <img class="card__image" alt="Galle Fort" src="assets/galle.jpg">
            <div class="card__badge">Galle</div>
            <h3 class="card__title">Galle Dutch Fort</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
        <article class="card" data-destination-id="3" data-destination-name="secret-beach-mirissa">
          <a href="index.php?url=DestinationDetails/index/3" class="card-link">
            <img class="card__image" alt="Secret Beach" src="assets/mirissa.jpg">
            <div class="card__badge">Matara</div>
            <h3 class="card__title">SECRET BEACH MIRISSA</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
        <article class="card" data-destination-id="1" data-destination-name="sigiriya-rock-fortress">
          <a href="index.php?url=DestinationDetails/index/1" class="card-link">
            <img class="card__image" alt="Sigiriya" src="assets/sigiriya.jpg">
            <div class="card__badge">Matale</div>
            <h3 class="card__title">Sigiriya Rock Fortress</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
        <article class="card" data-destination-id="2" data-destination-name="galle-dutch-fort">
          <a href="index.php?url=DestinationDetails/index/2" class="card-link">
            <img class="card__image" alt="Galle Fort" src="assets/galle.jpg">
            <div class="card__badge">Galle</div>
            <h3 class="card__title">Galle Dutch Fort</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
        <article class="card" data-destination-id="3" data-destination-name="secret-beach-mirissa">
          <a href="index.php?url=DestinationDetails/index/3" class="card-link">
            <img class="card__image" alt="Secret Beach" src="assets/mirissa.jpg">
            <div class="card__badge">Matara</div>
            <h3 class="card__title">SECRET BEACH MIRISSA</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
      </div>
      <button class="scroll-arrow scroll-right" aria-label="Scroll right">
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>

    <h2 class="section__title">Culture & Heritage</h2>
    <div class="scroll-container">
      <button class="scroll-arrow scroll-left" aria-label="Scroll left">
        <i class="fas fa-chevron-left"></i>
      </button>
      <div class="card-row" data-section="culture">
        <article class="card" data-destination-id="1" data-destination-name="sigiriya-rock-fortress">
          <a href="index.php?url=DestinationDetails/index/1" class="card-link">
            <img class="card__image" alt="Sigiriya" src="assets/sigiriya.jpg">
            <div class="card__badge">Matale</div>
            <h3 class="card__title">Sigiriya Rock Fortress</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
        <article class="card" data-destination-id="2" data-destination-name="galle-dutch-fort">
          <a href="index.php?url=DestinationDetails/index/2" class="card-link">
            <img class="card__image" alt="Galle Fort" src="assets/galle.jpg">
            <div class="card__badge">Galle</div>
            <h3 class="card__title">Galle Dutch Fort</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
        <article class="card" data-destination-id="3" data-destination-name="secret-beach-mirissa">
          <a href="index.php?url=DestinationDetails/index/3" class="card-link">
            <img class="card__image" alt="Secret Beach" src="assets/mirissa.jpg">
            <div class="card__badge">Matara</div>
            <h3 class="card__title">SECRET BEACH MIRISSA</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
        <article class="card" data-destination-id="1" data-destination-name="sigiriya-rock-fortress">
          <a href="index.php?url=DestinationDetails/index/1" class="card-link">
            <img class="card__image" alt="Sigiriya" src="assets/sigiriya.jpg">
            <div class="card__badge">Matale</div>
            <h3 class="card__title">Sigiriya Rock Fortress</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
        <article class="card">
            <img class="card__image" alt="Galle Fort" src="assets/galle.jpg">
            <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
          <div class="card__badge">Galle</div>
          <h3 class="card__title">Galle Dutch Fort</h3>
          <div class="rating">
            <span class="score">4.8</span>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-regular fa-star"></i>
            <span class="count">(450)</span>
          </div>        
        </article>
        <article class="card" data-destination-id="3" data-destination-name="secret-beach-mirissa">
          <a href="index.php?url=DestinationDetails/index/3" class="card-link">
            <img class="card__image" alt="Secret Beach" src="assets/mirissa.jpg">
            <div class="card__badge">Matara</div>
            <h3 class="card__title">SECRET BEACH MIRISSA</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
      </div>
      <button class="scroll-arrow scroll-right" aria-label="Scroll right">
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>

    <h2 class="section__title">Relaxation & Leisure</h2>
    <div class="scroll-container">
      <button class="scroll-arrow scroll-left" aria-label="Scroll left">
        <i class="fas fa-chevron-left"></i>
      </button>
      <div class="card-row" data-section="relaxation">
        <article class="card" data-destination-id="1" data-destination-name="sigiriya-rock-fortress">
          <a href="index.php?url=DestinationDetails/index/1" class="card-link">
            <img class="card__image" alt="Sigiriya" src="assets/sigiriya.jpg">
            <div class="card__badge">Matale</div>
            <h3 class="card__title">Sigiriya Rock Fortress</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
        <article class="card" data-destination-id="2" data-destination-name="galle-dutch-fort">
          <a href="index.php?url=DestinationDetails/index/2" class="card-link">
            <img class="card__image" alt="Galle Fort" src="assets/galle.jpg">
            <div class="card__badge">Galle</div>
            <h3 class="card__title">Galle Dutch Fort</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
        <article class="card" data-destination-id="3" data-destination-name="secret-beach-mirissa">
          <a href="index.php?url=DestinationDetails/index/3" class="card-link">
            <img class="card__image" alt="Secret Beach" src="assets/mirissa.jpg">
            <div class="card__badge">Matara</div>
            <h3 class="card__title">SECRET BEACH MIRISSA</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
        <article class="card" data-destination-id="1" data-destination-name="sigiriya-rock-fortress">
          <a href="index.php?url=DestinationDetails/index/1" class="card-link">
            <img class="card__image" alt="Sigiriya" src="assets/sigiriya.jpg">
            <div class="card__badge">Matale</div>
            <h3 class="card__title">Sigiriya Rock Fortress</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
        <article class="card" data-destination-id="2" data-destination-name="galle-dutch-fort">
          <a href="index.php?url=DestinationDetails/index/2" class="card-link">
            <img class="card__image" alt="Galle Fort" src="assets/galle.jpg">
            <div class="card__badge">Galle</div>
            <h3 class="card__title">Galle Dutch Fort</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
        <article class="card" data-destination-id="3" data-destination-name="secret-beach-mirissa">
          <a href="index.php?url=DestinationDetails/index/3" class="card-link">
            <img class="card__image" alt="Secret Beach" src="assets/mirissa.jpg">
            <div class="card__badge">Matara</div>
            <h3 class="card__title">SECRET BEACH MIRISSA</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
      </div>
      <button class="scroll-arrow scroll-right" aria-label="Scroll right">
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>

    <h2 class="section__title">Entertainment & Activities</h2>
    <div class="scroll-container">
      <button class="scroll-arrow scroll-left" aria-label="Scroll left">
        <i class="fas fa-chevron-left"></i>
      </button>
      <div class="card-row" data-section="entertainment">
        <article class="card" data-destination-id="1" data-destination-name="sigiriya-rock-fortress">
          <a href="index.php?url=DestinationDetails/index/1" class="card-link">
            <img class="card__image" alt="Sigiriya" src="assets/sigiriya.jpg">
            <div class="card__badge">Matale</div>
            <h3 class="card__title">Sigiriya Rock Fortress</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
        <article class="card" data-destination-id="2" data-destination-name="galle-dutch-fort">
          <a href="index.php?url=DestinationDetails/index/2" class="card-link">
            <img class="card__image" alt="Galle Fort" src="assets/galle.jpg">
            <div class="card__badge">Galle</div>
            <h3 class="card__title">Galle Dutch Fort</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
        <article class="card">
            <img class="card__image" alt="Secret Beach" src="assets/mirissa.jpg">
            <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
          <div class="card__badge">Matara</div>
          <h3 class="card__title">SECRET BEACH MIRISSA</h3>
          <div class="rating">
            <span class="score">4.8</span>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-regular fa-star"></i>
            <span class="count">(450)</span>
          </div>        
        </article>
        <article class="card">
          <img class="card__image" alt="Sigiriya" src="assets/sigiriya.jpg">
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
          <div class="card__badge">Matale</div>
          <h3 class="card__title">Sigiriya Rock Fortress</h3>
          <div class="rating">
            <span class="score">4.8</span>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-regular fa-star"></i>
            <span class="count">(450)</span>
          </div>        
        </article>
        <article class="card" data-destination-id="2" data-destination-name="galle-dutch-fort">
          <a href="index.php?url=DestinationDetails/index/2" class="card-link">
            <img class="card__image" alt="Galle Fort" src="assets/galle.jpg">
            <div class="card__badge">Galle</div>
            <h3 class="card__title">Galle Dutch Fort</h3>
            <div class="rating">
              <span class="score">4.8</span>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-regular fa-star"></i>
              <span class="count">(450)</span>
            </div>
          </a>
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
        </article>
        <article class="card">
          <img class="card__image" alt="Secret Beach" src="assets/mirissa.JPG">
          <button class="fav-btn"><i class="fa-regular fa-heart"></i></button>
          <div class="card__badge">Matara</div>
          <h3 class="card__title">SECRET BEACH MIRISSA</h3>
          <div class="rating">
            <span class="score">4.8</span>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-regular fa-star"></i>
            <span class="count">(450)</span>
          </div>        
        </article>
      </div>
      <button class="scroll-arrow scroll-right" aria-label="Scroll right">
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>
  </main>

  <?php include __DIR__ ? dirname(__DIR__, 2) . '/public/components/int/footer/footer.php' : 'public/components/int/footer/footer.php'; ?>

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

        <!-- Create Trip Button -->
        <div class="create-trip-section">
          <button class="create-trip-btn">
            <i class="fas fa-plus"></i>
            Create a trip
          </button>
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

  <script src="js/script.js"></script>
  <script src="js/popup.js"></script>
</body>
</html>