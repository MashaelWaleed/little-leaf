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
$user_id = $_SESSION['user_id'] ?? null;
$cart_products = [];//An empty list where we will eventually store all our plant data.
$subtotal = 0;
$shipping=0;//an initial universal shipping price for all items

if ($user_id) {
    // LOGGED IN: Get items from the DATABASE
    $stmt = $pdo->prepare("
        SELECT p.*, c.quantity 
        FROM plants p 
        JOIN cart c ON p.id = c.plant_id 
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $cart_products = $stmt->fetchAll();
} else {
    // GUEST: Get items from the SESSION, the Session only stores IDs and numbers
    if (!empty($_SESSION['cart'])) {
        $ids = array_keys($_SESSION['cart']);//Get the IDs
        $placeholders = implode(',', array_fill(0, count($ids), '?'));//string of ?
        $stmt = $pdo->prepare("SELECT * FROM plants WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $products = $stmt->fetchAll();//get plants table info
        //Merging the Data
        foreach ($products as $p) {
            $p['quantity'] = $_SESSION['cart'][$p['id']]; // Add quantity to the array
            $cart_products[] = $p;//this now like 2D array or list of array 
            //The empty brackets [] in $cart_products[] are PHP shorthand for "Push to the end of the list."
        }
    }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- SEO Description -->
    <meta
      name="description"
      content="View and manage your shopping cart at Little Leaf. Review your favorite plants, home decor items, and complete your purchase بسهولة وأمان."
    />

    <!-- SEO Keywords -->
    <meta
      name="keywords"
      content="Little Leaf cart, shopping cart, indoor plants, plant store, home decor, eco-friendly shop, buy plants online, Saudi Arabia plants, cart page"
    />
    <title>Your Cart | Little Leaf</title>
    <link rel="stylesheet" href="../global/main.css" />
    <link rel="stylesheet" href="../css/cart.css" />
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
        <h2>Your Cart</h2>
        <p>
          Review your botanical companions before bringing them home to your
          sanctuary.
        </p>
      </div>

      <section class="cart-container">
      <div class="cart-items-list">
        <!-- 🚨 CART ERROR BANNERS -->
        <?php if (isset($_GET['error']) && $_GET['error'] === 'out_of_stock'): ?>
            <div class="alert alert-danger" style="padding: 15px; margin-bottom: 20px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px;">
                <strong>Wait!</strong> One or more of the plants in your cart is currently out of stock or you have requested more than we have available. Please reduce the quantity.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'fail_order'): ?>
            <div class="alert alert-danger" style="padding: 15px; margin-bottom: 20px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px;">
                <strong>Error:</strong> We couldn't process your order at this time. Please try checking out again.
            </div>
        <?php endif; ?>
       
          <!-- cart_products contain cart plant info -->
        <?php if (empty($cart_products)): ?>
          <div class="empty-card">  <img src="../images/logo.svg" alt="leaf icon"> <p>Your cart is empty </p> <a href="products.php">Go shopping!</a></div>
           
        <?php else: ?>
         <?php foreach ($cart_products as $product): 
              $item_total = $product['price'] * $product['quantity'];
              $subtotal += $item_total;
          ?>
              <div class="cart-item-card">
                <img src="<?= BASE_URL ?>images/products/<?= $product['image'] ?>" alt="<?= $product['name'] ?>" />
                <div class="item-details">
                  <h3><?= $product['name'] ?></h3>
                  <p class="item-price"><?= $product['price'] ?> SAR</p>
                </div>
                <div class="item-quantity">
                  <button class="qty-btn" data-id="<?= $product['id'] ?>">-</button>
                  <span class="qty-number"><?= $product['quantity'] ?></span>
                  <button class="qty-btn" data-id="<?= $product['id'] ?>">+</button>
                </div>
                <button class="remove-btn" data-id="<?= $product['id'] ?>">✕</button>
              </div>
          <?php endforeach; ?>
          <?php $_SESSION['subtotal']=$subtotal;
                $_SESSION['shipping']=$shipping; ?>
        <?php endif; ?>
      </div>

        <div class="cart-summary moving-card">
        <h3>Order Summary</h3>
        <div class="summary-row">
          <span>Subtotal</span>
          <span><?= $subtotal ?> SAR</span>
        </div>
        <div class="summary-row">
          <span>Shipping</span>
         <?php if($shipping==0):?>
           <span>Free</span>
           <?php else:?> 
            <span><?= $shipping ?></span>
            <?php endif ?>
        </div>
        <div class="divider"></div>
        <div class="summary-row total-row">
          <span>Total</span>
          <span><?= $subtotal + $shipping ?> SAR</span>
        </div>
        <button name="checkout-btn" class="btn checkout-btn" <?= empty($cart_products) ? 'disabled' : '' ?> onclick="window.location.href='checkout.php';">
          Proceed to Checkout
        </button>
      </div>
      </section>
      </main>
       <!-- SIMPLE FOOTER -->
      <?php include("../parts/footer.php")?>
           </div>

    <!-- 🔍 Floating Search Overlay -->
    <?php include("../parts/floatingSearch.php")?>
     <?php require_once(BASE_PATH . 'parts/toast.php'); ?>
    <script src="../scripts/main.js"></script>
    <script src="../scripts/cart.js"></script>
  </body>
</html>
