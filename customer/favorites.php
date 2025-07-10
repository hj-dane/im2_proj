<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>REKTA Cycling Favorites</title>
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
            padding: 2rem;
            flex: 1;
        }

        .main h1 {
            margin-bottom: 1rem;
        }

        .favorites {
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
            background: #c62828;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
        }

        .product-card button:hover {
            background: #a40000;
        }

        .no-favorites {
            text-align: center;
            color: #777;
            margin-top: 4rem;
            font-size: 1.2rem;
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
        <a href="#">Clothing</a>
        <a href="#">Accessories</a>
        <a href="#">Favorites</a>
    </nav>
</header>

<main class="main">
    <h1>Your Favorites</h1>

    <section class="favorites">
        <?php
        // Simulated favorite products (can be pulled from session/database later)
        $favorites = [
            ['name' => 'Cycling Cap', 'price' => '₱499', 'image' => 'https://via.placeholder.com/220x200?text=Cycling+Cap'],
            ['name' => 'Track Jacket', 'price' => '₱2,999', 'image' => 'https://via.placeholder.com/220x200?text=Jacket']
        ];

        if (empty($favorites)) {
            echo '<div class="no-favorites">You have no favorite items yet.</div>';
        } else {
            foreach ($favorites as $item) {
                echo '
                <div class="product-card">
                    <img src="' . $item['image'] . '" alt="' . $item['name'] . '">
                    <h3>' . $item['name'] . '</h3>
                    <p>' . $item['price'] . '</p>
                    <button>Remove from Favorites</button>
                </div>';
            }
        }
        ?>
    </section>
</main>

<footer class="footer">
    &copy; 2025 REKTA Cycling. All rights reserved.
</footer>

</body>
</html>
