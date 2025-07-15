<!-- User Role Label -->
<span class="text-muted small role" style="color: black;font-weight: 500;font-size: 18px;">Admin/Seller</span>
<?php
// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'school_db';

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get order ID from URL or use a default (should be integer)
$orderId = isset($_GET['order_id']) ? intval($_GET['order_id']) : 1;

// Handle form submission
$order_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $orderId = intval($_POST['order_id']);
    if ($action === 'confirm') {
        $stmt = $mysqli->prepare("UPDATE trans_header SET status = 'Preparing' WHERE id = ?");
        $stmt->bind_param('i', $orderId);
        if ($stmt->execute()) {
            $order_message = '<div class="alert alert-success">Order confirmed and set to Preparing.</div>';
        } else {
            $order_message = '<div class="alert alert-danger">Error confirming order: ' . htmlspecialchars($stmt->error) . '</div>';
        }
    } elseif ($action === 'cancel') {
        $stmt = $mysqli->prepare("UPDATE trans_header SET status = 'Cancelled' WHERE id = ?");
        $stmt->bind_param('i', $orderId);
        if ($stmt->execute()) {
            $order_message = '<div class="alert alert-warning">Order cancelled.</div>';
        } else {
            $order_message = '<div class="alert alert-danger">Error cancelling order: ' . htmlspecialchars($stmt->error) . '</div>';
        }
    }
}

// Fetch order header info
$orderHeader = [];
$stmt = $mysqli->prepare("SELECT id as order_id, trans_date as order_date FROM trans_header WHERE id = ?");
$stmt->bind_param('i', $orderId);
$stmt->execute();
$result = $stmt->get_result();
$orderHeader = $result->fetch_assoc();

// Fetch order items
$orderItems = [];
$stmt = $mysqli->prepare("
    SELECT td.id, td.product_id, td.qty_out as quantity, td.price, td.amount,
           pi.product_name, pi.quantity
    FROM trans_details td
    LEFT JOIN product_inventory pi ON td.product_id = pi.id
    WHERE td.trans_header_id = ?
");
$stmt->bind_param('i', $orderId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $orderItems[] = $row;
}

// Calculate order total
$orderTotal = 0;
foreach ($orderItems as $item) {
    $orderTotal += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="../styles/orderdesc.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <title>REKTA | Order Details</title>
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
                                <a class="custom-nav-link" href="warehouse.php" title="Add Product">
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
                                <a class="custom-nav-link custom-active" aria-current="page" href="orderlogs.php" title="Orders">
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
            <div class="container-orderdescc">
                <h1 class="order-title">ORDER DETAILS</h1>
        <?php if (empty($orderItems)): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border: 1px solid #f5c6cb; border-radius: 4px;">
            <strong>No order details found for this order.</strong>
        </div>
        <?php endif; ?>
                <hr>
                <?php if (!empty($order_message)) echo $order_message; ?>
                <!-- No debug info -->
                <div class="order-container">
                    <div class="order-header">
                        <div class="order-id">Order ID: <?php echo htmlspecialchars($orderHeader['order_id'] ?? $orderId); ?></div>
                        <div class="order-date"> Date:
                            <?php 
                            if (isset($orderHeader['order_date'])) {
                                echo date('m/d/Y h:i A', strtotime($orderHeader['order_date']));
                            } else {
                                echo 'N/A'; // Default if no date in DB
                            }
                            ?>
                        </div>
                    </div>
                    <table class="product-table">
                        <thead>
                            <tr>
                                <th>PRODUCT</th>
                                <th>QUANTITY</th>
                                <th>AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderItems as $item): ?>
                            <tr>
                                <td>
                                    <div class="product-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                    <div class="product-price">₱<?php echo number_format($item['price'], 2); ?></div>
                                    <div class="product-details">
                                        <span>Available Quantity: <?php echo isset($item['quantity']) ? htmlspecialchars($item['quantity']) : 'N/A'; ?></span>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td>₱<?php echo number_format($item['amount'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="order-total">
                        <span class="order-total-label">Order Total</span>
                        <span class="order-total-amount">₱<?php echo number_format($orderTotal, 2); ?></span>
                    </div>
                </div>
                <div class="action-buttons">
                    <button type="button" class="btn btn-confirm" onclick="showOrderMessage('confirmed')">Confirm Order</button>
                    <button type="button" class="btn btn-cancel" onclick="showOrderMessage('cancelled')">Cancel Order</button>
                </div>
                <div id="order-action-message" style="display:none; margin-top:15px;"></div>
                <script>
                function showOrderMessage(action) {
                    var msgDiv = document.getElementById('order-action-message');
                    if (action === 'confirmed') {
                        msgDiv.innerHTML = '<div class="alert alert-success">Order confirmed and set to Preparing.</div>';
                    } else if (action === 'cancelled') {
                        msgDiv.innerHTML = '<div class="alert alert-warning">Order cancelled.</div>';
                    }
                    msgDiv.style.display = 'block';
                }
                </script>
            </div>
        </main>
        <script src="../js/new_sign_in.js"></script>
    </body>
</html>