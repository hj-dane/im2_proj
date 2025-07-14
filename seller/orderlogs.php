<?php
// Database connection
$host = 'localhost';
$username = 's11820346';
$password = 'SEULRENE_kangseulgi';
$dbname = 's11820346_im2';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

// Handle POST requests for order processing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
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
            die(json_encode(['error' => 'Invalid action']));
    }
    
    $stmt = $conn->prepare("UPDATE trans_header SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $orderId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => $stmt->error]);
    }
    exit();
}

// Handle GET requests for data fetching
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    $action = $_GET['action'];
    
    try {
        switch ($action) {
            case 'get_orders':
                // ... existing get_orders code ...
                break;
                
            case 'get_order_details':
                // ... existing get_order_details code ...
                break;
                
            default:
                echo json_encode(['error' => 'Invalid action']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit();
}

// If no specific API action requested, show the full HTML page
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
                            <a href="landingpage.html">rekta</a>
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
                        <input type="text" id="searchBox" class="form-control w-25" placeholder="Search orders...">
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
                                <!-- Orders will be loaded here by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    
                    <ul class="pagination" id="paginationButtons">
                        <li class="page-item" id="prevPage">
                            <a class="page-link" href="#">Previous</a>
                        </li>
                        <li class="page-item" id="nextPage">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/orderlogs.js"></script>
</body>
</html>