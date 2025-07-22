<!-- User Role Label -->
<span class="text-muted small role" style="color: black; font-weight: 500; font-size: 18px;">Admin/Seller</span>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>REKTA | Product Details</title>
    <link rel="icon" type="image/x-icon" href="../assets/logo_stockflow.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/invdesc.css?v=5">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between w-100">
            <div class="d-flex align-items-center">
                            <a class="navbar-brand" href="../index.php">REKTA</a>
                            <ul class="nav nav-tabs border-0 ms-4">
                                <li class="nav-item"><a class="custom-nav-link" href="analytics.php"><i class="bi bi-speedometer2 fs-5"></i><span class="ms-2 d-none d-md-inline">DASHBOARD</span></a></li>
                                <li class="nav-item"><a class="custom-nav-link" href="warehouse.php"><i class="bi bi-plus-square fs-5"></i><span class="ms-2 d-none d-md-inline">ADD PRODUCT</span></a></li>
                                <li class="nav-item"><a class="custom-nav-link custom-active" href="inventorylist.php"><i class="bi bi-box-seam fs-5"></i><span class="ms-2 d-none d-md-inline">INVENTORY</span></a></li>
                                <li class="nav-item"><a class="custom-nav-link " href="stockin.php"><i class="bi bi-box-seam fs-5"></i><span class="ms-2 d-none d-md-inline">STOCKS LOGS</span></a></li>
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
                    <ul class="dropdown-menu dropdown-menu-end p-0" style="width: 300px;">
                        <li class="px-3 pt-3 pb-2">
                            <div class="d-flex flex-column">
                                <span class="fw-bold name" style="color: black; font-size: 20px;"></span>
                                <span class="text-muted small role" style="color: black; font-weight: 500; font-size: 18px;">Admin/Seller</span>
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
    <div class="container-itemdescc">
        <h1 class="product-title"><b>Product Details</b></h1>
        <hr>
        <div class="row-top">
            <div class="infor" id="productDescSection">
                <?php
                require_once '../config.php';
                if ($mysqli->connect_error) {
                    die("Connection failed: " . $mysqli->connect_error);
                }

                $productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

                $sql = "SELECT pi.*, c.category_name 
                        FROM product_inventory pi
                        LEFT JOIN category c ON pi.category_id = c.id
                        WHERE pi.id = ?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("i", $productId);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $product = $result->fetch_assoc();

                    echo '<h2><strong>' . htmlspecialchars($product['product_name']) . '</strong></h2>';
                    echo '<p>' . htmlspecialchars($product['product_description']) . '</p>';

                    $imageFile = $product['images'];
                    $imagePath = '../assets/' . $imageFile;

                    if (!empty($imageFile) && file_exists($imagePath)) {
                        echo '<div class="product-image-container" style="margin-top: 20px;">';
                        echo '<img src="' . $imagePath . '" alt="Product Image" style="max-width: 100%; max-height: 400px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">';
                        echo '</div>';
                    } else {
                        echo '<div class="no-image" style="margin-top: 20px; color: #666;">No image available</div>';
                    }
                } else {
                    echo "<p>Product not found.</p>";
                }
                ?>
            </div>

            <div class="infor" id="productDetailSection">
                <?php
                if (isset($product)) {
                    echo '<p><strong>Price:</strong> ₱' . number_format($product['unit_price'], 2) . '</p>';
                    echo '<p><strong>Quantity:</strong> ' . htmlspecialchars($product['quantity']) . '</p>';
                    echo '<p><strong>Color:</strong> ' . htmlspecialchars($product['color']) . '</p>';
                    echo '<p><strong>Size:</strong> ' . ($product['size'] ? htmlspecialchars($product['size']) : 'N/A') . '</p>';
                    echo '<p><strong>Category:</strong> ' . htmlspecialchars($product['category_name']) . '</p>';
                }

                $stmt->close();
                $mysqli->close();
                ?>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const backButton = document.createElement('a');
    backButton.href = 'inventorylist.php';
    backButton.className = 'btn btn-back';
    backButton.innerHTML = '<i class="bi bi-arrow-left"></i> Back to Inventory';

    const buttonContainer = document.createElement('div');
    buttonContainer.className = 'button-container';
    buttonContainer.appendChild(backButton);

    const productDescSection = document.getElementById('productDescSection');
    if (productDescSection) {
        productDescSection.insertAdjacentElement('beforebegin', buttonContainer);
    }

    const profileDropdown = document.getElementById('profileDropdown');
    if (profileDropdown) {
        profileDropdown.addEventListener('click', function () {
            const dropdownMenu = this.nextElementSibling;
            dropdownMenu.classList.toggle('show');
        });
    }

    window.addEventListener('click', function (event) {
        if (!event.target.matches('.dropdown-toggle')) {
            const dropdowns = document.getElementsByClassName('dropdown-menu');
            for (let i = 0; i < dropdowns.length; i++) {
                if (dropdowns[i].classList.contains('show')) {
                    dropdowns[i].classList.remove('show');
                }
            }
        }
    });
});
</script>
<script src="../js/new_sign_in.js"></script>
</body>
</html>
