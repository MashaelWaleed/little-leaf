<!-- 
 Mshael Waleed Alshwaf   2212857
 Shahad Mohammed Ebrahim 2212855
 Jumanah Jamal Banabilah 2206898
 BAR/403
 3/10/2026
-->
<?php 
 session_start();
require_once('../server/db_connect.php');
  require_once('../config.php');
  // The Fetching
$stmt = $pdo->query("SELECT * FROM plants");
$plants = $stmt->fetchAll();

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <!-- SEO Description -->
      <meta
        name="description"
        content="Explore Little Leaf’s collection of indoor plants, decorative pots, and eco-friendly home decor. Find the perfect greenery to brighten your space."
      />

      <!-- SEO Keywords -->
      <meta
        name="keywords"
        content="Little Leaf products, indoor plants, plant shop, home decor, decorative pots, eco-friendly products, buy plants online, gardening, Saudi Arabia plants, houseplants"
      />
    <title>Our Products | Little Leaf</title>
    <link rel="stylesheet" href="../global/main.css" />
    <link rel="stylesheet" href="../css/products.css" />
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
           include(BASE_PATH . 'parts/nav.php'); 
      ?>
      <main>
      <div class="page-header">
        <h2>Our Collection</h2>
        <p>Bring the outside in with our hand-selected botanical companions.</p>
      </div>
<div class="cards-container product-grid">

<?php foreach ($plants as $plant): ?>
        <div class="product-card">
          <div class="product-image-bg">
            <img
              src="<?= BASE_URL ?>images/products/<?= $plant['image'] ?>"
              alt="<?= $plant['name'] ?>"
            />
          <button class="add-to-cart-btn" data-id="<?= $plant['id'] ?>">+</button>
          </div>
          <div class="product-info">
            <h3><?= $plant['name'] ?></h3>
            <p class="price"><?= $plant['price'] ?> SAR</p>
          </div>
        </div>
 <?php endforeach; ?>

  </div>
  </main>
      <!-- SIMPLE FOOTER -->
      <?php include("../parts/footer.php")?>
</div>

     <!-- 🔍 Floating Search Overlay -->
    <?php include("../parts/floatingSearch.php")?>

    <!-- toast -->
     <?php require_once(BASE_PATH . 'parts/toast.php'); ?>
    <script src="../scripts/main.js"></script>
    <script src="../scripts/product.js"></script>
  </body>
</html>
