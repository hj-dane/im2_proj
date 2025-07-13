document.addEventListener("DOMContentLoaded", function() {
    // DOM Elements
    const inventoryTable = document.getElementById('inventoryTable');
    const searchBox = document.getElementById('searchBox');
    const filterDropdown = document.getElementById('filterDropdown');
    const paginationContainer = document.querySelector('.pagination');
    const totalProductsSpan = document.getElementById('totalProducts');
    const lowStockSpan = document.getElementById('lowStock');
    const lowStockList = document.getElementById('lowStockList');

    // State
    let currentData = {
        products: [],
        pagination: {},
        summary: {}
    };

    // Initialize
    init();

    function init() {
        loadData();
        setupEventListeners();
        addBlinkAnimation();
    }

    function setupEventListeners() {
        searchBox.addEventListener('input', debounce(() => loadData(), 300));
        filterDropdown.addEventListener('change', () => loadData());
    }

    async function loadData() {
        try {
            const params = new URLSearchParams({
                search: searchBox.value,
                filter: filterDropdown.value,
                page: 1 // Always reset to first page on filter/search
            });

            const response = await fetch(`api/get_inventory.php?${params}`);
            const data = await response.json();

            if (data.success) {
                currentData = data;
                renderTable();
                renderPagination();
                updateSummary();
            } else {
                throw new Error(data.message || 'Failed to load data');
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('danger', 'Error loading inventory data');
        }
    }

    function renderTable() {
        inventoryTable.innerHTML = currentData.products.map(product => `
            <tr>
                <td class="text-center">${product.id}</td>
                <td><a href="invdesc.php?id=${product.id}">${escapeHtml(product.product_name)}</a></td>
                <td>${escapeHtml(product.product_description)}</td>
                <td class="text-center">${escapeHtml(product.color)}</td>
                <td class="text-center">${product.size || 'N/A'}</td>
                <td class="text-center ${product.quantity < 10 ? 'text-danger blink' : ''}">
                    ${product.quantity}
                </td>
                <td class="text-center">₱${product.unit_price.toFixed(2)}</td>
                <td class="text-center">
                    <a href="delist.php?id=${product.id}" class="btn btn-success btn-sm">Archive</a>
                </td>
            </tr>
        `).join('');
    }

    function renderPagination() {
        const { currentPage, totalPages } = currentData.pagination;
        
        let html = `
            <li class="page-item ${currentPage <= 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}">Prev</a>
            </li>
        `;

        // Show limited page numbers (1 ... 4 5 [6] 7 8 ... 20)
        const maxVisiblePages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

        if (startPage > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
            if (startPage > 2) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }

        for (let i = startPage; i <= endPage; i++) {
            html += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
        }

        html += `
            <li class="page-item ${currentPage >= totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
            </li>
        `;

        paginationContainer.innerHTML = html;

        // Add click handlers
        paginationContainer.querySelectorAll('.page-link').forEach(link => {
            if (!link.parentElement.classList.contains('disabled')) {
                link.addEventListener('click', async (e) => {
                    e.preventDefault();
                    const page = parseInt(link.dataset.page);
                    await loadPage(page);
                });
            }
        });
    }

    async function loadPage(page) {
        try {
            const params = new URLSearchParams({
                search: searchBox.value,
                filter: filterDropdown.value,
                page: page
            });

            const response = await fetch(`api/get_inventory.php?${params}`);
            const data = await response.json();

            if (data.success) {
                currentData = data;
                renderTable();
                renderPagination();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('danger', 'Error loading page');
        }
    }

    function updateSummary() {
        totalProductsSpan.textContent = currentData.summary.totalProducts;
        lowStockSpan.textContent = currentData.summary.lowStockCount;

        // Update low stock list
        const lowStockItems = currentData.products.filter(p => p.quantity < 10);
        lowStockList.innerHTML = lowStockItems.length > 0 ? `
            <ul class="list-group">
                ${lowStockItems.map(item => `
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <div>
                            <strong>${escapeHtml(item.product_name)}</strong>
                            <div class="text-muted small">ID: ${item.id} | Qty: ${item.quantity}</div>
                        </div>
                    </li>
                `).join('')}
            </ul>
        ` : '<div class="alert alert-warning py-2">No low stock items</div>';
    }

    // Utility functions
    function debounce(func, timeout = 300) {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => { func.apply(this, args); }, timeout);
        };
    }

    function escapeHtml(text) {
        return text?.toString().replace(/[&<>"']/g, m => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;',
            '"': '&quot;', "'": '&#039;'
        }[m])) || '';
    }

    function showAlert(type, message) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show mt-3`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.querySelector('.container2-fluid').prepend(alert);
        setTimeout(() => alert.remove(), 5000);
    }

    function addBlinkAnimation() {
        if (!document.getElementById('blink-style')) {
            const style = document.createElement('style');
            style.id = 'blink-style';
            style.textContent = `
                @keyframes blink { 0% { opacity: 1; } 50% { opacity: 0.3; } 100% { opacity: 1; } }
                .blink { animation: blink 1.5s infinite; }
            `;
            document.head.appendChild(style);
        }
    }
});