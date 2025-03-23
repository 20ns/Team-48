<?php
session_start(); // Start the session to access the current user's session ID

// Database connection details
$servername = "localhost";
$username = "cs2team48";
$password = "9ZReO56gOBkKTcr";
$dbname = "cs2team48_db";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];

    
    $query = "SELECT * FROM shoppingSession WHERE userID = ?"; 
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    
    if ($result->num_rows > 0) {
        $loggedIn = true;
    } else {
        $loggedIn = false;
    }

    $stmt->close();
} else {
    $loggedIn = false;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!isset($_SESSION['userID'])) {
        header("Location: login.php");
        exit();
    }


    $userId = $_SESSION['userID'];
    $rating = (int)$_POST['rating'];
    $comment = htmlspecialchars($_POST['comment']);
    $isAnonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    $productId = null; 

    $stmt = $conn->prepare("INSERT INTO reviews (user_id, rating, comment, is_anonymous) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iisi", $userId, $rating, $comment, $isAnonymous);
    $stmt->execute();
    $stmt->close();
}

$reviews = $conn->query("
    SELECT r.*, u.name 
    FROM reviews r
    LEFT JOIN users u ON r.user_id = u.id
    ORDER BY r.created_at DESC
    LIMIT 5
");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- 
    - primary meta tags
  -->
  <title>Peri Palace - Discover Delicious Food</title>
  <meta name="title" content="Peri Palace - Sensational Flavours">
  <meta name="description" content="Peri Palace offers delicious peri-peri dishes, grilled to perfection. Discover the taste of sensational flavours!">
  <!-- 
    - google font link
  -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="./assets/css/style.css">
  <link rel="preload" as="image" href="./assets/images/BEEFhome.jpg">
  <link rel="preload" as="image" href="./assets/images/FLAVOUR.jpg">
  <link rel="preload" as="image" href="./assets/images/chefNoodles.jpg">


</head>
<body id="top">
<div class="preload" data-preaload>
    <div class="circle"></div>
    <p class="text">Peri Palace</p>
  </div>
<div class="topbar">
    <div class="container">
      <address class="topbar-item">
        <div class="icon">
          <ion-icon name="location-outline" aria-hidden="true"></ion-icon>
        </div>
        <span class="span">
          Corporate Street, Stratford Rd, Liverpool 8976, UK
        </span>
      </address>
   <div class="separator"></div>
      <div class="topbar-item item-2">
        <div class="icon">
          <ion-icon name="time-outline" aria-hidden="true"></ion-icon>
        </div>
        <span class="span">Open Daily 11am-11pm</span>
      </div>
      <a href="tel:+2 342 563 89760" class="topbar-item link">
        <div class="icon">
          <ion-icon name="call-outline" aria-hidden="true"></ion-icon>
        </div>
        <span class="span">+2 342 563 89760</span>
      </a>
      <div class="separator"></div>
      <a href="mailto:Peri-booking@restaurant.com" class="topbar-item link">
        <div class="icon">
          <ion-icon name="mail-outline" aria-hidden="true"></ion-icon>
        </div>
        <span class="span">Peri-booking@restaurant.com</span>
      </a>
    </div>
  </div>
  <!-- 
    - #HEADER
  -->
  <header class="header" data-header>
    <div class="container">
      <a href="#" class="logo">
        <img src="./assets/images/logoWhite.png" width="160" height="50" alt="Peri Palace - Home">
      </a>
      <nav class="navbar" data-navbar>
        <button class="close-btn" aria-label="close menu" data-nav-toggler>
          <ion-icon name="close-outline" aria-hidden="true"></ion-icon>
        </button>
   <a href="#index.php" class="logo">
          <img src="./assets/images/logoWhite.png" width="160" height="50" alt="Peri Palace - Home">
        </a>
        <ul class="navbar-list">
          <li class="navbar-item">
            <a href="#home" class="navbar-link hover-underline active">
              <div class="separator"></div>
              <span class="span">Home</span>
            </a>
          </li>
          <li class="navbar-item">
            <a href="#menu" class="navbar-link hover-underline">
              <div class="separator"></div>
              <span class="span">Menus</span>
            </a>
          </li>
          <li class="navbar-item">
            <a href="#about" class="navbar-link hover-underline">
              <div class="separator"></div>
              <span class="span">About Us</span>
            </a>
          </li>
          <li class="navbar-item">
            <a href="basket.php" class="navbar-link hover-underline">
              <div class="separator"></div>
              <span class="span">Basket</span>
            </a>
          </li>
          <li class="navbar-item">
            <a href="#contact" class="navbar-link hover-underline">
              <div class="separator"></div>
              <span class="span">Contact</span>
            </a>
          </li>
          <?php if (isset($_SESSION['userID'])): ?>
            <a href="my_orders.php" class="navbar-link">My Orders</a>

            <?php endif; ?>
        </ul>
        <div class="text-center">
          <p class="headline-1 navbar-title">Visit Us</p>
          <address class="body-4">
            Corporate Street, Stratford Rd, <br>
            Liverpool 8976, UK
          </address>
          <p class="body-4 nav-text">Open: 11 am - 11pm</p>
          <a href="mailto:Peri-booking@palace.com" class="body-4 sidebar-link">Peri-booking@palace.com</a>
          <div class="separator"></div>
          <p class="contact-label">Booking Request</p>
          <a href="tel:+29056745321" class="body-1 contact-number hover-underline">
            +29056745321
          </a>
        </div>
      </nav>
      <?php if ($loggedIn): ?>
    <!-- Account Info Button (show when logged in) -->
    <a href="AccountInfo.php" class="btn btn-primary">
        <span class="text text-1">Account Info</span>
        <span class="text text-2" aria-hidden="true">Account Info</span>
    </a>

<?php else: ?>
    <!-- Log In Button (show when not logged in) -->
    <a href="logIn.php" class="btn btn-secondary">
        <span class="text text-1">Log In</span>
        <span class="text text-2" aria-hidden="true">Log In</span>
    </a>

    <?php endif; ?>
      <button class="nav-open-btn" aria-label="open menu" data-nav-toggler>
        <span class="line line-1"></span>
        <span class="line line-2"></span>
        <span class="line line-3"></span>
      </button>
      <div class="overlay" data-nav-toggler data-overlay></div>
    </div>
  </header>
  <main>
    <article>
      <!-- 
        - #HERO SECTION
      -->
 <section class="hero text-center" aria-label="home" id="home">
        <ul class="hero-slider" data-hero-slider>
 <li class="slider-item active" data-hero-slider-item>
    <div class="slider-bg">
              <img src="./assets/images/BEEFhome.jpg" width="1880" height="950" alt="" class="img-cover">
            </div>
            <p class="label-2 section-subtitle slider-reveal">Traditional & Tasty</p>
            <h1 class="display-1 hero-title slider-reveal">
              Flame-Grilled Perfection <br>
              Fired Up with Peri Peri Flavor!
            </h1>
            
            
          </li>
          <li class="slider-item" data-hero-slider-item>
            <div class="slider-bg">
              <img src="./assets/images/newBackSpice.jpg" width="1880" height="950" alt="" class="img-cover">
            </div>
            <p class="label-2 section-subtitle slider-reveal">A Symphony of Bold Flavors</p>
            <h1 class="display-1 hero-title slider-reveal">
              Inspired By The Seasons <br>
              Perfected For You
            </h1>
            
          </li>
   <li class="slider-item" data-hero-slider-item>
            <div class="slider-bg">
              <img src="./assets/images/chefNoodles.jpg" width="1880" height="950" alt="" class="img-cover">
            </div>
            <p class="label-2 section-subtitle slider-reveal">Simply Irresistible Flavors</p>
            <h1 class="display-1 hero-title slider-reveal">
              Freshly Made,  <br>
              Packed with Love
            </h1>
            
          </li>
        </ul>
        <button class="slider-button prev" aria-label="slide to previous" data-prev-btn>
          <ion-icon name="chevron-back"></ion-icon>
        </button>
        <button class="slider-button next" aria-label="slide to next" data-next-btn>
          <ion-icon name="chevron-forward"></ion-icon>
        </button>
        <?php if ($loggedIn): ?>
    <!-- Account Info Button (show when logged in) -->
    
    <?php else: ?>
        <a href="logIn.php" class="hero-btn has-after">
          <img src="./assets/images/hero-icon.png" width="48" height="48" alt="booking icon">
          <span class="label-2 text-center span">Sign In</span>
        </a>

        <?php endif; ?>
      </section>

      <!-- 
 #SERVICE SECTION -->
<section id="menu" class="section service bg-black-10 text-center" aria-label="service">
    <div class="container">
        <p class="section-subtitle label-2">Where Flavor Comes Alive</p>
        <h2 class="headline-1 section-title">Bringing you the finest flavors</h2>
        <p class="section-text">
            At Peri Palace, our menu caters to all, offering a variety of bold, flavorful dishes infused with our signature fiery touch. Complement your meal with crispy sides like seasoned fries and enjoy refreshing drinks. Finish off with indulgent desserts like gooey brownies. At Peri Palace, every dish is crafted to excite your taste buds and leave you satisfied!
        </p>
        <ul class="grid-list menu-categories">
            <li class="category-item">
                <div class="service-card">
                    <a href="./starters.php" class="has-before hover:shine">
                        <figure class="card-banner img-holder">
                            <img src="./assets/images/garlicBread.jpg" alt="Starters" class="img-cover">
                        </figure>
                    </a>
                    <div class="card-content">
                        <h3 class="title-4 card-title"><a href="./starters.php">Starters</a></h3>
                        <a href="./starters.php" class="btn-text hover-underline label-2">View Menu</a>
                    </div>
                </div>
            </li>
            <li class="category-item">
                <div class="service-card">
                    <a href="./mains.php" class="has-before hover:shine">
                        <figure class="card-banner img-holder">
                            <img src="./assets/images/Chicken.jpg" alt="Mains" class="img-cover">
                        </figure>
                    </a>
                    <div class="card-content">
                        <h3 class="title-4 card-title"><a href="./mains.php">Mains</a></h3>
                        <a href="./mains.php" class="btn-text hover-underline label-2">View Menu</a>
                    </div>
                </div>
            </li>
            <li class="category-item">
                <div class="service-card">
                    <a href="./sides.php" class="has-before hover:shine">
                        <figure class="card-banner img-holder">
                            <img src="./assets/images/corn.jpg" alt="Sides" class="img-cover">
                        </figure>
                    </a>
                    <div class="card-content">
                        <h3 class="title-4 card-title"><a href="./sides.php">Sides</a></h3>
                        <a href="./sides.php" class="btn-text hover-underline label-2">View Menu</a>
                    </div>
                </div>
            </li>
            <li class="category-item">
                <div class="service-card">
                    <a href="./desserts.php" class="has-before hover:shine">
                        <figure class="card-banner img-holder">
                            <img src="./assets/images/powder.jpg" alt="Desserts" class="img-cover">
                        </figure>
                    </a>
                    <div class="card-content">
                        <h3 class="title-4 card-title"><a href="./desserts.php">Desserts</a></h3>
                        <a href="./desserts.php" class="btn-text hover-underline label-2">View Menu</a>
                    </div>
                </div>
            </li>
            <li class="category-item">
                <div class="service-card">
                    <a href="./drinks.php" class="has-before hover:shine">
                        <figure class="card-banner img-holder">
                            <img src="./assets/images/lemonDrink.jpg" alt="Drinks" class="img-cover">
                        </figure>
                    </a>
                    <div class="card-content">
                        <h3 class="title-4 card-title"><a href="./drinks.php">Drinks</a></h3>
                        <a href="./drinks.php" class="btn-text hover-underline label-2">View Menu</a>
                    </div>
                </div>
    </div>
</section>
      <!-- 
        - #ABOUT
      -->
      <section class="section about text-center" aria-labelledby="about-label" id="about">
        <div class="container">
          <div class="about-content">
            <p class="label-2 section-subtitle" id="about-label">Our Story</p>
            <h2 class="headline-1 section-title">Where Bold Flavors Ignite Tradition</h2>
            <p class="section-text">
              Welcome to Peri Palace, where bold flavors and rich traditions come together! Inspired by the fiery peri peri spice, originating from the African Bird’s Eye Chili native to Mozambique and Angola, our dishes bring this vibrant heritage to life. 
              Paired with the smoky charm of charcoal-grilled cooking, every bite is crafted to perfection, delivering a unique blend of zest and warmth. At Peri Palace, we don’t just serve food—we serve an unforgettable journey of flavor.
            </p>
            <div class="contact-label">Book Through Call</div>
            <a href="tel;+29056745321" class="body-1 contact-number hover-underline">+2 905 674 5321</a>
            
          </div>
          <figure class="about-banner">
  <img src="./assets/images/SpiceWomanHand.jpg" width="570" height="570" loading="lazy" alt="about banner"
              class="w-100" data-parallax-item data-parallax-speed="1">
            <div class="abs-img abs-img-1 has-before" data-parallax-item data-parallax-speed="1.75">
   <img src="./assets/images/boat.jpg" width="285" height="285" loading="lazy" alt="" class="w-100">
            </div>
            
          </figure>
         </div>
      </section>

<!-- 
    - #REVIEW SECTION
-->
<section class="section reviews-section text-center" aria-label="reviews">
  <div class="container">
    <h2 class="headline-1 section-title">Customer Reviews</h2>

    <?php if (isset($_SESSION['userID'])): ?>
    <div class="review-form">
      <h3 class="label-2">Write a Review</h3>
      <form method="POST" class="review-form-content">
        <div class="form-group">
          <div class="star-rating">
            <?php for ($i = 5; $i >= 1; $i--): ?>
            <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" required>
            <label for="star<?= $i ?>" class="star">&#9733;</label>
            <?php endfor; ?>
          </div>
        </div>

        <div class="form-group">
          <textarea name="comment" class="input-field" placeholder="Your review..." required></textarea>
        </div>

        <div class="form-group">
          <label class="checkbox-label">
            <input type="checkbox" name="is_anonymous"> Post Anonymously
          </label>
        </div>

        <button type="submit" name="submit_review" class="btn btn-primary">
          <span class="text text-1">Submit Review</span>
          <span class="text text-2">Submit Review</span>
        </button>
      </form>
    </div>
    <?php endif; ?>

    <div class="reviews-list">
      <?php while ($review = $reviews->fetch_assoc()): ?>
      <div class="review-card">
        <div class="review-header">
          <span class="review-author">
            <?= $review['is_anonymous'] ? 'Anonymous' : htmlspecialchars($review['name']) ?>
          </span>
          <div class="review-rating">
            <?= str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']) ?>
          </div>
        </div>
        <p class="review-text"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
        <time class="review-date"><?= date('M j, Y', strtotime($review['created_at'])) ?></time>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>
              
     <!-- 
        - #CONTACT US PAGE
      -->
      <section id="contact" class="contact-us-section">
        <div class="container">
          <div class="form contact-form bg-black-10">
            <form id="contactForm" class="form-left">
              <h2 class="headline-1 text-center">Contact Us</h2>
              <p class="form-text text-center">
                Got a question or feedback? Reach out to us via the form below.
              </p>
              <div class="input-wrapper">
                <input type="text" id="name" name="name" placeholder="Your Name" autocomplete="off" class="input-field" required>
                <input type="email" id="email" name="email" placeholder="Your Email" autocomplete="off" class="input-field" required>
              </div>
              <textarea id="message" name="message" placeholder="Your Message" autocomplete="off" class="input-field" required></textarea>
        <button type="submit" class="btn btn-secondary">
                <span class="text text-1">Send Message</span>
                <span class="text text-2" aria-hidden="true">Send Message</span>
              </button>
              <p id="formMessage" class="form-message"></p>
            </form>
            <div class="form-right text-center" >
              <h2 class="headline-1 text-center">Get in Touch</h2>
              <p class="contact-label">Location</p>
              <address class="body-4">Corporate Street, Stratford Rd, Liverpool 8976, UK</address>
              <p class="contact-label">Phone</p>
              <a href="tel:+29056745321" class="body-1 contact-number hover-underline">+2 905 674 5321</a>
              <p class="contact-label">Email</p>
              <a href="mailto:contact@peripalace.com" class="body-4 sidebar-link">contact@peripalace.com</a>
              
               <div class="social-links" style="margin-top: 20px; text-align:center;"> 
          <a href="https://x.com/Peri_Palace"
             target="_blank"
             aria-label="Follow Peri Palace on X (Twitter)">
            <img src="./assets/images/Twitter.png"
                 alt="Twitter icon"
                 width="45"
                 height="40"
                 style="object-fit: cover; display:inline-block;">
              </a>
              
            </div>
          </div>
        </div>
      </section>
      <!-- 
        - #NEW ADDITION
      -->
      <section class="section features text-center" aria-label="features">
        <div class="container">
          <p class="section-subtitle label-2">WE'RE NOT DONE YET!</p>
          <h2 class="headline-1 section-title">Get ready to indulge in our sweetest new delights – a sneak peek has never tasted this good! </h2>
          <ul class="grid-list">
            <li class="feature-item">
              <div class="feature-card">
                <div class="card-icon">
                  <img src="./assets/images/ice-cream.jpg" width="200" height="100" loading="lazy" alt="icon">
                </div>
       <h3 class="title-2 card-title">Brownie-Cream Explosion</h3>
 <p class="label-1 card-text">OUR BEST SELLER! Enjoy our gooey chocolate brownie, topped with creamy vanilla ice-cream!</p>
              </div>
            </li>
            <li class="feature-item">
              <div class="feature-card">
                <div class="card-icon">
                  <img src="./assets/images/mousse.jpg" width="200" height="100" loading="lazy" alt="icon">
                </div>
                <h3 class="title-2 card-title">Chocolate Mousse Mountain</h3>
                <p class="label-1 card-text">Satisfy your sweet craving with our creamy mousse, topped with fresh sweet blueberries!</p>
              </div>
            </li>
    <li class="feature-item">
  <div class="feature-card">
   <div class="card-icon">
     <img src="./assets/images/cookie.jpg" width="200" height="100" loading="lazy" alt="icon">
             </div>
                <h3 class="title-2 card-title">Cookie Dough Delight</h3>
                <p class="label-1 card-text">You won't get enough of our soft cookie dough, still warm with a melting scoop of ice-cream of your choice!</p>
              </div>
            </li>
            <li class="feature-item">
              <div class="feature-card">
                <div class="card-icon">
                  <img src="./assets/images/pearCake.jpg" width="200" height="100" loading="lazy" alt="icon">
                </div>
                <h3 class="title-2 card-title">Pear-fection Cake </h3>
                <p class="label-1 card-text">Our Pearfection Cake; a dessert that's irresistibly delightful in every bite</p>
              </div>
            </li>
          </ul>
          
       <!-- 
  - #CONTACT US PAGE
-->

<section id="contact" class="contact-us-section">
  
</section>

<!-- 
  - #NEW ADDITION
-->
<section class="section features text-center" aria-label="features">
  <!-- (Features code above remains unchanged) -->
</section>


<!-- 
  - #FOOTER SECTION
-->
<footer class="footer section has-bg-image text-center"
  style="background-image: url('./assets/images/newBackSpice.jpg')">
  <div class="container">

    <div class="footer-top grid-list">

      <div class="footer-brand has-before has-after">
        <a href="#" class="logo">
          <img src="./assets/images/logoWhite.png" width="160" height="50" loading="lazy" alt="Peri Palace home">
        </a>
        <address class="body-4">
          Corporate Street, Stratford Rd, Liverpool 8976, UK
        </address>
        <a href="mailto:Peri-booking@palace.com" class="body-4 contact-link">
          Peri-booking@palace.com
        </a>
        <a href="tel:+29056745321" class="body-4 contact-link">
          Booking Request : +2 905 674 5321
        </a>
        <p class="body-4">
          Open : 11 am - 11 pm
        </p>

        
        <div class="social-links" style="margin-top: 20px;">
          <a href="https://x.com/Peri_Palace"
             target="_blank"
             aria-label="Follow Peri Palace on X (Twitter)">
            <img src="./assets/images/Twitter.png"
                 alt="Twitter"
                 width="24"
                 height="24"
                 style="object-fit: cover;">
          </a>
        </div>

        <div class="wrapper">
          <div class="separator"></div>
          <div class="separator"></div>
          <div class="separator"></div>
        </div>
      </div>

      <ul class="footer-list">
        <li>
          <a href="#contact" class="label-2 footer-link hover-underline">Contact</a>
        </li>
       
      </ul>

    </div>

    <div class="footer-bottom">
      <p class="copyright">
        &copy; 2024 Peri Place. All Rights Reserved |
      </p>
    </div>

  </div>
</footer>


<script src="./assets/js/script.js"></script>

</body>
</html>