<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>REKTA Cycling's Clothing | Adidas Inspired</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f8f8f8;
            color: #333;
        }

        .header {
            background-color: #000;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .nav a {
            color: white;
            text-decoration: none;
            margin: 0 1rem;
        }

        .main {
            display: flex;
            padding: 2rem;
            flex: 1;
        }

        .sidebar {
            width: 20%;
            padding: 1rem;
            background: white;
            border-radius: 8px;
            margin-right: 2rem;
        }

        .sidebar h2 {
            margin-bottom: 1rem;
        }

        .sidebar label {
            display: block;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }

        .sidebar select {
            width: 100%;
            padding: 0.5rem;
            border-radius: 5px;
        }

        .products {
            width: 80%;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 2rem;
        }

        .product-card {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        .product-card img {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
            margin-bottom: 1rem;
        }

        .product-card h3 {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .product-card p {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .product-card button {
            background: black;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
        }

        .product-card button:hover {
            background: #222;
        }

        .footer {
            text-align: center;
            padding: 1rem;
            background: #000;
            color: white;
        }
    </style>
</head>
<body>

<header class="header">
    <div class="logo">REKTA Cycling</div>
    <nav class="nav">
        <a href="#">Men</a>
        <a href="#">Women</a>
        <a href="#">Kids</a>
        <a href="#">Clothing</a>
        <a href="#">Shoes</a>
    </nav>
</header>

<main class="main">
    <aside class="sidebar">
        <h2>Filter</h2>
        <form>
            <label>Gender</label>
            <select>
                <option>All</option>
                <option>Men</option>
                <option>Women</option>
            </select>

            <label>Category</label>
            <select>
                <option>All</option>
                <option>T-Shirts</option>
                <option>Jackets</option>
                <option>Hoodies</option>
            </select>

            <label>Price Range</label>
            <select>
                <option>All</option>
                <option>₱500 - ₱999</option>
                <option>₱1,000 - ₱2,000</option>
                <option>₱2,000+</option>
            </select>
        </form>
    </aside>

    <section class="products">
        <?php
        // Static product array (backend/database can be added later)
        $products = [
            ['name' => 'T-Shirt', 'price' => '₱999', 'image' => 'https://via.placeholder.com/220x200?text=T-Shirt'],
            ['name' => 'Hoodie', 'price' => '₱2,199', 'image' => 'https://via.placeholder.com/220x200?text=Hoodie'],
            ['name' => 'Track Jacket', 'price' => '₱2,999', 'image' => 'https://via.placeholder.com/220x200?text=Jacket'],
            ['name' => 'Shorts', 'price' => '₱1,199', 'image' => 'https://via.placeholder.com/220x200?text=Shorts']
        ];

        foreach ($products as $product) {
            echo '
            <div class="product-card">
                <img src="' . $product['image'] . '" alt="' . $product['name'] . '">
                <h3>' . $product['name'] . '</h3>
                <p>' . $product['price'] . '</p>
                <button>Add to Cart</button>
            </div>';
        }
        ?>
    </section>
</main>

<footer class="footer">
    &copy; 2025 REKTA Cycling. All rights reserved.
</footer>

</body>
</html>