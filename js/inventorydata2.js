// GET PRODUCTS FROM DATABASE


document.addEventListener("DOMContentLoaded", function () {
    const inventoryTable = document.getElementById("inventoryTable");
    const paginationButtons = document.getElementById("paginationButtons");
    let currentPage = 1;
    const itemsPerPage = 10;

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

    window.archiveProduct = function (id) {
        window.location.href = `delist.html?id=${id}`;
    };

    function getFilteredData() {
        const searchQuery = document.getElementById("searchBox").value.toLowerCase();
        const archivedItems = inventoryData.filter(item => item.status === false);
        
        return archivedItems.filter(item => {
            return item.name.toLowerCase().includes(searchQuery) ||
                   item.desc.toLowerCase().includes(searchQuery) ||
                   item.location.toLowerCase().includes(searchQuery);
        });
    }

    function updatePaginationButtons() {
        const filteredData = getFilteredData();
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

    function populateTable() {
        inventoryTable.innerHTML = "";
        const filteredData = getFilteredData();
        const startIndex = (currentPage - 1) * itemsPerPage;
        const paginatedData = filteredData.slice(startIndex, startIndex + itemsPerPage);
        
        paginatedData.forEach(item => {
            const row = document.createElement("tr");
            
            row.innerHTML = `
                <td class="text-center">${item.id}</td>
                <td><a href="invdesc2.html?id=${item.id}" class="product-link">${item.name}</a></td>
                <td>${item.desc}</td>
                <td class="text-center">${item.qty}</td>
                <td class="text-center">${item.location}</td>
                <td class="text-center">${item.status ? 'Active' : 'Archived'}</td>
                <td class="text-center">
                    <button onclick="restoreProduct(${item.id})" class="btn btn-success btn-sm">Restore</button>
                </td>
            `;
            inventoryTable.appendChild(row);
        });

        updatePaginationButtons();
        updateSummary();
    }

    window.restoreProduct = function (id) {
        const item = inventoryData.find(item => item.id === id);
        if (item) {
            item.status = true; 
            populateTable(); 
        }
    };

    // Event listeners
    document.getElementById("searchBox").addEventListener("input", () => {
        currentPage = 1;
        populateTable();
    });

    populateTable();
    updateSummary();
});

function handleProductAction(productId) {
    const btn = document.getElementById(`productActionBtn_${productId}`);
    
    if (btn.textContent.trim() === 'Restore') {
      restoreProduct(productId);
      btn.textContent = 'Delete';
      btn.classList.remove('btn-success');
      btn.classList.add('btn-danger');
    } else {
      deleteProduct(productId);
      btn.textContent = 'Restore';
      btn.classList.remove('btn-danger');
      btn.classList.add('btn-success');
    }
  }

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






