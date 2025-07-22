<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $mysqli->prepare("SELECT user_type_id FROM user_login WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($user_type_id);
$stmt->fetch();
$stmt->close();

if ($user_type_id != 2) {
    header("Location: ../index.php");
    exit;
}

$logs = [];
try {
    $query = "
        SELECT 
            td.id AS log_id,
            td.product_id,
            pi.product_name,
            th.trans_date,
            td.qty_in,
            td.qty_out,
            pi.quantity
        FROM trans_details td
        JOIN trans_header th ON td.trans_header_id = th.id
        JOIN product_inventory pi ON td.product_id = pi.id
        ORDER BY th.trans_date DESC";

    $result = $mysqli->query($query);
    while ($row = $result->fetch_assoc()) {
        $activity = '';
        $qty_change = 0;
        $prev_qty = 0;
        $new_qty = $row['quantity'];

        if ($row['qty_in'] > 0) {
            $activity = 'Item IN';
            $qty_change = '+' .$row['qty_in'];
            $prev_qty = $new_qty - $qty_change;
        } elseif ($row['qty_out'] > 0) {
            $activity = 'Item OUT';
            $qty_change = '-' .$row['qty_out'];
            $prev_qty = $new_qty - $qty_change;
        } else {
            continue; // Skip if no movement
        }

        $logs[] = [
            'log_id' => $row['log_id'],
            'product_id' => $row['product_id'],
            'product_name' => $row['product_name'],
            'trans_date' => $row['trans_date'],
            'activity' => $activity,
            'qty_change' => $qty_change,
            'prev_qty' => $prev_qty,
            'new_qty' => $new_qty
        ];
    }
} catch (Exception $e) {
    $error = "Error loading logs: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>REKTA | Stock Logs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../styles/logs.css?v=5">
    <link rel="icon" type="image/x-icon" href="../assets/logo_stockflow.png">
</head>
<body>
    <div class="page-wrapper">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container-fluid">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center">
                        <a class="navbar-brand" href="../index.php">REKTA</a>
                        <ul class="nav nav-tabs border-0 ms-4">
                            <li class="nav-item"><a class="custom-nav-link" href="analytics.php"><i class="bi bi-speedometer2 fs-5"></i><span class="ms-2 d-none d-md-inline">DASHBOARD</span></a></li>
                            <li class="nav-item"><a class="custom-nav-link" href="warehouse.php"><i class="bi bi-plus-square fs-5"></i><span class="ms-2 d-none d-md-inline">ADD PRODUCT</span></a></li>
                            <li class="nav-item"><a class="custom-nav-link" href="inventorylist.php"><i class="bi bi-box-seam fs-5"></i><span class="ms-2 d-none d-md-inline">INVENTORY</span></a></li>
                            <li class="nav-item"><a class="custom-nav-link custom-active" href="stockin.php"><i class="bi bi-box-seam fs-5"></i><span class="ms-2 d-none d-md-inline">STOCKS LOGS</span></a></li>
                            <li class="nav-item"><a class="custom-nav-link" href="orderlogs.php"><i class="bi bi-box-seam fs-5"></i><span class="ms-2 d-none d-md-inline">ORDER LOGS</span></a></li>
                            <li class="nav-item"><a class="custom-nav-link" href="delist.php"><i class="bi bi-box-seam fs-5"></i><span class="ms-2 d-none d-md-inline">ARCHIVED ITEMS</span></a></li>
                        </ul>
                    </div>
                    <div class="d-flex align-items-center gap-4">
                        <div class="dropdown">
                            <button class="btn border-0 shadow-none p-0" type="button" data-bs-toggle="dropdown">
                                <img src="../assets/profile_icon.png" alt="Profile" class="rounded-circle" width="40" height="40">
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end p-0" style="width: 300px;">
                                <li class="px-3 pt-3 pb-2">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold name" style="color: black;font-size: 20px;">Admin</span>  
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

        <!-- Main Content -->
        <main class="main-content">
            <div class="container-fluid px-4 py-3">
                <h1 class="row" style="padding-bottom: 28px;"><b>STOCK MONITORING LOGS</b></h1>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">Log ID</th>
                                <th class="text-center">Product ID</th>
                                <th class="text-center">Product Name</th>
                                <th class="text-center">Date</th>
                                <th class="text-center">Activity</th>
                                <th class="text-center">Quantity Change</th>
                                <th class="text-center">Previous QTY</th>
                                <th class="text-center">New QTY</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($logs)): ?>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td class="text-center"><?php echo $log['log_id']; ?></td>
                                        <td class="text-center"><?php echo $log['product_id']; ?></td>
                                        <td class="text-center"><?php echo htmlspecialchars($log['product_name']); ?></td>
                                        <td class="text-center"><?php echo $log['trans_date']; ?></td>
                                        <td class="text-center"><?php echo $log['activity']; ?></td>
                                        <td class="text-center"><?php echo $log['qty_change']; ?></td>
                                        <td class="text-center"><?php echo $log['prev_qty']; ?></td>
                                        <td class="text-center"><?php echo $log['new_qty']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="8" class="text-center">No stock logs found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
