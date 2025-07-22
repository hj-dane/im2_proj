<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}



if ($is_logged_in) {
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


$inventoryData = [];
$query = "SELECT * FROM product_inventory";
$result = $mysqli->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $inventoryData[] = $row;
    }
}

// Convert inventory data to JSON for JavaScript
$inventoryDataJson = json_encode($inventoryData);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="../styles/analys.css?v=5">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <title>REKTA | Dashboard</title>
        <link rel="icon" type="image/x-icon" href="../assets/logo_stockflow.png">
        
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container-fluid">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center">
                            <a class="navbar-brand" href="../index.php">REKTA</a>
                            <ul class="nav nav-tabs border-0 ms-4">
                                <li class="nav-item"><a class="custom-nav-link custom-active" href="analytics.php"><i class="bi bi-speedometer2 fs-5"></i><span class="ms-2 d-none d-md-inline">DASHBOARD</span></a></li>
                                <li class="nav-item"><a class="custom-nav-link" href="warehouse.php"><i class="bi bi-plus-square fs-5"></i><span class="ms-2 d-none d-md-inline">ADD PRODUCT</span></a></li>
                                <li class="nav-item"><a class="custom-nav-link" href="inventorylist.php"><i class="bi bi-box-seam fs-5"></i><span class="ms-2 d-none d-md-inline">INVENTORY</span></a></li>
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
            <h1 style="padding-left: 58px;padding-bottom: 28px;"><b>RECENT ACTIVITY</b></h1>
            <hr>
            <div class="dashboard">
                <div class="parent">
                    <div class="div1" style="margin-top: 35px;">  
                        <div class="chart-container">
                            <h2 style="padding-bottom: 35px;">Inventory by Category</h2>
                            <div class="chart-wrapper">
                                <canvas id="categoryChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="div3"> 
                        <div class="card-container">
                            <div class="card mb-3">
                              <div class="card-header text-white">
                                <h5 class="mb-0">Total Stock</h5>
                              </div>
                              <div class="card-body">
                                <p class="display-5 mb-0" id="totalProducts">0</p>
                                <footer class="blockquote-footer mt-2">All inventory items</footer>
                              </div>
                            </div>
                        
                            <div class="card mb-3">
                              <div class="card-header text-white">
                                <h5 class="mb-0">Low in Stock</h5>
                              </div>
                              <div class="card-body">
                                <p class="display-5 mb-0" id="lowStock">0</p>
                                <footer class="blockquote-footer mt-2">Items below threshold</footer>
                              </div>
                            </div>
                          </div>
                    </div>
                </div>
            </div>
        </main>

        <script>
            // Pass PHP data to JavaScript
            const inventoryData = <?php echo $inventoryDataJson; ?>;
        </script>
        <script src="../js/analysischart.js?v=8" defer></script>
        <script src="../js/new_sign_in.js"></script>
    </body>
</html>
