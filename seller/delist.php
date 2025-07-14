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

// API Endpoint: Get Archived Products
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_archived') {
    header('Content-Type: application/json');
    
    try {
        $stmt = $conn->prepare("
            SELECT id, product_name, product_description, color, size, quantity, unit_price 
            FROM product_inventory 
            WHERE is_active = 0
            ORDER BY id DESC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode($products);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// API Endpoint: Restore Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'restore') {
    header('Content-Type: application/json');
    
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
        exit;
    }

    $productId = (int)$_POST['id'];
    
    try {
        $stmt = $conn->prepare("UPDATE product_inventory SET is_active = 1 WHERE id = ?");
        $stmt->bind_param('i', $productId);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Product restored successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Product not found or already restored']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

// Regular Page Display
$archived_products = [];
try {
    $stmt = $conn->prepare("
        SELECT id, product_name, product_description, color, size, quantity, unit_price 
        FROM product_inventory 
        WHERE is_active = 0
        ORDER BY id DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $archived_products = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $error = "Error loading archived products: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="../styles/delist.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <title>REKTA | Archived Items</title>
        <link rel="icon" type="image/x-icon" href="../assets/logo_stockflow.png">
        
    </head>
    <body>
        <div class="page-wrapper">
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
                                    <a class="custom-nav-link " aria-current="page" href="inventorylist.php" title="Inventory">
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
                                    <a class="custom-nav-link custom-active" aria-current="page" href="delist.php" title="Archive">
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
                <div class="container2-fluid px-4 py-3">
                    <div class="row g-4">
                        <div class="d-flex justify-content-between mb-3">
                            <input type="text" id="searchBox" class="form-control w-25" placeholder="Search...">
                        </div>
            
                        <table class="table table-bordered" id="dataTable">
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Product Name</th>
                                    <th class="text-center">Description</th>
                                    <th class="text-center">Color</th>
                                    <th class="text-center">Size</th>
                                    <th class="text-center">Quantity (Pcs)</th>
                                    <th class="text-center">Unit Price</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="inventoryTable">
                                <!-- inventory data will be loaded by JavaScript -->
                            </tbody>
                        </table>
                        <ul class="pagination" id="paginationButtons">
                            <li class="page-item" id="prevPage">
                                <a class="page-link" href="#">Prev</a>
                            </li>
                            <li class="page-item" id="nextPage">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </main>
        </div>
        
        <script src="../js/inventorydata2.js"></script>
        <script src="../js/new_sign_in.js"></script>

    </body>
</html>
