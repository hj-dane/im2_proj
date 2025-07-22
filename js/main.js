// Real-time search filter
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const products = document.querySelectorAll(".product");

    searchInput.addEventListener("input", function () {
        const query = this.value.toLowerCase();
        products.forEach(product => {
            const name = product.getAttribute("data-name").toLowerCase();
            product.style.display = name.includes(query) ? "block" : "none";
        });
    });
});

function addToCart(productId) {
    alert("Product " + productId + " added to cart!");
}

function addToFavorites(productId) {
    alert("Product " + productId + " added to favorites!");
}


/* JavaScript for live search */
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("searchInput");
    const productCards = document.querySelectorAll(".product");

    searchInput.addEventListener("input", function() {
        const searchText = searchInput.value.toLowerCase();

        productCards.forEach(card => {
            const name = card.getAttribute("data-name").toLowerCase();
            if (name.includes(searchText)) {
                card.style.display = "block";
            } else {
                card.style.display = "none";
            }
        });
    });
});



document.querySelectorAll('.add-to-cart-form').forEach(form => {
    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const productId = this.getAttribute('data-product-id');
        const quantity = this.querySelector('input[name="quantity"]').value;

        const response = await fetch('add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        });

        const result = await response.json();
        if (result.success) {
            alert("✅ Product added to cart");
        } else {
            alert("❌ " + result.message);
        }
    });
});

document.querySelectorAll('.add-to-cart-form').forEach(form => {
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const productId = this.dataset.productId; // from <form data-product-id="">
        const quantity = this.querySelector('input[name="quantity"]').value;

        fetch('add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.success && data.cart_count !== undefined) {
                document.getElementById('cart-count').textContent = data.cart_count;
            }
        })
        .catch(err => {
            console.error('❌ JS error:', err);
            alert('❌ Failed to add to cart');
        });
    });
});



