<!-- 
 Mshael Waleed Alshwaf   2212857
 Shahad Mohammed Ebrahim 2212855
 Jumanah Jamal Banabilah 2206898
 BAR/403
 3/10/2026
-->
<?php 
  require_once('../config.php');
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign In/Up | Little Leaf</title>
    <link rel="stylesheet" href="../global/main.css" />
    <link rel="stylesheet" href="../css/login.css" />
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
      <section class="login-wrapper">
        <div
          class="login-image"
          onclick="window.location.href = '../index.php'"
        >
          <img
            src="../images/logo.svg"
            alt="Lush green leaves shining in the sun"
          />
          <div class="image-overlay-text">
            <h2>Grow With Us !</h2>
          </div>
        </div>

        <div class="login-card">
          <div class="login-form-container">
            <!-- 🔐 LOGIN FORM -->
            <form
              id="loginForm"
              class="login-form active-form"
              method="post"
              action="../server/login_process.php"
            >
      
              <?php 
              // Check if "success" exists in the URL and if its value is "registered"
              if (isset($_GET['success']) && $_GET['success'] == 'registered'): 
              ?>
                  <div class="message-box success-box">
                      <p>Welcome to the family! Your account is ready. Please sign in below.</p>
                  </div> 
              <?php 
               // handle the error message while we are at it
              elseif(isset($_GET['error']) && $_GET['error'] == 'invalid'): ?>
                  <div class="message-box error-box">
                      <p>❌ Oops! That email or password doesn't look right. Try again..</p>
                  </div>
               <?php 
              elseif(isset($_GET['error']) && $_GET['error'] == 'exists'): ?>
                  <div class="message-box error-box">
                      <p> ❌ An account with this email already exists. </p>
                  </div>
                  <div class="note">
                      <p>Please log in to your existing account or reset your password if you’ve forgotten it. </p>
                  </div>
              <?php else: ?>
                  <header class="login-header">
                    <h2>Welcome Back</h2>
                    <p>Sign in to continue cultivating your personal sanctuary.</p>
                  </header>

              <?php endif; ?>

             

              <div class="form-group">
                <label for="email">Email Address</label>
                <input
                  type="email"
                  placeholder="hello@example.com"
                  name="email"
                  id="email"
                  required
                />
              </div>

              <div class="form-group">
                <label for="password">Password</label>
                <input
                  type="password"
                  name="password"
                  id="password"
                  placeholder="••••••••"
                  required
                />
              </div>

              <button class="login-btn" name="login_submit" type="submit">Sign In</button>

              <div class="login-footer">
                Don’t have an account?
                <a href="#" id="showRegister">Create one here</a>
              </div>
            </form>

            <!-- 🆕 REGISTER FORM -->
            <form id="registerForm" class="login-form"  method="post"
              action="../server/register_process.php">
              <header class="login-header">
                <h2>Create Account</h2>
                <p>Join Little Leaf and start growing your green space 🌿</p>
              </header>
              <div class="form-row">
                <div class="form-group">
                  <label for="fName">First Name</label>
                  <input
                    type="text"
                    placeholder="Your First Name"
                    name="fName"
                    id="fName"
                    minlength="2"
                    maxlength="30"
                    pattern="[A-Za-z]+" 
                    title="Letters only, please!"
                    required
                  />
                  <small class="form-hint">Min. 2 characters (Letters only)</small>
                </div>
                <div class="form-group">
                  <label for="lName">Last Name</label>
                  <input
                    type="text"
                    placeholder="Your Last Name"
                    name="lName"
                    id="lName"
                    minlength="2"
                    maxlength="30"
                    pattern="[A-Za-z]+" 
                    title="Letters only, please!"
                    required
                  />
                  <small class="form-hint">Min. 2 characters (Letters only)</small>
                </div>
              </div>

              <div class="form-group">
                <label for="email">Email Address</label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  placeholder="hello@example.com"
                  required
                />
              </div>

              <div class="form-group">
                <label for="password">Password</label>
                <input
                  type="password"
                  id="password"
                  name="password"
                  placeholder="Min. 8 characters"
                  minlength="8"
                  required
                />
                <small class="form-hint">Must be at least 8 characters.</small>
              </div>

              <button class="login-btn" name="register_submit" type="submit">Sign Up</button>

              <div class="login-footer">
                Already have an account?
                <a href="#" id="showLogin">Login here</a>
              </div>
            </form>
          </div>
        </div>
      </section>
      </main>  
    <!-- SIMPLE FOOTER -->
      <?php include("../parts/footer.php")?>
              </div>

 <!-- 🔍 Floating Search Overlay -->
    <?php include("../parts/floatingSearch.php")?>
    <script src="../scripts/main.js"></script>
    <script src="../scripts/login.js"></script>
  </body>
</html>
