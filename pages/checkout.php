<?php 
session_start();
require_once("../config.php");
require_once("../server/db_connect.php");

if (!isset($_SESSION['logged_in'])) { header("Location: login.php"); exit(); }
//Fetch default address from DB tables
$stmt = $pdo->prepare("
    SELECT *
    FROM user_addresses
    WHERE user_id = ? AND is_default = 1
    LIMIT 1
");

$stmt->execute([$_SESSION["user_id"]]);
//should only be one default address
$savedAddress =$stmt->fetch(PDO::FETCH_ASSOC);


$savedCard = "Visa ending in 4242";
$subtotal = $_SESSION["subtotal"];
$shipping =$_SESSION["shipping"];
$total = $_SESSION["subtotal"] + $_SESSION["shipping"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout | Little Leaf</title>
    <link rel="stylesheet" href="../global/main.css" />
    <link rel="stylesheet" href="../css/checkout.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap"
      rel="stylesheet"
    />
</head>
<body>
    <main class="container fill-container scroll-page">
        <!-- HEADER -->
        <?php 
           include(BASE_PATH . 'parts/nav.php'); 
        ?>
        <div class="page-header">
            <h2>Finalize Your Sanctuary</h2>
            <p>Review your details before we prepare your plants.</p>
        </div>

        <div class="checkout-grid">
            <section class="checkout-details">
                <?php if (!empty($savedAddress)): ?>
                <div class="checkout-section">
                    <h3>1. Shipping Address</h3>
                    <div class="address-box">
                         <div class="info-card">
                             
                                 <?= htmlspecialchars($savedAddress['full_name']) ?><br />
                                 <?= htmlspecialchars($savedAddress['address_line']) ?><br />
                                 <?= htmlspecialchars($savedAddress['city']) ?>, <?= htmlspecialchars($savedAddress['province']) ?><br />
                                 Saudi Arabia
                                   
                         </div>       
                        <a href="profile.php#saved-addresses" class="edit-link">Change</a>
                    </div>
                </div>
                <?php else: ?>
                     <div class="checkout-section">
                        <h3>1. Shipping Address</h3>
                        <div class="address-box">
                            <p>No addresses found. Add one!</p>
                            <a href="profile.php#saved-addresses" class="edit-link">Add</a>
                        </div>
                     </div>
                <?php endif; ?>



                <div class="checkout-section">
                    <h3>2. Payment Method</h3>
                    <div class="payment-box">
                        <span>💳 <?php echo $savedCard; ?></span>
                        <a href="profile.php#payment-methods" class="edit-link">Change</a>
                    </div>
                </div>
            </section>

            <aside class="order-summary-card">
                <h2>Order Summary</h2>
                <div class="summary-line">
                    <span>Subtotal</span>
                    <span><?php echo $subtotal; ?> SAR</span>
                </div>
                <div class="summary-line">
                    <span>Shipping</span>
                    <?php if($shipping==0):?>
                    <span>Free</span>
                    <?php else:?> 
                        <span><?= $shipping ?></span>
                    <?php endif ?>    
                </div>
                <hr>
                <div class="summary-line total">
                    <span>Total</span>
                    <span><?php echo $total; ?> SAR</span>
                </div>

                <form action="../server/place_order.php" method="POST">
                    <button type="submit" class="place-order-btn" <?= (empty($savedAddress) || empty($savedCard))? "disabled" : "" ?>>
                        Complete Purchase
                    </button>
                </form>
                <p class="secure-note">🔒 Secure encrypted checkout</p>
            </aside>
        </div>
    <!-- SIMPLE FOOTER -->
      <?php include("../parts/footer.php")?>
    </main>

 <!-- 🔍 Floating Search Overlay -->
    <?php include("../parts/floatingSearch.php")?>
</body>
</html>