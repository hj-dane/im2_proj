// analysischart.js

document.addEventListener('DOMContentLoaded', () => {
    try {
        // Check if inventoryData exists and is valid
        if (typeof inventoryData === 'undefined' || !Array.isArray(inventoryData)) {
            throw new Error('Inventory data is not available or invalid');
        }

        // Initialize chart and stats
        createCategoryChart();
        updateCardStats();

        // Make chart responsive to window resize
        window.addEventListener('resize', debounce(() => {
            createCategoryChart();
        }, 200));

    } catch (error) {
        console.error('Error initializing dashboard:', error);
        displayErrorToUser();
    }
});

// Utility function to limit how often a function can execute
function debounce(func, wait) {
    let timeout;
    return function() {
        const context = this, args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}

// Show error message to user
function displayErrorToUser() {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-danger';
    errorDiv.textContent = 'Failed to load dashboard data. Please try again later.';
    document.querySelector('.dashboard').prepend(errorDiv);
}

function createCategoryChart() {
    try {
        // Clear previous chart if it exists
        if (window.categoryChart instanceof Chart) {
            window.categoryChart.destroy();
        }

        // Map category IDs to names (from your category table)
        const categoryNames = {
            1: 'Clothing',
            2: 'Accessories'
        };

        // Calculate quantities by category
        const categoryData = inventoryData.reduce((acc, item) => {
            if (!item.category_id) return acc;
            const quantity = parseInt(item.quantity) || 0;
            const categoryName = categoryNames[item.category_id] || `Category ${item.category_id}`;
            acc[categoryName] = (acc[categoryName] || 0) + quantity;
            return acc;
        }, {});

        const categories = Object.keys(categoryData);
        const quantities = Object.values(categoryData);

        // Color scheme for categories
        const categoryColors = {
            'Clothing': '#796984',
            'Accessories': '#5A4D6B'
        };

        // Get colors for each category
        const backgroundColors = categories.map(cat => 
            categoryColors[cat] || `hsl(${Math.random() * 360}, 70%, 60%)`
        );

        // Get chart canvas
        const ctx = document.getElementById('categoryChart')?.getContext('2d');
        if (!ctx) throw new Error('Category chart canvas not found');

        // Create new chart
        window.categoryChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: categories,
                datasets: [{
                    data: quantities,
                    backgroundColor: backgroundColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 20
                        }
                    },
                    title: { 
                        display: true, 
                        text: 'Inventory by Category', 
                        font: { 
                            size: 16,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round(context.raw / total * 100);
                                return `${context.label}: ${context.raw} items (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

    } catch (error) {
        console.error('Error creating category chart:', error);
    }
}

function updateCardStats() {
    try {
        const totalProducts = inventoryData.length;
        
        const lowStockThreshold = 20;
        const lowStockItems = inventoryData.filter(item => {
            const qty = parseInt(item.quantity) || 0;
            return qty < lowStockThreshold;
        }).length;

        // Update the DOM elements safely
        const totalElement = document.getElementById('totalProducts');
        const lowStockElement = document.getElementById('lowStock');
        
        if (totalElement) totalElement.textContent = totalProducts;
        if (lowStockElement) lowStockElement.textContent = lowStockItems;

    } catch (error) {
        console.error('Error updating card stats:', error);
    }
}