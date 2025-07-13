document.addEventListener("DOMContentLoaded", function() {
    // DOM elements
    const submitBtn = document.getElementById('submit-product');
    const categoryItems = document.querySelectorAll('#categoryDropdown + .dropdown-menu .dropdown-item');
                const sizeItems = document.querySelectorAll('#sizeDropdown + .dropdown-menu .dropdown-item');
                const imageUpload = document.getElementById('imageUpload');
                const fileInput = document.getElementById('product-image');
                const alertContainer = document.getElementById('alert-container');
                
                let selectedCategory = '';
                let selectedSize = '';
                let imageFile = null;
                
                // Set up category dropdown
                categoryItems.forEach(item => {
                    item.addEventListener('click', function() {
                        selectedCategory = this.textContent;
                        document.getElementById('categoryDropdown').textContent = selectedCategory;
                    });
                });
                
                // Set up size dropdown
                sizeItems.forEach(item => {
                    item.addEventListener('click', function() {
                        selectedSize = this.textContent;
                        document.getElementById('sizeDropdown').textContent = selectedSize;
                    });
                });
                
                // Image upload handling
                imageUpload.addEventListener('click', function() {
                    fileInput.click();
                });
                
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        imageFile = this.files[0];
                        imageUpload.textContent = imageFile.name;
                    }
                });
                
                // Handle form submission
                submitBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Get form values
                    const productName = document.getElementById('product-name').value.trim();
                    const description = document.getElementById('product-description').value.trim();
                    const price = document.getElementById('price').value;
                    const quantity = document.getElementById('quantity').value;
                    const color = document.getElementById('color').value.trim();
                    
                    // Validate required fields
                    if (!productName) {
                        showAlert('danger', 'Product name is required');
                        return;
                    }
                    
                    if (!price || parseFloat(price) <= 0) {
                        showAlert('danger', 'Price must be greater than 0');
                        return;
                    }
                    
                    if (!quantity || parseInt(quantity) < 0) {
                        showAlert('danger', 'Quantity cannot be negative');
                        return;
                    }
                    
                    if (!selectedCategory) {
                        showAlert('danger', 'Category is required');
                        return;
                    }
                    
                    // Prepare form data
                    const formData = new FormData();
                    formData.append('product_name', productName);
                    formData.append('product_description', description);
                    formData.append('price', parseFloat(price).toFixed(2));
                    formData.append('category', selectedCategory);
                    formData.append('quantity', parseInt(quantity));
                    formData.append('color', color);
                    if (selectedSize && selectedSize !== 'N/A') formData.append('size', selectedSize);
                    if (imageFile) formData.append('product_image', imageFile);
                    
                    // Show loading state
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Adding...';
                    
                    // Send to server
                    fetch('warehouse.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            showAlert('success', data.message);
                            // Reset form
                            document.getElementById('product-name').value = '';
                            document.getElementById('product-description').value = '';
                            document.getElementById('price').value = '';
                            document.getElementById('quantity').value = '';
                            document.getElementById('color').value = '';
                            document.getElementById('categoryDropdown').textContent = 'Select Category';
                            document.getElementById('sizeDropdown').textContent = 'Select Size';
                            selectedCategory = '';
                            selectedSize = '';
                            imageFile = null;
                            imageUpload.textContent = 'Upload Image';
                            fileInput.value = '';
                            
                            // Optionally redirect after delay
                            setTimeout(() => {
                                window.location.href = 'inventorylist.html';
                            }, 1500);
                        } else {
                            throw new Error(data.message || 'Failed to add product');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('danger', error.message);
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Add Product';
                    });
                });
                
                // Helper function to show alerts
                function showAlert(type, message) {
                    const alertDiv = document.createElement('div');
                    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
                    alertDiv.innerHTML = `
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    
                    alertContainer.innerHTML = '';
                    alertContainer.appendChild(alertDiv);
                    
                    // Auto-dismiss after 5 seconds
                    setTimeout(() => {
                        const bsAlert = new bootstrap.Alert(alertDiv);
                        bsAlert.close();
                    }, 5000);
                }
            });