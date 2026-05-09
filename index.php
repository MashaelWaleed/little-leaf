<!-- 
 Mshael Waleed Alshwaf   2212857
 Shahad Mohammed Ebrahim 2212855
 Jumanah Jamal Banabilah 2206898
 BAR/403
 3/10/2026
-->
<?php
  session_start(); // This starts the "memory" for this user
  require_once('config.php');

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Little Leaf</title>
    <link rel="stylesheet" href="global/main.css" />
    <link rel="stylesheet" href="css/home.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap"
      rel="stylesheet"
    />
  </head>
  <body>
    <div class="container main-container">
  <!-- NAVBAR -->
            <?php 
           include(BASE_PATH . 'parts/nav.php'); 
      ?>
      <main>
      <div class="plainDiv"></div>

      <div class="store_name_container">
        <h1 class="store-name">Rooted in Tranquility</h1>
        <h3 class="store-phrase">
          We believe every room is a garden waiting to bloom. Bring the outside
          in and let your space breathe with our hand-selected collection of
          vibrant, living companions.
        </h3>
        <div class="btn-container">
          <button
            class="main-btn"
            onclick="window.location.href = './pages/products.php'"
          >
            Cultivate Your Space
          </button>
        <?php if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true): ?>
        <button id="loginBtn" class="main-btn login-btn" onclick="window.location.href = '<?= BASE_URL ?>pages/login.php'">
            Login
        </button>
    <?php endif; ?>   
    <?php if (
        isset($_SESSION['logged_in'], $_SESSION['role']) &&
        $_SESSION['logged_in'] === true &&
        $_SESSION['role'] === 'admin'
    ): ?>
        <button
            id="dashBtn"
            class="main-btn login-btn"
            onclick="window.location.href='<?= BASE_URL ?>admin/dashboard.php'"
        >
            Dashboard
        </button>
    <?php endif; ?>

        </div>
      </div>

      <img
        class="main-img"
        src="./images/bonsai plants.svg"
        alt="plant image"
        width="100"
      />

      <div class="card-container">
        <div class="card">
          <img class="img" src="./images/A.svg" alt="plant image" />
        </div>
        <div class="card">
          <img class="img" src="./images/B.svg" alt="plant image" />
        </div>
        <div class="card">
          <img class="img" src="./images/C.svg" alt="plant image" />
        </div>
      </div>
      </main>
            <!-- SIMPLE FOOTER -->
      <?php include("parts/footer.php")?>
    
        </div>
    <!-- 🔍 Floating Search Overlay -->
    <?php include("parts/floatingSearch.php")?>

    <script src="./scripts/main.js"></script>
  </body>
</html>
