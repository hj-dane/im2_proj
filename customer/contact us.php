<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Us | REKTA Cycling</title>
  <link rel="stylesheet" href="../styles/landingCSS.css" />
  <link rel="stylesheet" href="../styles/contactCSS.css" />
</head>
<body>

  <!-- Header -->
 <header>
    <div class="container">
      <div class="nav-container" style="display: flex; align-items: center; justify-content: space-between; position: relative;">
        
        <!-- Logo -->
        <div class="logo" style="font-size: 1.8rem; font-weight: bold; text-transform: uppercase; letter-spacing: 2px;">
          <a href="landingpage.php" style="color: white;">REKTA</a>
        </div>
  
        <!-- Centered Navigation Menu -->
        <nav style="position: absolute; left: 50%; transform: translateX(-50%);">
          <ul style="display: flex; gap: 2rem; list-style: none;">
            <li><a href="clothing.php">Clothing</a></li>
            <li><a href="accessories.php">Accessories</a></li>
            <li><a href="contact us.php">Contact Us</a></li>
          </ul>
        </nav>
  
        <!-- Search / Cart / User -->
        <div style="display: flex; align-items: center; gap: 1rem;">
        <form class="search-bar-header" method="POST" action="">
                <input type="text" name="search" placeholder="Search accessories" value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>" autocomplete="off" />
                <button type="submit" aria-label="Search">
                    <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7" stroke="white" stroke-width="2" fill="none"/><line x1="16.5" y1="16.5" x2="21" y2="21" stroke="white" stroke-width="2"/></svg>
                </button>
            </form>
          <a href="cart.php">
            <img src="../assets/cart-73-16.png" alt="Cart" style="cursor: pointer;">
          </a>
          <a href="../login.php">
            <img src="../assets/user.png" alt="User" style="cursor: pointer;">
          </a>
        </div>
  
      </div>
    </div>
  </header>

  <!-- Contact Section -->
  <section class="contact-section">
    <div class="container">
      <h2>Contact Us</h2>
      <p>If you have any questions, feedback, or inquiries — we’d love to hear from you.</p>

      <form class="contact-form" action="#" method="POST">
        <input type="text" name="name" placeholder="Your Name" required />
        <input type="email" name="email" placeholder="Your Email" required />
        <textarea name="message" placeholder="Your Message" rows="6" required></textarea>
        <button type="submit">Send Message</button>
      </form>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <div class="container">
      &copy; 2025 REKTA Cycling. All rights reserved.
    </div>
  </footer>

</body>
</html>
