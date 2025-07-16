<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';

$is_logged_in = isset($_SESSION['user_id']);
$cart_count = 0;


?>

<!-- Navbar -->
 <html>
<head>
<link rel="stylesheet" href="styles/navbar.css">
<style>
        .navbar { position: sticky; top: 0; z-index: 1030; }
        .navbar .icons a:hover i,
        .navbar .icons button:hover i { outline: 2px solid black; border-radius: 5px; }
        .sidebar { min-width: 250px; padding: 20px; border-right: 1px solid #ccc; }
        .product-list { flex-grow: 1; padding: 20px; }
        .search-bar { display: flex; align-items: center; gap: 8px; }
        .search-bar input[type="text"] { border: 1px solid #ccc; border-radius: 4px; padding: 5px 10px; }
        .search-bar i:hover { outline: 2px solid black; border-radius: 4px; cursor: pointer; }
        footer { background: #222; color: #fff; padding: 40px 0; margin-top: 50px; }
        footer h5 { color: #fff; }
</style>
</head>

<nav class="navbar navbar-expand-lg navbar-dark bg-black px-3">
    <a class="navbar-brand" href="index.php">REKTA</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link" href="order_history.php">Order History</a></li>
        </ul>
    </div>
</nav>

</html>