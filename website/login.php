<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | REKTA Cycling</title>
  <link rel="stylesheet" href="landingCSS.css" />
  <link rel="stylesheet" href="login.css" />
</head>
<body>

  <!-- Header (same as accessories or hybrid header) -->
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
            <li><a href="cycling.php">Cycling</a></li>
            <li><a href="accessories.php">Accessories</a></li>
          </ul>
        </nav>
  
        <!-- Search / Cart / User -->
        <div style="display: flex; align-items: center; gap: 1rem;">
          <form action="#" method="GET" style="display: flex; align-items: center;">
            <input type="text" name="q" placeholder="Search products..." style="padding: 0.4rem 0.75rem; border: none; border-radius: 4px 0 0 4px;">
            <button type="submit" style="padding: 0.4rem 0.75rem; background: white; border: none; border-radius: 0 4px 4px 0; font-weight: bold; cursor: pointer;">
              🔍
            </button>
          </form>
          <a href="cart.php">
            <img src="cart-73-16.png" alt="Cart" style="cursor: pointer;">
          </a>
          <a href="login.php">
            <img src="user.png" alt="User" style="cursor: pointer;">
          </a>
        </div>
  
      </div>
    </div>
  </header>

  <!-- Login Form -->
  <div class="login-container">
    <h2>Login to Your Account</h2>
    <form action="authenticate.php" method="POST">
      <input type="text" name="username" placeholder="Username or Email" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">Log In</button>
    </form>
    <div class="signup-link">
      Don't have an account? <a href="register.php">Sign up</a>
    </div>
  </div>

  <footer>
    <div class="container" style="display: flex; flex-wrap: wrap; justify-content: space-between; padding: 2rem 0;">
      
      <!-- Column 1: Shop -->
      <div style="flex: 1 1 200px; margin-bottom: 1rem;">
        <h3>Shop</h3>
        <ul style="list-style: none; padding: 0;">
          <li><a href="mens.html" style="color: #fff;">Men</a></li>
          <li><a href="womens.html" style="color: #fff;">Women</a></li>
          <li><a href="#" style="color: #fff;">Accessories</a></li>
        </ul>
      </div>
  
      <!-- Column 2: Support -->
      <div style="flex: 1 1 200px; margin-bottom: 1rem;">
        <h3>Support</h3>
        <ul style="list-style: none; padding: 0;">
          <li><a href="#" style="color: #fff;">Help</a></li>
          <li><a href="#" style="color: #fff;">Returns</a></li>
          <li><a href="#" style="color: #fff;">Order Tracker</a></li>
        </ul>
      </div>
  
      <!-- Column 3: Company Info -->
      <div style="flex: 1 1 200px; margin-bottom: 1rem;">
        <h3>Company</h3>
        <ul style="list-style: none; padding: 0;">
          <li><a href="#" style="color: #fff;">About Us</a></li>
          <li><a href="#" style="color: #fff;">Careers</a></li>
          <li><a href="#" style="color: #fff;">Sustainability</a></li>
        </ul>
      </div>
  
      <!-- Column 4: Newsletter -->
      <div style="flex: 1 1 300px; margin-bottom: 1rem;">
        <h3>Sign up for updates</h3>
        <form>
          <input type="email" placeholder="Your email" style="padding: 0.5rem; width: 80%; margin-bottom: 0.5rem;">
          <br>
          <button type="submit" class="btn" style="background: #fff; color: #000;">Subscribe</button>
        </form>
      </div>

      <!-- Column 5: Social Media -->
      <div style="flex: 1 1 200px; margin-bottom: 1rem;">
        <h3>Follow Us</h3>
        <div style="display: flex; gap: 1rem; align-items: center; margin-inline: auto;">
          <a href="https://www.facebook.com/rektacycling"><img src="facebook.webp" alt="Facebook" style="width: 32px; height: 32px;"></a>
          <a href="https://www.instagram.com/rektacycling/"><img src="instagram.webp" alt="Instagram" style="width: 32px; height: 32px;"></a>
        </div>
      </div>

  
    <!-- Bottom Bar -->
    <div style="background-color: #111; text-align: center; padding: 1rem 0; width: 100%;">
      <p style="margin: 0;">&copy; 2025 Rekta Cycling. All rights reserved. |
        <a href="#" style="color: #fff;">Privacy</a> |
        <a href="#" style="color: #fff;">Terms</a>
      </p>
    </div>
  </footer>

</body>
</html>
