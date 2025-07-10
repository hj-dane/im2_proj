// GET PRODUCTS FROM DATABASE

document.addEventListener("DOMContentLoaded", function () {
    const inventoryTable = document.getElementById("inventoryTable");
    const paginationButtons = document.getElementById("paginationButtons");
    let currentPage = 1;
    const itemsPerPage = 10;

    window.toggleStar = function (id) {
        const item = inventoryData.find(item => item.id === id);
        if (item) {
            item.starred = !item.starred;
            populateTable();
        }
    };

    function changePage(direction) {
        const totalPages = Math.ceil(inventoryData.length / itemsPerPage);
        if (direction === "next" && currentPage < totalPages) {
            currentPage++;
        } else if (direction === "prev" && currentPage > 1) {
            currentPage--;
        }
        populateTable();
    }

    window.archiveProduct = function (id) {
        window.location.href = `delist.html?id=${id}`;
    };

    function updatePaginationButtons() {
        const filteredData = applyFilters(); 
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        
        paginationButtons.innerHTML = "";
        
        const prevBtn = document.createElement("li");
        prevBtn.classList.add("page-item");
        prevBtn.innerHTML = `<a class="page-link" href="#">Prev</a>`;
        prevBtn.addEventListener("click", () => changePage("prev"));
        paginationButtons.appendChild(prevBtn);
        
        for (let i = 1; i <= totalPages; i++) {
            const pageBtn = document.createElement("li");
            pageBtn.classList.add("page-item");
            if (i === currentPage) pageBtn.classList.add("active");
            pageBtn.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            pageBtn.addEventListener("click", () => {
                currentPage = i;
                populateTable();
            });
            paginationButtons.appendChild(pageBtn);
        }
        
        const nextBtn = document.createElement("li");
        nextBtn.classList.add("page-item");
        nextBtn.innerHTML = `<a class="page-link" href="#">Next</a>`;
        nextBtn.addEventListener("click", () => changePage("next"));
        paginationButtons.appendChild(nextBtn);
    }

    function getFilteredData() {
        const searchQuery = document.getElementById("searchBox").value.toLowerCase();
        const activeItems = inventoryData.filter(item => item.status === true); 
        
        return activeItems.filter(item => {
            return item.name.toLowerCase().includes(searchQuery) ||
                   item.desc.toLowerCase().includes(searchQuery) ||
                   item.location.toLowerCase().includes(searchQuery);
        });
    }

    function applyFilters() {
        const searchQuery = document.getElementById("searchBox").value.toLowerCase();
        const filterValue = document.getElementById("filterDropdown").value;
        
        const activeItems = inventoryData.filter(item => item.status === true);
        
        return activeItems.filter(item => {
            const matchesSearch = item.name.toLowerCase().includes(searchQuery) ||
                                item.desc.toLowerCase().includes(searchQuery) ||
                                item.location.toLowerCase().includes(searchQuery);
            
            const matchesStarFilter = filterValue === 'all' ||
                                    (filterValue === 'starred' && item.starred) ||
                                    (filterValue === 'unstarred' && !item.starred);
            
            return matchesSearch && matchesStarFilter;
        });
    }

    function populateTable() {
        inventoryTable.innerHTML = "";
        const filteredData = applyFilters();
        const startIndex = (currentPage - 1) * itemsPerPage;
        const paginatedData = filteredData.slice(startIndex, startIndex + itemsPerPage);

        paginatedData.forEach(item => {
            const row = document.createElement("tr");

            row.innerHTML = `
                <td class="text-center" onclick="toggleStar(${item.id})">${item.starred ? "⭐" : "☆"}</td>
                <td class="text-center">${item.id}</td>
                <td><a href="invdesc2.html?id=${item.id}" class="product-link">${item.name}</a></td>
                <td>${item.desc}</td>
                <td class="text-center">${item.qty}</td>
                <td class="text-center">${item.location}</td>
                <td class="text-center">
                    <a href="delist.html?id=${item.id}" class="btn btn-success btn-sm">Archive</a>
                </td>
            `;
            inventoryTable.appendChild(row);
        });

        updatePaginationButtons();
        updateSummary();
    }

    function updateLowStockList() {
        const lowStockList = document.getElementById("lowStockList");
        const lowStockItems = inventoryData.filter(item => item.status === true && item.qty < 10);
        
        if (lowStockItems.length === 0) {
            lowStockList.innerHTML = '<div class="alert alert-warning py-2">No low stock items</div>';
            return;
        }
        
        let listHTML = '<ul class="list-group">';
        
        lowStockItems.forEach(item => {
            listHTML += `
                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                    <div>
                        <strong>${item.name}</strong>
                        <div class="text-muted small">ID: ${item.id} | Qty: ${item.qty}</div>
                    </div>
                </li>
            `;
        });
        
        listHTML += '</ul>';
        lowStockList.innerHTML = listHTML;
    }
    
    function updateSummary() {
        const activeItems = inventoryData.filter(item => item.status === true);
        const totalProducts = activeItems.length;
        const lowStockCount = activeItems.filter(item => item.qty < 10).length;
        
        document.getElementById("totalProducts").textContent = totalProducts;
        document.getElementById("lowStock").textContent = lowStockCount;
        updateLowStockList(); 
    }

    document.getElementById("filterDropdown").addEventListener("change", () => {
        currentPage = 1;
        populateTable();
    });

    populateTable();
    updateSummary();
});


/*============================================================================================================================*/


document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const productId = parseInt(urlParams.get('id')); 

    console.log("Product ID from URL:", productId); 

    const product = inventoryData.find(item => item.id === productId);

    console.log("Found product:", product); 

    if (product) {
        const productDescSection = document.getElementById("productDescSection");
        productDescSection.innerHTML = `
            <h2><strong>${product.name}</strong></h2>
            <p>${product.desc}</p>
        `;

        const productDetailSection = document.getElementById("productDetailSection");
        productDetailSection.innerHTML = `
            <p><strong>Price:</strong> ${product.price}</p>
            <p><strong>Quantity:</strong> ${product.qty}</p>
            <p><strong>Location:</strong> ${product.location}</p>
            <p><strong>Category:</strong> ${product.category}</p>
        `;
        
        const inOutTable = document.getElementById("inOutTable").getElementsByTagName('tbody')[0];
        
        transactionData.forEach(transaction => {
            const row = inOutTable.insertRow();
            row.innerHTML = `
                <td>${transaction.requestDate}</td>
                <td>${transaction.approveDate}</td>
                <td>${transaction.borrowDate}</td>
                <td>${transaction.returnDate}</td>
            `;
        });
    } else {
        console.log("No product found with that ID."); 
        document.getElementById("productDescSection").innerHTML = "<p>Product not found.</p>";
        document.getElementById("productDetailSection").innerHTML = "";
    }
});


/*===================================================================================================================*/

document.addEventListener("DOMContentLoaded", function () {

    const transactionData = [
        { requestDate: '2025-04-01', approveDate: '2025-04-02', borrowDate: '2025-04-03', returnDate: '2025-04-04' },
        { requestDate: '2025-04-05', approveDate: '2025-04-06', borrowDate: '2025-04-07', returnDate: '2025-04-08' },
        { requestDate: '2025-04-10', approveDate: '2025-04-12', borrowDate: '2025-04-14', returnDate: '2025-04-16' }
    ];

    const inOutTableBody = document.getElementById("inOutTable").getElementsByTagName('tbody')[0];

    inOutTableBody.innerHTML = "";

    transactionData.forEach(transaction => {
        const row = inOutTableBody.insertRow();  

        const requestDateCell = row.insertCell(0);
        const approveDateCell = row.insertCell(1);
        const borrowDateCell = row.insertCell(2);
        const returnDateCell = row.insertCell(3);

        requestDateCell.textContent = transaction.requestDate;
        approveDateCell.textContent = transaction.approveDate;
        borrowDateCell.textContent = transaction.borrowDate;
        returnDateCell.textContent = transaction.returnDate;
    });
});




