// GET PRODUCTS FROM DATABASE

function generateColors(count) {
  return Array.from({length: count}, (_, i) => {
      const hue = (i * 360 / count) % 360;
      return `hsl(${hue}, 70%, 60%)`;
  });
}

function createCategoryChart() {
  const categoryColors = {
    technology: " #796984",
    food: "rgb(100, 86, 99)",
    clothing: " #5A4D6B",
    bags: "rgb(79, 50, 85)",
    hygiene: "rgb(170, 129, 160)",
    tools: "rgb(175, 148, 238)",
    gardening: "rgb(94, 77, 114)"
  };

  const categoryData = inventoryData.reduce((acc, item) => {
    acc[item.category] = (acc[item.category] || 0) + item.qty;
    return acc;
  }, {});

  const categories = Object.keys(categoryData);
  const quantities = Object.values(categoryData);

  const backgroundColors = categories.map(cat => categoryColors[cat] || "#CCCCCC");

  const ctx = document.getElementById('categoryChart').getContext('2d');
  new Chart(ctx, {
    type: 'pie',
    data: {
      labels: categories,
      datasets: [{
        data: quantities,
        backgroundColor: backgroundColors
      }]
    },
    options: getChartOptions('Quantity by Category')
  });
}

function createStatusChart() {
  const statusData = inventoryData.reduce((acc, item) => {
      const status = item.logs ? 'On Hand' : 'Borrowed';
      acc[status] = (acc[status] || 0) + item.qty;
      return acc;
  }, {});

  const ctx = document.getElementById('statusChart').getContext('2d');
  new Chart(ctx, {
      type: 'pie',
      data: {
          labels: Object.keys(statusData),
          datasets: [{
              data: Object.values(statusData),
              backgroundColor: ['#4CAF50', '#F44336']
          }]
      },
      options: getChartOptions('On Hand vs Borrowed')
  });
}

function getChartOptions(title) {
  return {
      responsive: true,
      plugins: {
          title: { display: true, text: title, font: { size: 16 } },
          tooltip: {
              callbacks: {
                  label: function(context) {
                      const total = context.dataset.data.reduce((a, b) => a + b, 0);
                      const percentage = Math.round(context.raw / total * 100);
                      return `${context.label}: ${context.raw} (${percentage}%)`;
                  }
              }
          }
      }
  };
}

function updateCardStats() {
  const totalProducts = inventoryData.length;
  const lowStockItems = inventoryData.filter(item => item.qty < item.threshold).length;
  const onHandItems = inventoryData.filter(item => item.logs === true).length;
  const borrowedItems = inventoryData.filter(item => item.logs === false).length;

  // Update the DOM
  document.getElementById('totalProducts').textContent = totalProducts;
  document.getElementById('lowStock').textContent = lowStockItems;
  document.getElementById('onHand').textContent = onHandItems;
  document.getElementById('borrowed').textContent = borrowedItems;
}

document.addEventListener('DOMContentLoaded', () => {
  createCategoryChart();
  createStatusChart();
  updateCardStats(); 
});

