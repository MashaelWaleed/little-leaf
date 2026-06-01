<!-- 
 Mshael Waleed Alshwaf   2212857
 Shahad Mohammed Ebrahim 2212855
 Jumanah Jamal Banabilah 2206898
 BAR/403
 3/10/2026
-->
 <?php 
 // Force PHP to show errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 session_start();
    include('../config.php');
    include('../server/db_connect.php');
 ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <!-- SEO Description -->
      <meta
        name="description"
        content="Share your feedback with Little Leaf. Tell us about your shopping experience, plant quality, customer service, and suggestions to help us improve."
      />

      <!-- SEO Keywords -->
      <meta
        name="keywords"
        content="Little Leaf feedback, customer reviews, plant store feedback, customer experience, indoor plants, home decor, eco-friendly store, Saudi Arabia plants, feedback form"
      />
    <title>Feedback | Little Leaf</title>
    <link rel="stylesheet" href="../global/main.css" />
    <link rel="stylesheet" href="../css/feedback.css" />
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
      <div class="grid-layout-feed">
        <div class="users-opinions-container">
          <h2>Share Your Thoughts with Little Leaf</h2>
          <p>We'd love to hear how we're doing!</p>

          <?php
          // Initial 3
          $stmt = $pdo->query("SELECT * FROM feedback ORDER BY created_at DESC LIMIT 3");

          // Check if there are rows using rowCount() since we haven't fetched yet
          if ($stmt->rowCount() > 0): ?>
              <div>
                  <div class="feedback-list" id="feedback-list">
                      <?php 
                      while ($row = $stmt->fetch()) {
                          // Add data-user-id to the card container so JavaScript can find it later
                          echo '<div class="comment-card" data-user-id="'.htmlspecialchars($row['user_id']).'">';
                          echo '<h1 class="comment-sender"><span> From </span>'.htmlspecialchars($row['user_name']).'</h1>';
                          echo '<p class="comment-content">'.htmlspecialchars($row['comments']).'</p>';
                          
                          if (!empty($row['updated_at'])) {
                              echo '<span class="comment-date">Updated: '.date('d/m/Y', strtotime($row['updated_at'])).'</span>';
                          } else {
                              echo '<span class="comment-date">'.date('d/m/Y', strtotime($row['created_at'])).'</span>';
                          }

                          echo '</div>';
                      }
                      ?>
                  </div>
                  <button id="loadMoreBtn" class="loadMoreBtn">Read More Opinions</button>
              </div>
          <?php else: ?>
            <div  class="no-feedback"> <p>No feedback yet.</p><p> Be the first to leave one! ☘️</p></div>
             
          <?php endif; ?>
        </div>


        <?php if (!isset($_SESSION['logged_in'])):?>
        <div class="msg">
           <img src="../images/logo.svg" alt="leaf icon"> <h1> Login to share your Thoughts </h1>  
          <a href="./login.php">Login</a>
        </div>
         <?php else: ?>
        <div class="feedback-container ">
            <form action="../server/process_feedback.php" method="POST" id="feedbackForm">
              <div class="form-group">
                <label for="name">Full Name</label>
                <input
                  type="text"
                  id="name"
                  name="name"
                  placeholder="Enter your name (Will be shown to others)"
                  required
                  minlength="6"
                  pattern="[A-Za-z\s]+"
                  title="Please enter your full name (letters only)."
                />
                <small class="form-hint">Min. 6 characters (Letters only)</small>
              </div>

              <div class="form-group">
                <label for="email">Email Address</label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  placeholder="Enter your email"
                  required
                />
              </div>

              <div class="form-group">
                <label>How would you rate your experience?</label>
                <div class="radio-group">
                  <input type="radio" id="good" name="rating" value="good" required/>
                  <label for="good">Good</label>

                  <input
                    type="radio"
                    id="average"
                    name="rating"
                    value="average"
                  />
                  <label for="average">Average</label>

                  <input type="radio" id="poor" name="rating" value="poor" />
                  <label for="poor">Poor</label>
                </div>
              </div>

              <div class="form-group">
                <label>Which services did you use? <span class="optional">(Optional)</span></label>
                <div class="checkbox-group">
                  <label
                    ><input type="checkbox" name="service[]" value="care-tips" />
                    Plant Care Tips</label
                  >
                  <label
                    ><input type="checkbox" name="service[]" value="shop" /> Plant
                    Shop</label
                  >
                  <label
                    ><input type="checkbox" name="service[]" value="gift" />
                    Send a Gift
                  </label>
                </div>
              </div>

              <div class="form-group">
                <label for="preference">Content Preference</label>
                <select id="preference" name="preference">
                  <option value="indoor">Indoor Plants</option>
                  <option value="outdoor">Outdoor Gardening</option>
                  <option value="succulents">Succulents & Cacti</option>
                  <option value="hydroponics">Hydroponics</option>
                </select>
              </div>

              <div class="form-group">
                <label for="comments">Comments to share</label>
                <textarea
                  id="comments"
                  name="comments"
                  rows="4"
                  placeholder="Share your thoughts..."
                  required
                  minlength="10"
                  maxlength="500"
                ></textarea>
                <div class="counter-container">
                <small id="charCount">0 / 500</small>
                </div>
              </div>

              <button type="submit" class="submit-btn" name="feedback-submit" >Submit Feedback</button>
            </form>
          </div>
        <?php endif; ?>
       
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
    <script src="../scripts/feedback.js"></script>
  </body>
</html>
