<!-- NAVBAR -->
     <header class="nav-header">
      <nav class="navbar">
        <a href="<?php echo BASE_URL; ?>index.php" class="logo">
          <img class="img" src="<?php echo BASE_URL; ?>images/logo.svg" alt="logo image" />
        </a>
        <ul class="nav-links">
          <li><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
          <li><a href="<?php echo BASE_URL; ?>pages/services.php">Services</a></li>
          <li><a href="<?php echo BASE_URL; ?>pages/products.php">Products</a></li>
          <li><a href="<?php echo BASE_URL; ?>pages/about.php">About </a></li>
          <li><a href="<?php echo BASE_URL; ?>pages/contact.php">Contact</a></li>
          <li><a href="<?php echo BASE_URL; ?>pages/feedback.php">Feedback</a></li>
        </ul>
        <div class="icons-container">
          <a href="#" id="openSearch">
            <img
              class="search-icon icon"
              src="<?php echo BASE_URL; ?>images/search.svg"
              alt="search icon"
          /></a>
          <a href="<?php echo BASE_URL; ?>pages/cart.php">
            <img
              class="cart-icon icon"
              src="<?php echo BASE_URL; ?>images/shopping-cart.svg"
              alt="shopping cart icon"
            />
          </a>

        <?php if (isset($_SESSION['logged_in'])): ?>
        <a id="userIcon" class="userIcon" href="<?= BASE_URL ?>pages/profile.php" style="display: flex;">
        <img class="user-icon icon" src="<?= BASE_URL ?>images/user.svg" alt="user icon" />
        </a>
        <?php endif; ?>

          <a href="<?php echo BASE_URL; ?>pages/schedule.php">
            <img
              class="sprout-icon-icon icon"
              src="<?php echo BASE_URL; ?>images/sprout.svg"
              alt="sprout icon"
            />
          </a>
        </div>

        <div class="hamburger" id="hamburger">
          <span class="bar"></span>
          <span class="bar"></span>
          <span class="bar"></span>
        </div>
      </nav>
    </header>
