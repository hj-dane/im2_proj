<!-- User Role Label -->
<span class="text-muted small role" style="color: black;font-weight: 500;font-size: 18px;">Admin/Seller</span>
<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
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



// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $productName = $_POST['product_name'] ?? '';
    $description = $_POST['product_description'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $category = $_POST['category'] ?? '';
    $color = $_POST['color'] ?? '';
    $size = $_POST['size'] ?? null;
    
    // ========== IMAGE UPLOAD HANDLING ========== //
    $imagePath = null; // Initialize as null if no image uploaded
    
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename to prevent conflicts
        $fileName = uniqid() . '_' . basename($_FILES['product_image']['name']);
        $targetPath = $uploadDir . $fileName;
        
        // Validate image file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['product_image']['tmp_name']);
        
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetPath)) {
                $imagePath = $targetPath; // Store relative path
            } else {
                // Handle upload error
                header('Location: inventorylist.php?added=0&error=upload_failed');
                exit;
            }
        } else {
            // Handle invalid file type
            header('Location: inventorylist.php?added=0&error=invalid_file_type');
            exit;
        }
    }
    // ========== END IMAGE UPLOAD ========== //
    
    // Validate required fields
    if (empty($productName)) {
        header('Location: inventorylist.php?added=0&error=name_required');
        exit;
    }
    
    if ($price <= 0) {
        header('Location: inventorylist.php?added=0&error=invalid_price');
        exit;
    }
    
    if (empty($category)) {
        header('Location: inventorylist.php?added=0&error=category_required');
        exit;
    }
    
    // Determine category ID
    $categoryId = ($category === 'Clothing') ? 1 : 2;
    
    try {
        // Insert into database with starting quantity = 20
        $stmt = $mysqli->prepare("
            INSERT INTO product_inventory 
            (product_name, product_description, unit_price, category_id, color, size, is_active, images, quantity) 
            VALUES (?, ?, ?, ?, ?, ?, 1, ?, 20)
        ");

        // Bind parameters (note: 7 placeholders, 7 parameters)
        $stmt->bind_param('ssdisss', $productName, $description, $price, $categoryId, $color, $size, $imagePath);

        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            header('Location: inventorylist.php?added=1');
            exit;
        } else {
            header('Location: inventorylist.php?added=0&error=db_error');
            exit;
        }
    } catch (Exception $e) {
        header('Location: inventorylist.php?added=0&error=db_exception');
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="../styles/warhus.css?v=5">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <title>REKTA | Add Product</title>
        <link rel="icon" type="image/x-icon" href="../assets/logo_stockflow.png">
        
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container-fluid">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center">
                            <a class="navbar-brand" href="../index.php">REKTA</a>
                            <ul class="nav nav-tabs border-0 ms-4">
                                <li class="nav-item"><a class="custom-nav-link" href="analytics.php"><i class="bi bi-speedometer2 fs-5"></i><span class="ms-2 d-none d-md-inline">DASHBOARD</span></a></li>
                                <li class="nav-item"><a class="custom-nav-link custom-active" href="warehouse.php"><i class="bi bi-plus-square fs-5"></i><span class="ms-2 d-none d-md-inline">ADD PRODUCT</span></a></li>
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
            <h1 class="row" style="padding-left: 58px;padding-bottom: 28px;"><b>ADD PRODUCT</b></h1>
            <hr>
            <div class="container mt-5">
                <form method="post" enctype="multipart/form-data">
                    <div class="prodcontainer">
                        <!-- First Column -->
                        <div class="column">
                            <div class="form-group">
                                <label for="product-id">Product ID (Auto-generated)</label>
                                <input type="text" id="product-id" placeholder="Will be auto-generated" disabled>
                            </div>
                            <div class="form-group">
                                <label for="product-name">Product Name *</label>
                                <input type="text" id="product-name" name="product_name" placeholder="Input Name" required>
                            </div>
                            <div class="form-group">
                                <label for="product-description">Product Description</label>
                                <textarea id="product-description" name="product_description" rows="4" placeholder="Input Description"></textarea>
                            </div>
                        </div>
                        <!-- Second Column -->
                        <div class="column">
                            <div class="form-group">
                                <label for="price">Price *</label>
                                <input type="number" id="price" name="price" placeholder="500" min="0" step="0.01" required> 
                            </div>
                            <div class="form-group">
                                <label for="category">Category *</label>
                                <select id="category" name="category" class="form-select" required>
                                    <option value="">Select Category</option>
                                    <option value="Clothing">Clothing</option>
                                    <option value="Accessories">Accessories</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="color">Color</label>
                                <input type="text" id="color" name="color" placeholder="Red/Yellow/Blue/Black">
                            </div>
                        </div>
                        <!-- Third Column (Rightmost) -->
                        <div class="column">
                            <div class="form-group">
                                <label for="size">Size</label>
                                <select id="size" name="size" class="form-select">
                                    <option value="">Select Size</option>
                                    <option value="Small">Small</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Large">Large</option>
                                    <option value="N/A">N/A</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Product Images</label>
                                <label for="product-image" style="cursor: pointer;">
                                    <div class="image-upload" id="imageUpload">
                                        <div id="imagePreview" style="width: 100px; height: 100px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                                            <span>Click to upload</span>
                                        </div>
                                        <span id="fileName">No image selected</span>
                                    </div>
                                </label>
                                <input type="file" id="product-image" name="product_image" style="display: none;" accept="image/*">
                            </div>
                            <button class="submit-btn" type="submit">Add Product</button>
                        </div>
                    </div>
                </form>
            </div>
        </main>

        <script>
        document.getElementById('product-image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Show filename
                document.getElementById('fileName').textContent = file.name;
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('imagePreview');
                    preview.innerHTML = `<img src="${event.target.result}" style="max-width: 100%; max-height: 100%;">`;
                };
                reader.readAsDataURL(file);
            }
        });
        </script>
        <script src="../js/new_sign_in.js"></script>

    </body>
</html> 