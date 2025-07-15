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

// Handle restore action via PHP form POST
$restore_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'restore') {
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        $restore_message = '<div class="alert alert-danger">Invalid product ID</div>';
    } else {
        $productId = (int)$_POST['id'];
        try {
            $stmt = $mysqli->prepare("UPDATE product_inventory SET is_active = 1 WHERE id = ?");
            $stmt->bind_param('i', $productId);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $restore_message = '<div class="alert alert-success">Product restored successfully.</div>';
            } else {
                $restore_message = '<div class="alert alert-warning">Product not found or already restored.</div>';
            }
        } catch (Exception $e) {
            $restore_message = '<div class="alert alert-danger">Database error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

// Fetch archived products for display
$archived_products = [];
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
try {
    if ($search_term !== '') {
        $like = '%' . $mysqli->real_escape_string($search_term) . '%';
        $stmt = $mysqli->prepare("
            SELECT id, product_name, product_description, color, size, quantity, unit_price 
            FROM product_inventory 
            WHERE is_active IS NULL AND (product_name LIKE ? OR product_description LIKE ?)
            ORDER BY id DESC
        ");
        $stmt->bind_param('ss', $like, $like);
    } else {
        $stmt = $mysqli->prepare("
            SELECT id, product_name, product_description, color, size, quantity, unit_price 
            FROM product_inventory 
            WHERE is_active IS NULL
            ORDER BY id DESC
        ");
    }
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
                <div class="container2-fluid px-4 py-3">
                    <div class="row g-4">
                        <?php if (!empty($restore_message)) echo $restore_message; ?>
                        <div class="d-flex justify-content-end">
                            <form method="get" action="delist.php" style="width: 100%; max-width: 400px;">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search archived products..." value="<?php echo htmlspecialchars($search_term); ?>">
                                    <button class="btn btn-primary" type="submit">Search</button>
                                </div>
                            </form>
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
                                <?php if (!empty($archived_products)): ?>
                                    <?php foreach ($archived_products as $product): ?>
                                        <tr>
                                            <td class="text-center"><?php echo $product['id']; ?></td>
                                            <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                            <td><?php echo htmlspecialchars($product['product_description']); ?></td>
                                            <td class="text-center"><?php echo htmlspecialchars($product['color']); ?></td>
                                            <td class="text-center"><?php echo htmlspecialchars($product['size'] ?? 'N/A'); ?></td>
                                            <td class="text-center"><?php echo $product['quantity']; ?></td>
                                            <td class="text-center">₱<?php echo number_format($product['unit_price'], 2); ?></td>
                                            <td class="text-center">
                                                <form method="post" action="delist.php" style="display:inline;">
                                                    <input type="hidden" name="action" value="restore">
                                                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                                    <button type="submit" class="btn btn-primary btn-sm">Restore</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="8" class="text-center">No archived products found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
