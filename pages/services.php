<!-- 
 Mshael Waleed Alshwaf   2212857
 Shahad Mohammed Ebrahim 2212855
 Jumanah Jamal Banabilah 2206898
 BAR/403
 3/10/2026
-->
<?php session_start();?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Service | Little Leaf</title>
    <link rel="stylesheet" href="../global/main.css" />
    <link rel="stylesheet" href="../css/services.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap"
      rel="stylesheet"
    />
  </head>

  <body>
    <div class="container fill-container scroll-page">

        <!-- HEADER -->
            <?php 
           include('../config.php'); 
           include(BASE_PATH . 'parts/nav.php'); 
      ?>
     <main>
      <div class="page-header">
        <h2>Our Services</h2>
        <p>Everything you need to grow your perfect green space 🌿</p>
      </div>

      <!-- SERVICES GRID -->
      <section class="services-grid">
        <!-- 1. SHOP -->
        <div class="service-card">
          <h3>🌱 Shop Plants</h3>
          <p>Browse our beautiful collection of indoor plants.</p>
          <a href="./products.php" class="service-btn button">Shop Now</a>
        </div>

        <!-- 2. SEARCH -->
        <div class="service-card">
          <h3>🔍 Find Your Plant</h3>
          <p>Search for the perfect plant by name or type.</p>
          <button id="openSearchFromService" class="service-btn button">
            Start Searching
          </button>
        </div>

        <!-- 3. GIFT -->
        <div class="service-card">
          <h3>🎁 Send a Gift</h3>
          <p>Send a plant as a beautiful gift to someone special.</p>
          <a href="./contact.php" class="service-btn button">Send Gift</a>
        </div>

        <!-- 4. CARE -->
        <div class="service-card">
          <h3>💡 Plant Care Tips</h3>
          <p>Learn how to take care of your plants easily.</p>
          <a href="#careSection" class="service-btn button">Learn More</a>
        </div>
      </section>

      <!-- CARE TIPS SECTION -->
      <section id="careSection" class="care-section page-header">
        <h2>Plant Care Basics 🌿</h2>

        <div class="care-grid">
          <div>
            <h3>☀️ Sunlight</h3>
            <p>Place plants near natural light but avoid strong direct sun.</p>
          </div>

          <div>
            <h3>💧 Water</h3>
            <p>Water when the soil feels dry. Do not overwater.</p>
          </div>

          <div>
            <h3>🌡 Temperature</h3>
            <p>Keep plants in a moderate room temperature environment.</p>
          </div>

          <div>
            <h3>🌬 Air</h3>
            <p>Good airflow helps prevent plant diseases.</p>
          </div>
        </div>
      </section>
      </main>
      <!-- SIMPLE FOOTER -->
      <?php include("../parts/footer.php")?>
</div>

 <!-- 🔍 Floating Search Overlay -->
    <?php include("../parts/floatingSearch.php")?>

    <button id="backToTopBtn" class="back-to-top">
      <img class="img" src="../images/chevron-up.svg" alt="up button" />
    </button>

    <script src="../scripts/main.js"></script>
    <script src="../scripts/services.js"></script>
  </body>
</html>
