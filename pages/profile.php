<!-- 
 Mshael Waleed Alshwaf   2212857
 Shahad Mohammed Ebrahim 2212855
 Jumanah Jamal Banabilah 2206898
 BAR/403
 3/10/2026
-->
<?php session_start();
  // If the user is NOT logged in, send them to the login page
if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit();}
    
    include_once('../config.php'); 
    include_once("../server/profile_controller.php");

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- SEO Description -->
    <meta
      name="description"
      content="Manage your Little Leaf profile, view your personal information, track your orders, and customize your shopping experience with your favorite plants and decor."
    />

    <!-- SEO Keywords -->
    <meta
      name="keywords"
      content="Little Leaf profile, user account, customer profile, plant store account, order tracking, indoor plants, home decor, eco-friendly shopping, Saudi Arabia plants"
    />
    <title>My profile | Little Leaf</title>
    <link rel="stylesheet" href="../global/main.css" />
    <link rel="stylesheet" href="../css/profile.css" />
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
        <h2>My Account</h2>
        <p>Manage your details and keep your sanctuary growing.</p>
      </div>
      <!-- My Account -->
      <div class="profile-container">
        <aside class="profile-sidebar sticky-sidebar">
          <ul class="sidebar-links">
            <li>
              <a href="#account-details" class="nav-link active"
                >Account Details</a
              >
            </li>
            <li>
              <a href="#order-history" class="nav-link">Order History</a>
            </li>
            <li>
              <a href="#saved-addresses" class="nav-link">Saved Addresses</a>
            </li>
            <li>
              <a href="#payment-methods" class="nav-link">Payment Methods</a>
            </li>
            <li class="divider"></li>
           <?php if (isset($_SESSION['logged_in'])): ?>
               <li>
                    <a href="<?= BASE_URL ?>server/logout.php" class="sign-out">Sign Out</a>
               </li>
           <?php endif; ?>
          </ul>
        </aside>
        <!-- Account Details -->
        <div id="account-details" class="profile-section">
          <h3>Personal Information</h3>
          <form class="profile-form" method="POST" action="../server/update_profile.php">
            <div class="form-row">
              <div class="form-group">
                <label for="firstName">First Name</label>
                <!--htmlspecialchars(...) to prevent XSS (Cross-Site Scripting).-->
                <input
                  type="text"
                  id="firstName"
                  name="fName"
                  placeholder="e.g. Jane"
                  required 
                  minlength="2" 
                  pattern="[A-Za-z\s]+"
                  value="<?= htmlspecialchars($_SESSION['user_fname']) ?>" 
                />
                <small class="form-hint">Min. 2 characters (Letters only)</small>
              </div>
              <div class="form-group">
                <label for="lastName">Last Name</label>
                <input
                  type="text"
                  id="lastName"
                  name="lName"
                  placeholder="e.g. Doe"
                  required
                  minlength="2"
                  pattern="[A-Za-z\s]+"
                  value="<?= htmlspecialchars($_SESSION['user_lname']) ?>"
                />
                <small class="form-hint">Min. 2 characters (Letters only)</small>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="email">Email Address</label>
                  <input 
                  type="email"  
                  id="email" 
                  name="email"
                  value="<?= htmlspecialchars($_SESSION['user_email']) ?>" 
                />
              </div>
              <div class="form-group">
                <label for="phone">Phone Number</label>
                <input 
                type="tel" 
                name="phone" 
                placeholder="05XXXXXXXX"
                pattern="^(05|5|\+9665|009665)[0-9]{8}$"
                title="Please enter a valid Saudi mobile number (e.g., 0512345678)" 
                id="phone" placeholder="Enter phone number" 
                value="<?= htmlspecialchars($_SESSION['user_phone'] ?? '') ?>" required/>
              </div>
            </div>

            <div class="form-group full-width">
              <button type="submit" name="update_profile" class="btn form-btn">Save Changes</button>
            </div>
          </form>
        </div>

 
        <!-- Order History ----------------------- -->

        <div id="order-history" class="profile-section">
                 <!-- 🔔 NOTIFICATION BANNERS -->
                <?php if (isset($_GET['status']) && $_GET['status'] === 'order'): ?>
                    <div class="alert alert-success" style="padding: 15px; margin-bottom: 20px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px;">
                        <strong>Success!</strong> Your order has been placed successfully. 🌿
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['email_err']) && $_GET['email_err'] === 'failed'): ?>
                    <div class="alert alert-warning" style="padding: 15px; margin-bottom: 20px; background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 4px;">
                        <strong>Notice:</strong> Your order was placed successfully, but we encountered an issue sending your confirmation email. 
                    </div>
                <?php endif; ?>
            <h3>Order History</h3>
            <div class="order-list">
                <?php if (empty($orders)): ?>
                    <p class="noOrder-msg">No orders found yet. Your sanctuary is waiting for its first plant! 🌿</p>
                <?php else: ?>
                    <?php foreach ($orders as $order): 
                        // Determine CSS class for the status badge (delivered, processing, etc.)
                        $statusClass = strtolower($order['status']); 
                    ?>
                        <div class="order-card">
                            <div class="order-header">
                                <span class="order-id">Order #LL-<?= $order['id'] ?></span>
                                <span class="order-status <?= $statusClass ?>"><?= $order['status'] ?></span>
                            </div>
                            
                            <p class="order-date">
                                Placed on <?= date('F j, Y', strtotime($order['created_at'])) ?>
                            </p>
                            
                            <div class="order-items">
                                <?= htmlspecialchars($order['items_summary']) ?>
                            </div>
                            
                            <p class="order-total">Total: SAR <?= number_format($order['total_price'], 2) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>


        <!-- Saved Addresses -->
        <div id="saved-addresses" class="profile-section">
          <h3>Saved Addresses</h3>
            <div class="info-grid">
            <?php if (empty($addresses)): ?>
              <div class="info-card centerChildren">
                <p>No addresses found. Add one!</p>
              </div>
            <?php else: ?>
                <?php foreach ($addresses as $addr): ?>
                    <div class="info-card" id="address-card-<?= $addr['id'] ?>">
                        <?php if ($addr['is_default']): ?>
                            <span class="badge">Default</span>
                        <?php endif; ?>

                        <h4><?= htmlspecialchars($addr['label']) ?></h4>
                        <p>
                            <?= htmlspecialchars($addr['full_name']) ?><br />
                            <?= htmlspecialchars($addr['address_line']) ?><br />
                            <?= htmlspecialchars($addr['city']) ?>, <?= htmlspecialchars($addr['province']) ?><br />
                            Saudi Arabia
                        </p>
                        
                        <div class="card-actions">
                            <button class="text-btn" onclick='openEditModal(<?= json_encode($addr) ?>)'>Edit</button>
                            <button class="text-btn danger" onclick="deleteAddress(<?= $addr['id'] ?>)">Delete</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="info-card add-new-card">
                <div class="add-icon" onclick="openAddModal()" >+</div>
                <div><p>Add New Address</p></div>
            </div>
        </div>
        </div>



        

        <!-- Payment Methods -->
        <div id="payment-methods" class="profile-section">
          <h3>Payment Methods</h3>
          <div class="info-grid">
            <?php if (empty($addresses)): ?>
              <div class="info-card centerChildren">
                <p>No addresses found. Add one!</p>
              </div>
            <?php else: ?>
              <?php foreach( $payments as $pay):?>
                <div class="info-card" id="payment-card-<?= $pay['id'] ?>">
                    <?php if ($pay['is_default']): ?>
                          <span class="badge">Default</span>
                    <?php endif; ?>
                    <h4><?=htmlspecialchars($pay["card_brand"])?> ending in <?=htmlspecialchars($pay["last4"])?></h4>
                    <p><?="Expires  ".htmlspecialchars($pay["expiry_month"])." / ".htmlspecialchars($pay["expiry_year"])?></p>
                    <p class="card-name"><?=$pay["cardholder_name"]?></p>
                    <div class="card-actions">
                      <button class="text-btn danger" onclick="deleteCard(<?= $pay['id'] ?>)">Remove</button>
                    </div>
                </div>
            <?php endforeach; ?>
          <?php endif; ?>

            <div class="info-card add-new-card">
              <div class="add-icon" onclick="openPaymentModal()">+</div>
              <p>Add Payment Method</p>
            </div>
          </div>
        </div>
      </div>
      </main>
       <!-- SIMPLE FOOTER -->
      <?php include("../parts/footer.php")?>
                    </div>

    <!--Payment method modal -->
    <div id="payment-modal" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <h3>Add Payment Method</h3>
        <form id="payment-form">
            <input type="hidden" name="action" value="add_payment">

            <div class="form-group">
                <label>Cardholder Name</label>
                <input type="text" name="cardholder_name" placeholder="Jane Doe" required minlength="3" pattern="^[A-Za-z\s'-]+$" title="Please enter the cardholder's name.">
            </div>

            <div class="form-group">
                <label>Card Number</label>
                <input type="text" id="card-num-input" name="card_number" inputmode="numeric" 
                       pattern="[0-9]{4} [0-9]{4} [0-9]{4} [0-9]{4}" 
                    maxlength="19" 
                    required 
                    placeholder="1234 5678 9101 1121"
                    title="Please enter a valid 16-digit card number.">
            </div>

            <div class="row">
                <div class="form-group">
                    <label>Expiry (MM/YYYY)</label>
                    <div class="expiry-inputs">
                        <input type="text" name="exp_month" id="exp_month" placeholder="MM"inputmode="numeric"
                            placeholder="MM" 
                            pattern="(0[1-9]|1[0-2])"
                            maxlength="2"
                            required>
                        <input type="text" name="exp_year" id="exp_year" placeholder="YYYY" inputmode="numeric"
                            placeholder="YYYY" 
                            pattern="[0-9]{4}"
                            maxlength="4"
                            required>
                    </div>
                </div>
                <div class="form-group">
                    <label>CVV</label>
                   <input type="text" name="cvv" pattern="[0-9]{3}" maxlength="3" inputmode="numeric" placeholder="123" required>
                </div>
            </div>

            <div class="form-group checkbox-group">
                <label>
                    <input type="checkbox" name="is_default" id="field-default" value="1">
                    Set as default card
                </label>
              </div>

            <div class="modal-actions">
                <button type="button" class="btn secondary" onclick="closePaymentModal()">Cancel</button>
                <button type="submit" class="btn primary">Save Card</button>
            </div>
        </form>
    </div>
</div> 
              
<!-- address modal -->
<div id="address-modal" class="modal-overlay" style="display:none;">
  <div class="modal-content">
    <h3 id="modal-title">Add New Address</h3>
    <form id="address-form">
      <input type="hidden" id="address-id" name="address_id" value="">
      <input type="hidden" id="form-action" name="action" value="add">

      <div class="form-group">
        <label for="field-label">Label (e.g., Home, Office)</label>
        <input 
          type="text" 
          name="label" 
          id="field-label" 
          required 
          placeholder="e.g. Home"
          minlength="2"
          maxlength="20"
        >
      </div>

      <div class="form-group">
        <label for="field-name">Full Name</label>
        <input 
          type="text" 
          name="full_name" 
          id="field-name" 
          required 
          placeholder="Recipient's Name"
          minlength="6"
          maxlength="50"
           pattern="[A-Za-z\s]+"
          title="Please enter a valid name (letters only)."
        >
      </div>

      <div class="form-group">
        <label for="field-address">Address Line</label>
        <input 
          type="text" 
          name="address_line" 
          id="field-address" 
          required 
          placeholder="Street name, Building number"
          minlength="5"
          maxlength="100"
        >
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="field-city">City</label>
          <input 
            type="text" 
            name="city" 
            id="field-city" 
            required 
            placeholder="e.g. Jeddah"
            pattern="^[A-Za-z\s]+$"
          >
        </div>

        <div class="form-group">
          <label for="field-province">Province</label>
          <input 
            type="text" 
            name="province" 
            id="field-province" 
            required 
            placeholder="e.g. Makkah"
            pattern="^[A-Za-z\s]+$"
          >
        </div>
      </div>
      
      <div class="form-group checkbox-group">
        <label>
          <input type="checkbox" name="is_default" id="field-default" value="1">
          Set as default address
        </label>
      </div>

      <div class="modal-actions">
        <button type="button" class="btn secondary" onclick="closeModal()">Cancel</button>
        <button type="submit" class="btn primary">Save Address</button>
      </div>
    </form>
  </div>
</div>


   <!-- 🔍 Floating Search Overlay -->
    <?php include("../parts/floatingSearch.php")?>

   <!-- toast -->
     <?php require_once(BASE_PATH . 'parts/toast.php'); ?>
    <script src="../scripts/main.js"></script>
    <script src="../scripts/profile.js"></script>
  </body>
</html>
