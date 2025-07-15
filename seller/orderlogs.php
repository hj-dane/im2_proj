<!-- User Role Label -->
<span class="text-muted small role" style="color: black;font-weight: 500;font-size: 18px;">Admin/Seller</span>
<?php
// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'school_db';

// Create connection
$mysqli = new mysqli("localhost", "root", "", "school_db");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Handle POST requests for order processing
$order_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $orderId = $_POST['order_id'];
    $action = $_POST['action'];
    switch ($action) {
        case 'confirm':
            $status = 'Preparing';
            break;
        case 'cancel':
            $status = 'Cancelled';
            break;
        case 'complete':
            $status = 'Completed';
            break;
        default:
            $order_message = '<div class="alert alert-danger">Invalid action</div>';
    }
    if (!empty($status)) {
        $stmt = $mysqli->prepare("UPDATE trans_header SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $orderId);
        if ($stmt->execute()) {
            $order_message = '<div class="alert alert-success">Order status updated to ' . htmlspecialchars($status) . '.</div>';
        } else {
            $order_message = '<div class="alert alert-danger">Error updating order: ' . htmlspecialchars($stmt->error) . '</div>';
        }
    }
}

// Fetch all orders for display
$orders = [];
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
try {
    if ($search_term !== '') {
        $like = '%' . $mysqli->real_escape_string($search_term) . '%';
        $stmt = $mysqli->prepare("SELECT id, order_date, customer_name, total_amount, payment_method, payment_status, delivery_type, status FROM trans_header WHERE id LIKE ? OR customer_name LIKE ? ORDER BY order_date DESC");
        $stmt->bind_param('ss', $like, $like);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $mysqli->query("SELECT id, order_date, customer_name, total_amount, payment_method, payment_status, delivery_type, status FROM trans_header ORDER BY order_date DESC");
    }
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
} catch (Exception $e) {
    $error = "Error loading orders: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../styles/orders.css">
    <title>REKTA | Order Logs</title>
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
                                <a class="custom-nav-link" href="analytics.php">
                                    <i class="bi bi-speedometer2 fs-5"></i>
                                    <span class="d-none d-md-inline ms-2">DASHBOARD</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="custom-nav-link" href="warehouse.php">
                                    <i class="bi bi-plus-square fs-5"></i>
                                    <span class="d-none d-md-inline ms-2">ADD PRODUCT</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="custom-nav-link" href="inventorylist.php">
                                    <i class="bi bi-box-seam fs-5"></i>
                                    <span class="d-none d-md-inline ms-2">INVENTORY</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="custom-nav-link custom-active" href="orderlogs.php">
                                    <i class="bi bi-box-seam fs-5"></i>
                                    <span class="d-none d-md-inline ms-2">ORDER LOGS</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="custom-nav-link" href="delist.php">
                                    <i class="bi bi-box-seam fs-5"></i>
                                    <span class="d-none d-md-inline ms-2">ARCHIVED ITEMS</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="d-flex align-items-center gap-4">
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
                    <?php if (!empty($order_message)) echo $order_message; ?>
                    <div class="d-flex justify-content-end">
                        <form method="get" action="orderlogs.php" style="width: 100%; max-width: 400px;">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search orders by ID or customer..." value="<?php echo htmlspecialchars($search_term); ?>">
                                <button class="btn btn-primary" type="submit">Search</button>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable">
                            <thead>
                                <tr>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Customer</th>
                                    <th class="text-center">Total Amount</th>
                                    <th class="text-center">Payment Method</th>
                                    <th class="text-center">Payment Status</th>
                                    <th class="text-center">Delivery Type</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="inventoryTable">
                                <?php if (!empty($orders)): ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td class="text-center"><?php echo htmlspecialchars($order['order_date']); ?></td>
                                            <td class="text-center"><?php echo $order['id']; ?></td>
                                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                            <td class="text-center">₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td class="text-center"><?php echo htmlspecialchars($order['payment_method']); ?></td>
                                            <td class="text-center"><?php echo htmlspecialchars($order['payment_status']); ?></td>
                                            <td class="text-center"><?php echo htmlspecialchars($order['delivery_type']); ?></td>
                                            <td class="text-center"><?php echo htmlspecialchars($order['status']); ?></td>
                                            <td class="text-center">
                                                <form method="post" action="orderlogs.php" style="display:inline;">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                    <input type="hidden" name="action" value="confirm">
                                                    <button type="submit" class="btn btn-success btn-sm">Confirm</button>
                                                </form>
                                                <form method="post" action="orderlogs.php" style="display:inline;">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                    <input type="hidden" name="action" value="complete">
                                                    <button type="submit" class="btn btn-primary btn-sm">Complete</button>
                                                </form>
                                                <form method="post" action="orderlogs.php" style="display:inline;">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                    <input type="hidden" name="action" value="cancel">
                                                    <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="9" class="text-center">No orders found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination and search removed for PHP-only version -->
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Removed JS for orderlogs.js -->
</body>
</html>