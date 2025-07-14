<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="../styles/invdesc.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <title>REKTA | Product Details</title>
        <link rel="icon" type="image/x-icon" href="../assets/logo_stockflow.png">
        
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container-fluid">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center">
                        
                        <div class="logo" style="font-family: Milker; flex: 0 0 auto;">
                            <a href="landingpage.html">rekta</a>
                        </div>
                        
                        <ul class="nav nav-tabs border-0" style="margin-top: 6px;">
                            <li class="nav-item">
                                <a class="custom-nav-link" href="analytics.php" title="Dashboard">
                                    <i class="bi bi-speedometer2 fs-5"></i>
                                    <span class="d-none d-md-inline ms-2">DASHBOARD</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="custom-nav-link" href="warehouse.php" title="Add Product">
                                    <i class="bi bi-plus-square fs-5"></i>
                                    <span class="d-none d-md-inline ms-2">ADD PRODUCT</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="custom-nav-link custom-active" aria-current="page" href="inventorylist.php" title="Inventory">
                                    <i class="bi bi-box-seam fs-5"></i>
                                    <span class="d-none d-md-inline ms-2">INVENTORY</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="custom-nav-link" aria-current="page" href="orderlogs.php" title="Orders">
                                    <i class="bi bi-box-seam fs-5"></i>
                                    <span class="d-none d-md-inline ms-2">ORDER LOGS</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="custom-nav-link" aria-current="page" href="delist.php" title="Archive">
                                    <i class="bi bi-box-seam fs-5"></i>
                                    <span class="d-none d-md-inline ms-2">ARCHIVED ITEMS</span>
                                </a>
                            </li>
                        </ul>
                    </div>
        
                    <div class="d-flex align-items-center gap-4">
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-white name" style="font-weight: 750;"></span>
                        </div>
                        
                        <div class="dropdown">
                            <button class="btn border-0 shadow-none p-0" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background: none;">
                                <img src="../assets/profile_icon.png" alt="Profile" class="rounded-circle" width="40" height="40">
                            </button>
                            
                            <ul class="dropdown-menu dropdown-menu-end p-0" aria-labelledby="profileDropdown" style="width: 300px;">
                                <li class="px-3 pt-3 pb-2">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold name" style="color: black;font-size: 20px;"></span>  
                                        <span class="text-muted small role" style="color: black;font-weight: 500;font-size: 18px;">Standard User</span>  
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider m-0"></li>
                                <li><a class="dropdown-item" href="#">Manage Account</a></li>
                                <li><a class="dropdown-item" href="#">Settings</a></li>
                                <li><a class="dropdown-item" href="#">Contact Support</a></li>
                                <li class="py-2"></li> 
                                <li><hr class="dropdown-divider m-0"></li>
                                <li class="px-3">  
                                    <div class="d-grid" style="padding-bottom: 6%;"> 
                                        <a href="../sign.html" class="btn btn-danger">Logout</a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main class="main-content">
            <div class="container-itemdescc">
                <h1 class="product-title"><b>Product Details</b></h1>
                <hr>
                <div class="row-top">
                <div class="infor" id="productDescSection">
                  <?php
                  // Database connection
                  $host = 'admin.dcism.org';
                  $username = 's11820346';
                  $password = 'SEULRENE_kangseulgi';
                  $dbname = 's11820346_im2';

                  // Create connection
                  $conn = new mysqli($host, $username, $password, $dbname);
                  
                  // Check connection
                  if ($conn->connect_error) {
                      die("Connection failed: " . $conn->connect_error);
                  }
                  
                  // Get product ID from URL
                  $productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
                  
                  // Fetch product details
                  $sql = "SELECT pi.*, c.category_name 
                          FROM product_inventory pi
                          LEFT JOIN category c ON pi.category_id = c.id
                          WHERE pi.id = ?";
                  $stmt = $conn->prepare($sql);
                  $stmt->bind_param("i", $productId);
                  $stmt->execute();
                  $result = $stmt->get_result();
                  
                  if ($result->num_rows > 0) {
                      $product = $result->fetch_assoc();
                      
                      // Display product description
                      echo '<h2><strong>' . htmlspecialchars($product['product_name']) . '</strong></h2>';
                      echo '<p>' . htmlspecialchars($product['product_description']) . '</p>';
                  } else {
                      echo "<p>Product not found.</p>";
                  }
                  ?>
                </div>
          
                <div class="infor" id="productDetailSection">
                  <?php
                  if (isset($product)) {
                      // Display product details
                      echo '<p><strong>Price:</strong> ₱' . number_format($product['unit_price'], 2) . '</p>';
                      echo '<p><strong>Quantity:</strong> ' . htmlspecialchars($product['quantity']) . '</p>';
                      echo '<p><strong>Color:</strong> ' . htmlspecialchars($product['color']) . '</p>';
                      echo '<p><strong>Size:</strong> ' . ($product['size'] ? htmlspecialchars($product['size']) : 'N/A') . '</p>';
                      echo '<p><strong>Category:</strong> ' . htmlspecialchars($product['category_name']) . '</p>';
                  }
                  
                  // Close connection
                  $stmt->close();
                  $conn->close();
                  ?>
                </div>
              </div>
          
            </div>
        </main>
        <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Add "Back to Inventory" button
            const backButton = document.createElement('a');
            backButton.href = 'inventorylist.php';
            backButton.className = 'btn btn-back'; // Changed from 'btn-secondary' to 'btn-back'
            backButton.innerHTML = '<i class="bi bi-arrow-left"></i> Back to Inventory';

            // If you want to wrap it in a container
            const buttonContainer = document.createElement('div');
            buttonContainer.className = 'button-container';
            buttonContainer.appendChild(backButton);
            
            if (productDescSection) {
                productDescSection.insertAdjacentElement('beforebegin', buttonContainer);
            }

            // Profile dropdown functionality
            const profileDropdown = document.getElementById('profileDropdown');
            if (profileDropdown) {
                profileDropdown.addEventListener('click', function() {
                    const dropdownMenu = this.nextElementSibling;
                    dropdownMenu.classList.toggle('show');
                });
            }

            // Close dropdown when clicking outside
            window.addEventListener('click', function(event) {
                if (!event.target.matches('.dropdown-toggle')) {
                    const dropdowns = document.getElementsByClassName('dropdown-menu');
                    for (let i = 0; i < dropdowns.length; i++) {
                        const openDropdown = dropdowns[i];
                        if (openDropdown.classList.contains('show')) {
                            openDropdown.classList.remove('show');
                        }
                    }
                }
            });
        });
        </script>
        <script src="../js/new_sign_in.js"></script>
    </body>
</html>