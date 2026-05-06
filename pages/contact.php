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
    <title>Contact | Little Leaf</title>
    <link rel="stylesheet" href="../global/main.css" />
    <link rel="stylesheet" href="../css/contact.css" />
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
      <section class="contact">
        <div class="contact-elegant-info">
          <h2>Get in Touch</h2>
          <p class="subtitle">
            Have a question about a plant, your order, or planning the perfect
            green gift? We’re always here to help your sanctuary grow, and to
            make gifting plants simple and meaningful.
          </p>

          <address class="info-blocks">
            <div class="info-item">
              <strong>📍 Visit Us</strong>
              <p>123 Green Oasis St, Al-Andalus<br />Jeddah, Makkah Province</p>
            </div>

            <div class="info-item">
              <strong>✉️ Email Us</strong>
              <p>hello@littleleaf.com</p>
            </div>
          </address>
        </div>

        <div class="contact-elegant-form">
          <form class="contact-form" method="POST" action="../server/contact_process.php">
            <div class="form-row">
              <div class="form-group">
                <label for="fName">First Name</label>
                <input
                  type="text"
                  id="fName"
                  name="fName"
                  placeholder="Your First Name"
                  required
                />
              </div>
              <div class="form-group">
                <label for="lName">Last Name</label>
                <input
                  type="text"
                  id="lName"
                  name="lName"
                  placeholder="Your Last Name"
                  required
                />
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
              <label for="message">Message</label>
              <textarea
                type="textarea"
                id="message"
                name="msg"
                rows="4"
                placeholder="write your message here..."
                required
              ></textarea>
            </div>

            <button type="submit" class="btn form-btn" name="contact-submit">Send Message</button>
          </form>
        </div>
      </section>
      </main>
       <!-- SIMPLE FOOTER -->
      <?php include("../parts/footer.php")?>
</div>

    <!-- 🔍 Floating Search Overlay -->
    <?php include("../parts/floatingSearch.php")?>

    <script src="../scripts/main.js"></script>
    <!-- toast -->
    <?php require_once(BASE_PATH . 'parts/toast.php'); ?>

  </body>
</html>
