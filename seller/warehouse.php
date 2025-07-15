<!-- User Role Label -->
<span class="text-muted small role" style="color: black;font-weight: 500;font-size: 18px;">Admin/Seller</span>
<?php
session_start();

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'school_db';

// Create connection
$mysqli = new mysqli("localhost", "root", "", "school_db");

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Remove JSON header for redirect
    
    // Get form data
    $productName = $_POST['product_name'] ?? '';
    $description = $_POST['product_description'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $category = $_POST['category'] ?? '';
    $quantity = intval($_POST['quantity'] ?? 0);
    $color = $_POST['color'] ?? '';
    $size = $_POST['size'] ?? null;
    
    // Validate required fields
    if (empty($productName)) {
        echo json_encode(['success' => false, 'message' => 'Product name is required']);
        exit;
    }
    
    if ($price <= 0) {
        echo json_encode(['success' => false, 'message' => 'Price must be greater than 0']);
        exit;
    }
    
    if ($quantity < 0) {
        echo json_encode(['success' => false, 'message' => 'Quantity cannot be negative']);
        exit;
    }
    
    if (empty($category)) {
        echo json_encode(['success' => false, 'message' => 'Category is required']);
        exit;
    }
    
    // Determine category ID
    $categoryId = ($category === 'Clothing') ? 1 : 2;
    
    try {
        // Insert into database
        $stmt = $mysqli ->prepare("
            INSERT INTO product_inventory 
            (product_name, product_description, unit_price, category_id, quantity, color, size, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 1)
        ");
        $stmt->bind_param('ssdisss', $productName, $description, $price, $categoryId, $quantity, $color, $size);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            // Redirect to inventorylist.php with success message
            header('Location: inventorylist.php?added=1');
            exit;
        } else {
            // Redirect with error
            header('Location: inventorylist.php?added=0');
            exit;
        }
    } catch (Exception $e) {
        header('Location: inventorylist.php?added=0');
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="../styles/warhus.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <title>REKTA | Add Product</title>
        <link rel="icon" type="image/x-icon" href="../assets/logo_stockflow.png">
        
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container-fluid">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center">
                        
                        <div class="logo" style="font-family: Milker; flex: 0 0 auto;">
                            <a href="landingpage.html" class="navbar-brand" style="font-family: Milker; font-size: 2.2rem; color: white; text-decoration: none; font-weight: 700; letter-spacing: 2px;">
                                rekta
                            </a>
                        </div>
                        
                        <ul class="nav nav-tabs border-0" style="margin-top: 6px;">
                            <li class="nav-item">
                                <a class="custom-nav-link" href="analytics.php" title="Dashboard">
                                    <i class="bi bi-speedometer2 fs-5"></i>
                                    <span class="d-none d-md-inline ms-2">DASHBOARD</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="custom-nav-link custom-active" href="warehouse.php" title="Add Product">
                                    <i class="bi bi-plus-square fs-5"></i>
                                    <span class="d-none d-md-inline ms-2">ADD PRODUCT</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="custom-nav-link" aria-current="page" href="inventorylist.php" title="Inventory">
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
                                        <span class="text-muted small role" style="color: black;font-weight: 500;font-size: 18px;">Admin/Seller</span>   
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
            <h1 class="row" style="padding-left: 58px;padding-top: 25px;padding-bottom: 28px;"><b>ADD PRODUCT</b></h1>
            <hr>
            <div class="container mt-5">
                <form method="post" enctype="multipart/form-data">
                    <div class="prodcontainer">
                        <!-- First Column -->
                        <div class="column">
                            <div class="form-group">
                                <label for="product-id">Product ID (Auto-generated)</label>
                                <input type="text" id="product-id" placeholder="Will be auto-generated" disabled>
                            </div>
                            <div class="form-group">
                                <label for="product-name">Product Name *</label>
                                <input type="text" id="product-name" name="product_name" placeholder="Input Name" required>
                            </div>
                            <div class="form-group">
                                <label for="product-description">Product Description</label>
                                <textarea id="product-description" name="product_description" rows="4" placeholder="Input Description"></textarea>
                            </div>
                        </div>
                        <!-- Second Column -->
                        <div class="column">
                            <div class="form-group">
                                <label for="price">Price *</label>
                                <input type="number" id="price" name="price" placeholder="500" min="0" step="0.01" required> 
                            </div>
                            <div class="form-group">
                                <label for="category">Category *</label>
                                <select id="category" name="category" class="form-select" required>
                                    <option value="">Select Category</option>
                                    <option value="Clothing">Clothing</option>
                                    <option value="Accessories">Accessories</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="quantity">Quantity *</label>
                                <input type="number" id="quantity" name="quantity" placeholder="20" min="0" required>
                            </div>
                            <div class="form-group">
                                <label for="color">Color</label>
                                <input type="text" id="color" name="color" placeholder="Red/Yellow/Blue/Black">
                            </div>
                        </div>
                        <!-- Third Column (Rightmost) -->
                        <div class="column">
                            <div class="form-group">
                                <label for="size">Size</label>
                                <select id="size" name="size" class="form-select">
                                    <option value="">Select Size</option>
                                    <option value="Small">Small</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Large">Large</option>
                                    <option value="N/A">N/A</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Product Images</label>
                                <div class="image-upload" id="imageUpload">Upload Image</div>
                                <input type="file" id="product-image" name="product_image" style="display: none;" accept="image/*">
                            </div>
                            <button class="submit-btn" type="submit">Add Product</button>
                        </div>
                    </div>
                </form>
            </div>
        </main>

        <!-- <script src="../js/addprod.js"></script> -->
        <script src="../js/new_sign_in.js"></script>

    </body>
</html> 