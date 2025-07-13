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

// Fetch all active products from database
$products = [];
$sql = "SELECT pi.*, c.category_name 
        FROM product_inventory pi
        LEFT JOIN category c ON pi.category_id = c.id
        WHERE pi.is_active = 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Handle filtering
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';

// Apply filters
$filteredProducts = array_filter($products, function($product) use ($filter, $search) {
    $matchesFilter = $filter === 'All' || 
                    ($filter === 'Clothing' && $product['category_name'] === 'Clothing') ||
                    ($filter === 'Accessories' && $product['category_name'] === 'Accessories');
    
    $matchesSearch = empty($search) || 
                    stripos($product['product_name'], $search) !== false || 
                    stripos($product['product_description'], $search) !== false;
    
    return $matchesFilter && $matchesSearch;
});

// Pagination
$itemsPerPage = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$totalPages = ceil(count($filteredProducts) / $itemsPerPage);
$offset = ($currentPage - 1) * $itemsPerPage;
$paginatedProducts = array_slice($filteredProducts, $offset, $itemsPerPage);

// Count low stock items (quantity < 10)
$lowStockCount = count(array_filter($products, function($product) {
    return $product['quantity'] < 10;
}));
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="../styles/inventory.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <title>REKTA | Product Inventory</title>
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
                                    <a class="custom-nav-link custom-active" aria-current="page" href="inventorylist.php" title="Inventory">
                                        <i class="bi bi-box-seam fs-5"></i>
                                        <span class="d-none d-md-inline ms-2">INVENTORY</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="custom-nav-link" aria-current="page" href="orderlogs.html" title="Orders">
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
                <div class="container2-fluid px-4 py-3">
                    <div class="row g-4">
                        <div class="col-lg-8 p-4">
                            <form method="get" action="inventorylist.php" class="d-flex justify-content-between mb-3">
                                <select name="filter" id="filterDropdown" class="form-select" style="width: 200px;">
                                    <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>All</option>
                                    <option value="Clothing" <?php echo $filter === 'Clothing' ? 'selected' : ''; ?>Clothing</option>
                                    <option value="Accessories" <?php echo $filter === 'Accessories' ? 'selected' : ''; ?>Accessories</option>
                                </select>
                                <div class="input-group" style="width: 250px;">
                                    <input type="text" name="search" id="searchBox" class="form-control" placeholder="Search...">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                </div>
                            </form>
                
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
                                    <?php foreach ($paginatedProducts as $product): ?>
                                        <tr>
                                            <td class="text-center"><?php echo $product['id']; ?></td>
                                            <td><a href="invdesc.php?id=<?php echo $product['id']; ?>" class="product-link"><?php echo htmlspecialchars($product['product_name']); ?></a></td>
                                            <td><?php echo htmlspecialchars($product['product_description']); ?></td>
                                            <td class="text-center"><?php echo htmlspecialchars($product['color']); ?></td>
                                            <td class="text-center"><?php echo htmlspecialchars($product['size'] ?? 'N/A'); ?></td>
                                            <td class="text-center <?php echo $product['quantity'] < 10 ? 'text-danger fw-bold' : ''; ?>">
                                                <?php echo $product['quantity']; ?>
                                            </td>
                                            <td class="text-center">₱<?php echo number_format($product['unit_price'], 2); ?></td>
                                            <td class="text-center">
                                                <a href="delist.php?id=<?php echo $product['id']; ?>" class="btn btn-success btn-sm">Archive</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            
                            <nav aria-label="Page navigation">
                                <ul class="pagination">
                                    <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>&filter=<?php echo $filter; ?>&search=<?php echo urlencode($search); ?>">Prev</a>
                                    </li>
                                    
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&filter=<?php echo $filter; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>&filter=<?php echo $filter; ?>&search=<?php echo urlencode($search); ?>">Next</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                
                        <div class="col-lg-4 summary-section" style="padding-top: 1%;">
                            <div class="card inventory-summary-card">
                                <div class="card-header inventory-summary-header">
                                    <b>Summary & Actions</b>
                                </div>
                                <div class="card-body inventory-summary-body">
                                    <p class="summary-stat"><strong>Total Products:</strong> <span id="totalProducts"><?php echo count($products); ?></span></p>
                                    <p class="summary-stat"><strong>Low Stock Items:</strong> <span id="lowStock"><?php echo $lowStockCount; ?></span></p>
                                    <div id="lowStockList" class="low-stock-list-container">
                                        <?php 
                                        $lowStockItems = array_filter($products, function($product) {
                                            return $product['quantity'] < 10;
                                        });
                                        
                                        if (empty($lowStockItems)): ?>
                                            <div class="alert alert-warning py-2">No low stock items</div>
                                        <?php else: ?>
                                            <ul class="list-group">
                                                <?php foreach ($lowStockItems as $item): ?>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                                        <div>
                                                            <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                                            <div class="text-muted small">ID: <?php echo $item['id']; ?> | Qty: <?php echo $item['quantity']; ?></div>
                                                        </div>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <script src="../js/inventorydata.js"></script>
        <script src="../js/new_sign_in.js"></script>

    </body>
</html>
