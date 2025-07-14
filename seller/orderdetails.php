<?php
// Database connection
$host = 'admin.dcism.org';
$user = 's11820346';
$pass = 'SEULRENE_kangseulgi';
$db = 's11820346_im2';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get order ID from URL or use a default
    $orderId = isset($_GET['order_id']) ? $_GET['order_id'] : 'SO-00004';
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $action = $_POST['action'];
        $orderId = $_POST['order_id'];
        
        if ($action === 'confirm') {
            $stmt = $conn->prepare("UPDATE trans_header SET status = 'Preparing' WHERE id = :order_id");
            $stmt->bindParam(':order_id', $orderId);
            $stmt->execute();
            
            header("Location: orderlogs.php");
            exit();
        } elseif ($action === 'cancel') {
            $stmt = $conn->prepare("UPDATE trans_header SET status = 'Cancelled' WHERE id = :order_id");
            $stmt->bindParam(':order_id', $orderId);
            $stmt->execute();
            
            header("Location: orderlogs.php");
            exit();
        }
    }
    
    // Fetch order header info
    $stmt = $conn->prepare("SELECT id as order_id, trans_date as order_date, status FROM trans_header WHERE id = :order_id");
    $stmt->bindParam(':order_id', $orderId);
    $stmt->execute();
    $orderHeader = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Fetch order items
    $stmt = $conn->prepare("
        SELECT pi.product_name, td.price, pi.weight, pi.stock, pi.sku, td.qty_out as quantity
        FROM trans_details td
        JOIN product_inventory pi ON td.product_id = pi.id
        WHERE td.trans_header_id = :order_id
    ");
    $stmt->bindParam(':order_id', $orderId);
    $stmt->execute();
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate order total
    $orderTotal = 0;
    foreach ($orderItems as $item) {
        $orderTotal += $item['price'] * $item['quantity'];
    }
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
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
            <div class="container-orderdescc">
                <h1 class="order-title"><b>ORDER DETAILS</b></h1>
                <hr>
                <div class="order-container">
                    <div class="order-header">
                        <div class="order-id">Order ID: <?php echo htmlspecialchars($orderHeader['order_id'] ?? 'SO-00004'); ?></div>
                        <div class="order-date"> Date:
                            <?php 
                            if (isset($orderHeader['order_date'])) {
                                echo date('m/d/Y h:i A', strtotime($orderHeader['order_date']));
                            } else {
                                echo '06/13/2022 06:24 AM'; // Default if no date in DB
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
                                        <span>weight: <?php echo htmlspecialchars($item['weight']); ?></span>
                                        <span>Available Stock: <?php echo htmlspecialchars($item['stock']); ?></span>
                                        <span>SKU: <?php echo htmlspecialchars($item['sku']); ?></span>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <a href="#" class="show-more">[Show More]</a>
                    
                    <div class="order-total">
                        <span class="order-total-label">Order Total</span>
                        <span class="order-total-amount">₱<?php echo number_format($orderTotal, 2); ?></span>
                    </div>
                </div>
                <form method="post" action="orderdetails.php" class="action-buttons">
                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($orderId); ?>">
                    <button type="submit" name="action" value="confirm" class="btn btn-confirm">Confirm Order</button>
                    <button type="submit" name="action" value="cancel" class="btn btn-cancel">Cancel Order</button>
                </form>
            </div>
        </main>
        
        <script>
            // Handle show more functionality
            document.querySelector('.show-more')?.addEventListener('click', function(e) {
                e.preventDefault();
                // This would show hidden rows in the table
                alert('Show more functionality would go here');
            });

            // Handle order actions
            document.querySelector('.btn-confirm')?.addEventListener('click', function(e) {
                if(!confirm('Are you sure you want to confirm this order?')) {
                    e.preventDefault();
                }
            });

            document.querySelector('.btn-cancel')?.addEventListener('click', function(e) {
                if(!confirm('Are you sure you want to cancel this order?')) {
                    e.preventDefault();
                }
            });
        </script>
        <script src="../js/new_sign_in.js"></script>
    </body>
</html>