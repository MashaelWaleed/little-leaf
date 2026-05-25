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

// Fetch default payment method from DB tables
$stmt = $pdo->prepare("
    SELECT *
    FROM user_payments
    WHERE user_id = ? AND is_default = 1
    LIMIT 1
");

$stmt->execute([$_SESSION["user_id"]]);
$savedCardInfo = $stmt->fetch(PDO::FETCH_ASSOC);

$subtotal = $_SESSION["subtotal"];
$shipping =$_SESSION["shipping"];
$total = $_SESSION["subtotal"] + $_SESSION["shipping"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- SEO Description -->
    <meta
        name="description"
        content="Complete your purchase securely at Little Leaf. Enter your shipping and payment details to order beautiful indoor plants and home decor items."
    />

    <!-- SEO Keywords -->
    <meta
        name="keywords"
        content="Little Leaf checkout, secure checkout, online plant store, buy indoor plants, plant delivery, home decor shopping, eco-friendly store, Saudi Arabia plants"
    />
    <title>Checkout | Little Leaf</title>
    <link rel="stylesheet" href="../global/main.css" />
    <link rel="stylesheet" href="../css/checkout.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap"
      rel="stylesheet"
    />
    <style>
        /* Basic inline styles for notifications - move to main.css if desired */
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container fill-container scroll-page">
        <!-- HEADER -->
        <?php 
           include(BASE_PATH . 'parts/nav.php'); 
        ?>
        <main>
        <div class="page-header">
            <h2>Finalize Your Sanctuary</h2>
            <p>Review your details before we prepare your plants.</p>
        </div>

        <!-- --- NEW: EMAIL / ORDER ERROR HANDLING MESSAGES --- -->
        <?php if (isset($_GET['error']) && $_GET['error'] === 'fail_order'): ?>
            <div class="alert alert-danger">
                <strong>Error:</strong> We couldn't process your order at this time. Please try again.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['email_err']) && $_GET['email_err'] === 'failed'): ?>
            <div class="alert alert-warning">
                <strong>Notice:</strong> Your order was placed successfully, but we encountered an issue sending your confirmation email. You can review your order details in your profile.
            </div>
        <?php endif; ?>
        <!-- -------------------------------------------------- -->

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
                    <?php if (!empty($savedCardInfo)): ?>
                        <!-- Hidden input to send the payment method ID to place_order.php -->
                        <input type="hidden" name="payment_method_id" form="checkout-form" value="<?= $savedCardInfo['id'] ?>">
                        
                        <span>💳 <?= htmlspecialchars($savedCardInfo['card_brand']) ?> ending in <?= htmlspecialchars($savedCardInfo['last4']) ?></span>
                        <a href="profile.php#payment-methods" class="edit-link">Change</a>
                        
                    <?php else: ?>
                        
                        <p style="margin: 0;">No cards found. Add one!</p>
                        <a href="profile.php#payment-methods" class="edit-link">Add</a>
                        
                    <?php endif; ?>
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
                    <button type="submit" class="place-order-btn" <?= (empty($savedAddress) || empty($savedCardInfo))? "disabled" : "" ?>>
                        Complete Purchase
                    </button>
                </form>
                <p class="secure-note">🔒 Secure encrypted checkout</p>
            </aside>
        </div>
        </main>
    <!-- SIMPLE FOOTER -->
      <?php include("../parts/footer.php")?>
                    </div>

 <!-- 🔍 Floating Search Overlay -->
    <?php include("../parts/floatingSearch.php")?>
</body>
</html>