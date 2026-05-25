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
    <!-- SEO Description -->
    <meta
      name="description"
      content="Learn more about Little Leaf, a modern plant and home decor store dedicated to bringing nature into your everyday life."
    />

    <!-- SEO Keywords -->
    <meta
      name="keywords"
      content="Little Leaf, plants, indoor plants, home decor, plant shop, eco-friendly, gardening, Jeddah plants, Saudi Arabia plants"
    />
    <title>About Us | Little Leaf</title>
    <link rel="stylesheet" href="../global/main.css" />
    <link rel="stylesheet" href="../css/about.css" />
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
      <div class="page-header">
        <h2>Our Roots</h2>
        <p>
          Just like a seed needs the right soil to sprout, a home needs the
          right energy to thrive. We started Little Leaf not merely as a
          botanical shop, but as a greenhouse for happiness. Every plant we
          nurture is a living allegory for patience, care, and quiet growth.
        </p>
      </div>

      <section class="about-card-container">
        <div class="moving-card">
          <div class="card-icon">🌱</div>
          <h3>The Seed</h3>
          <p>
            Our journey began in a tiny, sunlit studio. We realized that
            bringing the outside in wasn't just about decoration, but about
            planting an anchor of calm in a busy world.
          </p>
        </div>

        <div class="moving-card">
          <div class="card-icon">🌿</div>
          <h3>The Stem</h3>
          <p>
            Through seasons of learning, we grew. We partnered with local
            growers who share our belief that plants are quiet companions,
            reaching toward the light alongside us.
          </p>
        </div>

        <div class="moving-card">
          <div class="card-icon">🌸</div>
          <h3>The Bloom</h3>
          <p>
            Today, our mission is to help your personal sanctuary flourish. We
            hand-select resilient, vibrant greens ready to breathe new life and
            poetry into your living spaces.
          </p>
        </div>
          <div class="moving-card testimonial-card">
          <div class="card-icon">✨</div>
          <h3>The Whisper</h3>

          <blockquote cite="https://mashaelwaleed.github.io/Portfolio/">
            "Plants don't just grow in a room; they grow with the people inside
            it."
          </blockquote>

          <footer>— <cite>Our Head Gardener</cite></footer>
        </div>

      </section>

      <!-- VID SECTION -->
      <section id="vid" class="vid-section">
        <div class="container">
          <h2>🎬 Dive Into Our Story – Watch the Video!</h2>

          <div class="video-wrapper">
            <video controls width="100%" height="100%">
              <source src="../videos/My Video-1.mp4" type="video/mp4" />
              Your browser does not support the video tag.
            </video>
          </div>
        </div>
      </section>
      </main>
      <!-- SIMPLE FOOTER -->
      <?php include("../parts/footer.php")?>
</div>

     <!-- 🔍 Floating Search Overlay -->
    <?php include("../parts/floatingSearch.php")?>

    <button id="backToTopBtn" class="back-to-top">
      <img class="img" src="../images/chevron-up.svg" alt="up button" />
    </button>

    <script src="../scripts/main.js"></script>
  </body>
</html>
