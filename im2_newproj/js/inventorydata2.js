document.addEventListener("DOMContentLoaded", function () {
    // DOM Elements
    const inventoryTable = document.getElementById("inventoryTable");
    const searchBox = document.getElementById("searchBox");
    const paginationButtons = document.getElementById("paginationButtons");
    let currentPage = 1;
    const itemsPerPage = 10;
    let allProducts = [];

    // Fetch archived products from PHP backend
    function fetchArchivedProducts() {
        fetch('delist.php?action=get_archived')
            .then(response => response.json())
                .then(data => {
                    allProducts = data;
                    populateTable();
                    updatePaginationButtons();
                })
                .catch(error => {
                    console.error('Error fetching products:', error);
            inventoryTable.innerHTML = `<tr><td colspan="8" class="text-center">Error loading products. Please try again.</td></tr>`;
       });
    }

    // Filter products based on search
    function getFilteredData() {
        const searchQuery = searchBox.value.toLowerCase();
        return allProducts.filter(product => {
            return product.product_name.toLowerCase().includes(searchQuery) ||
                   product.product_description.toLowerCase().includes(searchQuery) ||
                   product.color.toLowerCase().includes(searchQuery) ||
                   product.size.toLowerCase().includes(searchQuery);
        });
    }

    // Populate the table with products
    function populateTable() {
        inventoryTable.innerHTML = "";
        const filteredData = getFilteredData();
        const startIndex = (currentPage - 1) * itemsPerPage;
        const paginatedData = filteredData.slice(startIndex, startIndex + itemsPerPage);

        if (paginatedData.length === 0) {
            inventoryTable.innerHTML = `<tr><td colspan="8" class="text-center">No archived products found</td></tr>`;
            return;
        }

        paginatedData.forEach(product => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td class="text-center">${product.id}</td>
                <td>${escapeHtml(product.product_name)}</td>
                <td>${escapeHtml(product.product_description)}</td>
                <td class="text-center">${escapeHtml(product.color)}</td>
                <td class="text-center">${product.size || 'N/A'}</td>
                <td class="text-center">${product.quantity}</td>
                <td class="text-center">₱${product.unit_price.toFixed(2)}</td>
                <td class="text-center">
                    <button onclick="restoreProduct(${product.id})" class="btn btn-success btn-sm">Restore</button>
                </td>
            `;
            inventoryTable.appendChild(row);
        });
    }

    // Update pagination buttons
    function updatePaginationButtons() {
        const filteredData = getFilteredData();
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        paginationButtons.innerHTML = "";

        // Previous Button
        const prevBtn = document.createElement("li");
        prevBtn.classList.add("page-item", currentPage === 1 ? "disabled" : "");
        prevBtn.innerHTML = `<a class="page-link" href="#">Previous</a>`;
        prevBtn.addEventListener("click", (e) => {
            e.preventDefault();
            if (currentPage > 1) changePage("prev");
        });
        paginationButtons.appendChild(prevBtn);

        // Page Numbers
        const maxVisiblePages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }

        if (startPage > 1) {
            const firstPageBtn = document.createElement("li");
            firstPageBtn.classList.add("page-item");
            firstPageBtn.innerHTML = `<a class="page-link" href="#">1</a>`;
            firstPageBtn.addEventListener("click", (e) => {
                e.preventDefault();
                currentPage = 1;
                populateTable();
            });
            paginationButtons.appendChild(firstPageBtn);

            if (startPage > 2) {
                const ellipsis = document.createElement("li");
                ellipsis.classList.add("page-item", "disabled");
                ellipsis.innerHTML = `<span class="page-link">...</span>`;
                paginationButtons.appendChild(ellipsis);
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            const pageBtn = document.createElement("li");
            pageBtn.classList.add("page-item", i === currentPage ? "active" : "");
            pageBtn.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            pageBtn.addEventListener("click", (e) => {
                e.preventDefault();
                currentPage = i;
                populateTable();
            });
            paginationButtons.appendChild(pageBtn);
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const ellipsis = document.createElement("li");
                ellipsis.classList.add("page-item", "disabled");
                ellipsis.innerHTML = `<span class="page-link">...</span>`;
                paginationButtons.appendChild(ellipsis);
            }

            const lastPageBtn = document.createElement("li");
            lastPageBtn.classList.add("page-item");
            lastPageBtn.innerHTML = `<a class="page-link" href="#">${totalPages}</a>`;
            lastPageBtn.addEventListener("click", (e) => {
                e.preventDefault();
                currentPage = totalPages;
                populateTable();
            });
            paginationButtons.appendChild(lastPageBtn);
        }

        // Next Button
        const nextBtn = document.createElement("li");
        nextBtn.classList.add("page-item", currentPage === totalPages ? "disabled" : "");
        nextBtn.innerHTML = `<a class="page-link" href="#">Next</a>`;
        nextBtn.addEventListener("click", (e) => {
            e.preventDefault();
            if (currentPage < totalPages) changePage("next");
        });
        paginationButtons.appendChild(nextBtn);
    }

    // Change page
    function changePage(direction) {
        const filteredData = getFilteredData();
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        
        if (direction === "next" && currentPage < totalPages) {
            currentPage++;
        } else if (direction === "prev" && currentPage > 1) {
            currentPage--;
        }
        
        populateTable();
    }

    // Restore product function
    window.restoreProduct = function (id) {
        if (confirm('Are you sure you want to restore this product?')) {
            const formData = new FormData();
            formData.append('action', 'restore');
            formData.append('id', id);
                    
            fetch('delist.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
            })
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Product restored successfully!');
                    fetchArchivedProducts(); // Refresh the list
                } else {
                    throw new Error(data.message || 'Unknown error occurred');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', `Error: ${error.message}`);
            });
        }
    };

    // Helper function to show alerts
    function showAlert(type, message) {
        const alertDiv = document.createElement("div");
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.role = "alert";
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        const container = document.querySelector(".container2-fluid");
        container.prepend(alertDiv);
        
        setTimeout(() => {
            alertDiv.classList.remove("show");
            setTimeout(() => alertDiv.remove(), 150);
        }, 5000);
    }

    // Helper function to escape HTML
    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe.toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Event listeners
    searchBox.addEventListener("input", () => {
        currentPage = 1;
        populateTable();
        updatePaginationButtons();
    });

    // Initial load
    fetchArchivedProducts();
});