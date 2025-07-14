document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let totalPages = 1;
    let searchQuery = '';

    // Load orders
    function loadOrders(page = 1, search = '') {
        fetch(`orderlogs.php?action=get_orders&page=${page}&search=${encodeURIComponent(search)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
                currentPage = page;
                totalPages = data.pages;
                renderOrders(data.orders);
                updatePagination();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading orders: ' + error.message);
            });
    }

    // Process order actions
    function processOrder(orderId, action) {
        const actionMessages = {
            'complete': 'complete this order',
            'confirm': 'confirm this order',
            'cancel': 'cancel this order'
        };
        
        if (confirm(`Are you sure you want to ${actionMessages[action]}?`)) {
            fetch('orderlogs.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `order_id=${orderId}&action=${action}`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    loadOrders(currentPage, searchQuery);
                } else {
                    throw new Error(data.error || 'Failed to process order');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error processing order: ' + error.message);
            });
        }
    }

    // Render orders table
    function renderOrders(orders) {
        const tbody = document.getElementById('inventoryTable');
        tbody.innerHTML = '';

        if (orders.length === 0) {
            tbody.innerHTML = `<tr><td colspan="9" class="text-center">No orders found</td></tr>`;
            return;
        }

        orders.forEach(order => {
            const row = document.createElement('tr');
            
            // Determine badge color based on status
            const statusBadgeClass = {
                'Completed': 'bg-success',
                'Cancelled': 'bg-danger',
                'Preparing': 'bg-warning',
                'Pending': 'bg-secondary'
            }[order.status] || 'bg-secondary';
            
            row.innerHTML = `
                <td class="text-center">${order.trans_date}</td>
                <td class="text-center">${order.id}</td>
                <td class="text-center">${order.customer_name || 'N/A'}</td>
                <td class="text-center">₱${parseFloat(order.total_order_amount).toFixed(2)}</td>
                <td class="text-center">${order.payment_method || 'N/A'}</td>
                <td class="text-center">${order.payment_status}</td>
                <td class="text-center">${order.delivery_type || 'N/A'}</td>
                <td class="text-center">
                    <span class="badge ${statusBadgeClass}">${order.status || 'Pending'}</span>
                </td>
                <td class="text-center">
                    <a href="orderdetails.php?order_id=${order.id}" class="btn btn-sm btn-primary">View</a>
                    ${order.status === 'Pending' ? 
                        `<button class="btn btn-sm btn-success confirm-btn" data-order-id="${order.id}">Confirm</button>
                         <button class="btn btn-sm btn-danger cancel-btn" data-order-id="${order.id}">Cancel</button>` : ''}
                    ${order.status === 'Preparing' ? 
                        `<button class="btn btn-sm btn-success complete-btn" data-order-id="${order.id}">Complete</button>` : ''}
                </td>
            `;
            
            tbody.appendChild(row);
        });

        // Add event listeners for action buttons
        document.querySelectorAll('.confirm-btn').forEach(btn => {
            btn.addEventListener('click', () => processOrder(btn.dataset.orderId, 'confirm'));
        });

        document.querySelectorAll('.cancel-btn').forEach(btn => {
            btn.addEventListener('click', () => processOrder(btn.dataset.orderId, 'cancel'));
        });

        document.querySelectorAll('.complete-btn').forEach(btn => {
            btn.addEventListener('click', () => processOrder(btn.dataset.orderId, 'complete'));
        });
    }

    // Update pagination buttons
    function updatePagination() {
        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');

        prevBtn.classList.toggle('disabled', currentPage === 1);
        nextBtn.classList.toggle('disabled', currentPage >= totalPages);

        prevBtn.onclick = (e) => {
            e.preventDefault();
            if (currentPage > 1) loadOrders(currentPage - 1, searchQuery);
        };

        nextBtn.onclick = (e) => {
            e.preventDefault();
            if (currentPage < totalPages) loadOrders(currentPage + 1, searchQuery);
        };
    }

    // Search functionality with debounce
    let searchTimeout;
    document.getElementById('searchBox').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            searchQuery = this.value.trim();
            loadOrders(1, searchQuery);
        }, 300);
    });

    // Initial load
    loadOrders();
});