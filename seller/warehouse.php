<?php
session_start();

// Database configuration
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

if (!isset($_SESSION['user_id'])) {
    header("Location: sign.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
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
        $stmt = $conn->prepare("
            INSERT INTO product_inventory 
            (product_name, product_description, unit_price, category_id, quantity, color, size, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 1)
        ");
        $stmt->bind_param('ssdisss', $productName, $description, $price, $categoryId, $quantity, $color, $size);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Product added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add product']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
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
            <h1 class="row" style="padding-left: 58px;padding-top: 25px;padding-bottom: 28px;"><b>ADD PRODUCT</b></h1>
            <hr>
            <div class="container mt-5">
                <div id="alert-container"></div>
                <div class="prodcontainer">
                    
                    <!-- First Column -->
                    <div class="column">
                        <div class="form-group">
                            <label for="product-id">Product ID (Auto-generated)</label>
                            <input type="text" id="product-id" placeholder="Will be auto-generated" disabled>
                        </div>
                        <div class="form-group">
                            <label for="product-name">Product Name *</label>
                            <input type="text" id="product-name" placeholder="Input Name" required>
                        </div>
                        <div class="form-group">
                            <label for="product-description">Product Description</label>
                            <textarea id="product-description" rows="4" placeholder="Input Description"></textarea>
                        </div>
                    </div>
                    
                    <!-- Second Column -->
                    <div class="column">
                        <div class="form-group">
                            <label for="price">Price *</label>
                            <input type="number" id="price" placeholder="500" min="0" step="0.01" required> 
                        </div>
                        <div class="form-group">
                            <label for="category">Category *</label>
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    Select Category
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
                                    <li><button class="dropdown-item" type="button">Clothing</button></li>
                                    <li><button class="dropdown-item" type="button">Accessories</button></li>
                                </ul>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Quantity *</label>
                            <input type="number" id="quantity" placeholder="20" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="color">Color</label>
                            <input type="text" id="color" placeholder="Red/Yellow/Blue/Black">
                        </div>
                    </div>
                    
                    <!-- Third Column (Rightmost) -->
                    <div class="column">
                        <div class="form-group">
                            <label for="size">Size</label>
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="sizeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    Select Size
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="sizeDropdown">
                                    <li><button class="dropdown-item" type="button">Small</button></li>
                                    <li><button class="dropdown-item" type="button">Medium</button></li>
                                    <li><button class="dropdown-item" type="button">Large</button></li>
                                    <li><button class="dropdown-item" type="button">N/A</button></li>
                                </ul>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Product Images</label>
                            <div class="image-upload" id="imageUpload">Upload Image</div>
                            <input type="file" id="product-image" style="display: none;" accept="image/*">
                        </div>
                        <button class="submit-btn" id="submit-product">Add Product</button>
                    </div>
                </div>
            </div>
        </main>

        <script src="../js/addprod.js"></script>
        <script src="../js/inventorydata.js"></script>
        <script src="../js/new_sign_in.js"></script>

    </body>
</html> 