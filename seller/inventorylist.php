<!-- User Role Label -->
<span class="text-muted small role" style="color: black;font-weight: 500;font-size: 18px;">Admin/Seller</span>
<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if (isset($_SESSION['user_id'])) {

    $user_id = $_SESSION['user_id'];

    $stmt = $mysqli->prepare("SELECT user_type_id FROM user_login WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($user_type_id);
    $stmt->fetch();
    $stmt->close();

    if ($user_type_id != 2) {  // 🚨 Only allow user_type = 1 (customer)
        header("Location: ../index.php");
        exit;
    }
}

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Fetch all active products from database
$products = [];
$sql = "
SELECT 
    pi.*, 
    td.id AS trans_id,
    td.qty_in,
    c.category_name
FROM product_inventory pi
LEFT JOIN (
    SELECT td1.*
    FROM trans_details td1
    INNER JOIN (
        SELECT product_id, MAX(id) AS max_id
        FROM trans_details
        GROUP BY product_id
    ) td2 ON td1.product_id = td2.product_id AND td1.id = td2.max_id
) td ON pi.id = td.product_id
LEFT JOIN category c ON pi.category_id = c.id
WHERE pi.is_active = 1
";
$result = $mysqli->query($sql);

// Debug: Check for query errors
if ($result === false) {
    die('<div style="background: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border: 1px solid #f5c6cb;">Query error: ' . $mysqli->error . '</div>');
}

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Normalize keys to lower-case for consistency
        $row = array_change_key_case($row, CASE_LOWER);
        $products[] = $row;
    }
}
// Debug: Print products array to check data
if (empty($products)) {
    echo '<div style="background: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border: 1px solid #f5c6cb;">No products found in <b>product_inventory</b> with is_active = 1.</div>';
} 

// Handle filtering
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';

// Apply filters
$filteredProducts = array_filter($products, function($product) use ($filter, $search) {
    $matchesFilter = $filter === 'all' || 
        ($filter === 'Clothing' && isset($product['category_name']) && strcasecmp($product['category_name'], 'Clothing') === 0) ||
        ($filter === 'Accessories' && isset($product['category_name']) && strcasecmp($product['category_name'], 'Accessories') === 0);
    $matchesSearch = empty($search) || 
        (isset($product['product_name']) && stripos($product['product_name'], $search) !== false) || 
        (isset($product['product_description']) && stripos($product['product_description'], $search) !== false);
    return $matchesFilter && $matchesSearch;
});

// Adding inventory qty
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? null;
    $trans_id = $_POST['trans_id'] ?? null;
    $add_qty = $_POST['add_qty'] ?? null;

    if ($product_id && $trans_id && is_numeric($add_qty)) {
        // Start transaction for safety
        $mysqli->begin_transaction();

        try {
            // Update product_inventory
            $stmt1 = $mysqli->prepare("UPDATE product_inventory SET quantity = quantity + ? WHERE id = ?");
            $stmt1->bind_param("ii", $add_qty, $product_id);
            $stmt1->execute();
            $stmt1->close();

            // Update trans_details
            $stmt2 = $mysqli->prepare("UPDATE trans_details SET qty_in = ? WHERE id = ?");
            $stmt2->bind_param("ii", $add_qty, $trans_id);
            $stmt2->execute();
            $stmt2->close();

            // Commit both changes
            $mysqli->commit();

            // Redirect on success
            header("Location: inventorylist.php?updated=1");
            exit;

        } catch (Exception $e) {
            $mysqli->rollback(); // Roll back both updates on error
            echo "❌ Error updating inventory: " . $e->getMessage();
        }
    } else {
        echo "❌ Missing or invalid data.";
    }
}



// Pagination
$itemsPerPage = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$totalPages = ceil(count($filteredProducts) / $itemsPerPage);
$offset = ($currentPage - 1) * $itemsPerPage;
$paginatedProducts = array_slice($filteredProducts, $offset, $itemsPerPage);

// Count low stock items (quantity < 20)
$lowStockCount = count(array_filter($products, function($product) {
    return $product['quantity'] < 20;
}));
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../styles/inventory.css?v=20">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <title>REKTA | Product Inventory</title>
        <link rel="icon" type="image/x-icon" href="../assets/logo_stockflow.png">
    </head>
    <body>
        <div class="page-wrapper">
            <nav class="navbar navbar-expand-lg navbar-custom">
                <div class="container-fluid">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="d-flex align-items-center">
                            <a class="navbar-brand" href="../index.php">REKTA</a>
                            <ul class="nav nav-tabs border-0 ms-4">
                                <li class="nav-item"><a class="custom-nav-link" href="analytics.php"><i class="bi bi-speedometer2 fs-5"></i><span class="ms-2 d-none d-md-inline">DASHBOARD</span></a></li>
                                <li class="nav-item"><a class="custom-nav-link" href="warehouse.php"><i class="bi bi-plus-square fs-5"></i><span class="ms-2 d-none d-md-inline">ADD PRODUCT</span></a></li>
                                <li class="nav-item"><a class="custom-nav-link custom-active" href="inventorylist.php"><i class="bi bi-box-seam fs-5"></i><span class="ms-2 d-none d-md-inline">INVENTORY</span></a></li>
                                <li class="nav-item"><a class="custom-nav-link" href="stockin.php"><i class="bi bi-box-seam fs-5"></i><span class="ms-2 d-none d-md-inline">STOCKS LOGS</span></a></li>
                                <li class="nav-item"><a class="custom-nav-link" href="orderlogs.php"><i class="bi bi-box-seam fs-5"></i><span class="ms-2 d-none d-md-inline">ORDER LOGS</span></a></li>
                                <li class="nav-item"><a class="custom-nav-link" href="delist.php"><i class="bi bi-box-seam fs-5"></i><span class="ms-2 d-none d-md-inline">ARCHIVED ITEMS</span></a></li>
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
                                            <a href="../login.php" class="btn btn-danger">Logout</a>
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
                            <form method="get" action="inventorylist.php" class="d-flex justify-content-between align-items-center mb-3" id="filterForm">
                                <div class="d-flex align-items-center">
                                    <select name="filter" id="filterDropdown" class="form-select me-2" style="width: 200px;">
                                        <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All</option>
                                        <option value="Clothing" <?php echo $filter === 'Clothing' ? 'selected' : ''; ?>>Clothing</option>
                                        <option value="Accessories" <?php echo $filter === 'Accessories' ? 'selected' : ''; ?>>Accessories</option>
                                    </select>
                                </div>

                                <div style="max-width: 300px; width: 100%;">
                                    <div class="input-group">
                                        <input type="text" name="search" id="searchBox" class="form-control" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>
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
                                        <th class="text-center">Quantity</th>
                                        <th class="text-center">Unit Price</th>
                                        <th class="text-center">Add Qty</th>
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
                                            <td>
                                                <form method="POST" class="d-flex align-items-center gap-2">
                                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                    <input type="hidden" name="trans_id" value="<?= $product['trans_id'] ?? '' ?>"> 
                                                    <input type="number" name="add_qty" class="form-control" min="0" step="1" required>
                                                    <button type="submit" class="btn btn-success btn-sm">Confirm</button>
                                                </form>
                                            </td>
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
                                            return $product['quantity'] < 20;
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
        <script src="../js/new_sign_in.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('filterDropdown').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
        });
        document.addEventListener('DOMContentLoaded', function () {
            <?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
                var updateModal = new bootstrap.Modal(document.getElementById('updateSuccessModal'));
                updateModal.show();
            <?php endif; ?>

            <?php if (isset($_GET['archived']) && $_GET['archived'] == 1): ?>
                var archiveModal = new bootstrap.Modal(document.getElementById('archiveSuccessModal'));
                archiveModal.show();
            <?php endif; ?>
        });
        </script>
        <?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
            <!-- ✅ Update Success Modal -->
            <div class="modal fade" id="updateSuccessModal" tabindex="-1" aria-labelledby="updateSuccessLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-success">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="updateSuccessLabel">Success</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    ✅ Quantity successfully updated.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
                </div>
                </div>
            </div>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['archived']) && $_GET['archived'] == 1): ?>
            <!-- 📦 Archive Success Modal -->
            <div class="modal fade" id="archiveSuccessModal" tabindex="-1" aria-labelledby="archiveSuccessLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-success">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="archiveSuccessLabel">Archived</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    📦 Product has been archived successfully.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
                </div>
                </div>
            </div>
            </div>
            <?php endif; ?>
    </body>
</html>
